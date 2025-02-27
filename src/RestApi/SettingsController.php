<?php
namespace PathOfSettings\RestApi;

use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use PathOfSettings\Core\Registries\PagesRegistry;
use PathOfSettings\Core\Services\SettingsManager;

class SettingsController extends WP_REST_Controller {
    /**
     * Settings manager
     */
    private SettingsManager $settingsManager;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->namespace = 'pos/v1';
        $this->rest_base = 'settings';
        $this->settingsManager = new SettingsManager();
    }
    
    /**
     * Register routes
     */
    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<page>[\w-]+)', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_items'],
                'permission_callback' => [$this, 'get_items_permissions_check'],
                'args' => [
                    'page' => [
                        'required' => true,
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_key',
                    ],
                ],
            ],
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'update_items'],
                'permission_callback' => [$this, 'update_items_permissions_check'],
                'args' => [
                    'page' => [
                        'required' => true,
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_key',
                    ],
                ],
            ],
        ]);
    }
    
    /**
     * Check permissions for reading settings
     * 
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function get_items_permissions_check($request) {
        $pageId = $request['page'];
        $pagesRegistry = PagesRegistry::getInstance();
        $page = $pagesRegistry->getPage($pageId);
        
        if (!$page) {
            return new WP_Error(
                'rest_page_invalid',
                __('Page not found.', 'path-of-settings'),
                ['status' => 404]
            );
        }
        
        return current_user_can($page->getCapability());
    }
    
    /**
     * Get settings for a page
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request) {
        $pageId = $request['page'];
        $settings = $this->settingsManager->getSettings($pageId);
        
        return rest_ensure_response($settings);
    }
    
    /**
     * Check permissions for updating settings
     * 
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function update_items_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }
    
    /**
     * Update settings for a page
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function update_items($request) {
        $pageId = $request['page'];
        $settings = $request->get_json_params();
        
        // Validate settings
        $pagesRegistry = PagesRegistry::getInstance();
        $page = $pagesRegistry->getPage($pageId);
        
        if (!$page) {
            return new WP_Error(
                'rest_page_invalid',
                __('Page not found.', 'path-of-settings'),
                ['status' => 404]
            );
        }
        
        // Validate and sanitize each field
        $validatedSettings = [];
        $errors = [];
        
        foreach ($page->getFields() as $field) {
            $fieldId = $field->getId();
            $value = $settings[$fieldId] ?? null;
            
            // Validate
            $validation = $field->validate($value);
            if (is_wp_error($validation)) {
                $errors[$fieldId] = $validation->get_error_message();
                continue;
            }
            
            // Sanitize
            $validatedSettings[$fieldId] = $field->sanitize($value);
        }
        
        if (!empty($errors)) {
            return new WP_Error(
                'rest_validation_error',
                __('Validation failed.', 'path-of-settings'),
                [
                    'status' => 400,
                    'errors' => $errors,
                ]
            );
        }
        
        // Save settings
        $result = $this->settingsManager->saveSettings($pageId, $validatedSettings);
        
        if (!$result) {
            return new WP_Error(
                'rest_update_failed',
                __('Failed to update settings.', 'path-of-settings'),
                ['status' => 500]
            );
        }
        
        return rest_ensure_response($validatedSettings);
    }
}