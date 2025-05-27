<?php

namespace PathOfSettings\RestApi;

use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use PathOfSettings\Core\Registries\PagesRegistry;
use PathOfSettings\Core\Services\SettingsManager;

/**
 * REST API controller for settings management.
 *
 * Provides REST API endpoints for retrieving and updating settings data.
 * Handles authentication, validation, and data processing for settings operations.
 *
 * @package PathOfSettings\RestApi
 * @since 1.0.0
 */
class SettingsController extends WP_REST_Controller {

	/**
	 * Settings management service.
	 *
	 * @since 1.0.0
	 * @var SettingsManager
	 */
	private SettingsManager $settingsManager;

	/**
	 * Initialize the REST controller.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->namespace       = 'pos/v1';
		$this->rest_base       = 'settings';
		$this->settingsManager = new SettingsManager();
	}

	/**
	 * Register REST API routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<page>[\w-]+)',
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
					'args'                => [
						'page' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_key',
							'description'       => 'Page identifier for settings retrieval.',
						],
					],
				],
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_items' ],
					'permission_callback' => [ $this, 'update_items_permissions_check' ],
					'args'                => [
						'page' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_key',
							'description'       => 'Page identifier for settings update.',
						],
					],
				],
			]
		);
	}

	/**
	 * Check permissions for reading settings.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request Request object
	 * @return bool|WP_Error True if user can read settings, WP_Error otherwise
	 */
	public function get_items_permissions_check( $request ) {
		$pageId        = $request['page'];
		$pagesRegistry = PagesRegistry::getInstance();
		$page          = $pagesRegistry->getPage( $pageId );

		if ( ! $page ) {
			return new WP_Error(
				'rest_page_invalid',
				__( 'Settings page not found.', 'path-of-settings' ),
				[ 'status' => 404 ]
			);
		}

		if ( ! current_user_can( $page->getCapability() ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to access this settings page.', 'path-of-settings' ),
				[ 'status' => 403 ]
			);
		}

		return true;
	}

	/**
	 * Retrieve settings for a specific page.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request Request object
	 * @return WP_REST_Response|WP_Error Response object or error
	 */
	public function get_items( $request ) {
		$pageId   = $request['page'];
		$settings = $this->settingsManager->getSettings( $pageId );

		return rest_ensure_response( $settings );
	}

	/**
	 * Check permissions for updating settings.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request Request object
	 * @return bool|WP_Error True if user can update settings, WP_Error otherwise
	 */
	public function update_items_permissions_check( $request ) {
		return $this->get_items_permissions_check( $request );
	}

	/**
	 * Update settings for a specific page.
	 *
	 * Validates and sanitizes all field data before saving.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request Request object
	 * @return WP_REST_Response|WP_Error Response object or error
	 */
	public function update_items( $request ) {
		$pageId   = $request['page'];
		$settings = $request->get_json_params();

		if ( ! is_array( $settings ) ) {
			return new WP_Error(
				'rest_invalid_data',
				__( 'Invalid settings data provided.', 'path-of-settings' ),
				[ 'status' => 400 ]
			);
		}

		$pagesRegistry = PagesRegistry::getInstance();
		$page          = $pagesRegistry->getPage( $pageId );

		if ( ! $page ) {
			return new WP_Error(
				'rest_page_invalid',
				__( 'Settings page not found.', 'path-of-settings' ),
				[ 'status' => 404 ]
			);
		}

		$validatedSettings = [];
		$errors            = [];

		foreach ( $page->getFields() as $field ) {
			$fieldId = $field->getId();
			$value   = $settings[ $fieldId ] ?? null;

			$validation = $field->validate( $value );
			if ( is_wp_error( $validation ) ) {
				$errors[ $fieldId ] = $validation->get_error_message();
				continue;
			}

			$validatedSettings[ $fieldId ] = $field->sanitize( $value );
		}

		if ( ! empty( $errors ) ) {
			return new WP_Error(
				'rest_validation_error',
				__( 'Validation failed for one or more fields.', 'path-of-settings' ),
				[
					'status' => 400,
					'errors' => $errors,
				]
			);
		}

		$result = $this->settingsManager->saveSettings( $pageId, $validatedSettings );

		if ( ! $result ) {
			return new WP_Error(
				'rest_update_failed',
				__( 'Failed to save settings. Please try again.', 'path-of-settings' ),
				[ 'status' => 500 ]
			);
		}

		return rest_ensure_response( $validatedSettings );
	}
}
