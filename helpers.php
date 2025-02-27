<?php
/**
 * Helper functions for Path Of Settings
 */

use PathOfSettings\Core\Registries\PagesRegistry;
use PathOfSettings\Core\Registries\FieldsRegistry;
use PathOfSettings\Core\Services\SettingsManager;

if (!function_exists('pos_register_page')) {
    /**
     * Register an options page
     * 
     * @param string $id Page ID
     * @param array $args Page arguments
     * @return \PathOfSettings\Core\Contracts\PageInterface
     */
    function pos_register_page(string $id, array $args = []) {
        $registry = PagesRegistry::getInstance();
        $pageClass = '\PathOfSettings\Core\Models\Page';
        $page = new $pageClass($id, $args);
        $registry->register($page);
        return $page;
    }
}

if (!function_exists('pos_add_field')) {
    /**
     * Add a field to a page
     * 
     * @param string $pageId Page ID
     * @param string $type Field type
     * @param string $id Field ID
     * @param array $args Field arguments
     * @return \PathOfSettings\Core\Contracts\FieldInterface
     */
    function pos_add_field(string $pageId, string $type, string $id, array $args = []) {
        $pagesRegistry = PagesRegistry::getInstance();
        $fieldsRegistry = FieldsRegistry::getInstance();
        
        $page = $pagesRegistry->getPage($pageId);
        if (!$page) {
            throw new \Exception("Page '{$pageId}' not found.");
        }
        
        $field = $fieldsRegistry->createField($type, $id, $args);
        $page->addField($field);
        
        return $field;
    }
}

if (!function_exists('pos_get_setting')) {
    /**
     * Get a setting value
     * 
     * @param string $pageId Page ID
     * @param string $fieldId Field ID
     * @param mixed $default Default value
     * @return mixed
     */
    function pos_get_setting(string $pageId, string $fieldId, $default = null) {
        $settingsManager = new SettingsManager();
        return $settingsManager->getSetting($pageId, $fieldId, $default);
    }
}

if (!function_exists('pos_get_settings')) {
    /**
     * Get all settings for a page
     * 
     * @param string $pageId Page ID
     * @return array
     */
    function pos_get_settings(string $pageId) {
        $settingsManager = new SettingsManager();
        return $settingsManager->getSettings($pageId);
    }
}