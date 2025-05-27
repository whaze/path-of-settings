<?php
/**
 * Helper functions for PathOfSettings package.
 *
 * Provides a procedural API for common PathOfSettings operations.
 * These functions offer a simple interface for registering pages,
 * adding fields, and retrieving settings data.
 *
 * @package PathOfSettings
 * @since 1.0.0
 */

use PathOfSettings\Core\Registries\PagesRegistry;
use PathOfSettings\Core\Registries\FieldsRegistry;
use PathOfSettings\Core\Services\SettingsManager;

if ( ! function_exists( 'pos_register_page' ) ) {
	/**
	 * Register a new settings page.
	 *
	 * @since 1.0.0
	 * @param string $id Unique page identifier
	 * @param array  $args Page configuration options
	 * @return \PathOfSettings\Core\Contracts\PageInterface The registered page object
	 * @throws \Exception If page registration fails
	 */
	function pos_register_page( string $id, array $args = [] ) {
		$registry  = PagesRegistry::getInstance();
		$pageClass = '\PathOfSettings\Core\Models\Page';
		$page      = new $pageClass( $id, $args );
		$registry->register( $page );
		return $page;
	}
}

if ( ! function_exists( 'pos_add_field' ) ) {
	/**
	 * Add a field to an existing settings page.
	 *
	 * @since 1.0.0
	 * @param string $pageId Page identifier
	 * @param string $type Field type (text, textarea, select, checkbox)
	 * @param string $id Unique field identifier
	 * @param array  $args Field configuration options
	 * @return \PathOfSettings\Core\Contracts\FieldInterface The created field object
	 * @throws \Exception If page is not found or field creation fails
	 */
	function pos_add_field( string $pageId, string $type, string $id, array $args = [] ) {
		$pagesRegistry  = PagesRegistry::getInstance();
		$fieldsRegistry = FieldsRegistry::getInstance();

		$page = $pagesRegistry->getPage( $pageId );
		if ( ! $page ) {
			throw new \Exception( "Settings page '{$pageId}' not found. Please register the page first using pos_register_page()." );
		}

		$field = $fieldsRegistry->createField( $type, $id, $args );
		$page->addField( $field );

		return $field;
	}
}

if ( ! function_exists( 'pos_get_setting' ) ) {
	/**
	 * Get a specific setting value.
	 *
	 * @since 1.0.0
	 * @param string $pageId Page identifier
	 * @param string $fieldId Field identifier
	 * @param mixed  $default Default value to return if setting doesn't exist
	 * @return mixed The setting value or default value
	 */
	function pos_get_setting( string $pageId, string $fieldId, $default = null ) {
		$settingsManager = new SettingsManager();
		return $settingsManager->getSetting( $pageId, $fieldId, $default );
	}
}

if ( ! function_exists( 'pos_get_settings' ) ) {
	/**
	 * Get all settings for a specific page.
	 *
	 * @since 1.0.0
	 * @param string $pageId Page identifier
	 * @return array All settings for the page as associative array
	 */
	function pos_get_settings( string $pageId ) {
		$settingsManager = new SettingsManager();
		return $settingsManager->getSettings( $pageId );
	}
}

if ( ! function_exists( 'pos_has_settings' ) ) {
	/**
	 * Check if settings exist for a specific page.
	 *
	 * @since 1.0.0
	 * @param string $pageId Page identifier
	 * @return bool True if settings exist for the page
	 */
	function pos_has_settings( string $pageId ) {
		$settingsManager = new SettingsManager();
		return $settingsManager->hasSettings( $pageId );
	}
}

if ( ! function_exists( 'pos_delete_settings' ) ) {
	/**
	 * Delete all settings for a specific page.
	 *
	 * @since 1.0.0
	 * @param string $pageId Page identifier
	 * @return bool True if settings were deleted successfully
	 */
	function pos_delete_settings( string $pageId ) {
		$settingsManager = new SettingsManager();
		return $settingsManager->deleteSettings( $pageId );
	}
}

if ( ! function_exists( 'pos_get_registered_pages' ) ) {
	/**
	 * Get all registered settings pages.
	 *
	 * @since 1.0.0
	 * @return array Array of PageInterface objects indexed by page ID
	 */
	function pos_get_registered_pages() {
		$registry = PagesRegistry::getInstance();
		return $registry->getPages();
	}
}

if ( ! function_exists( 'pos_get_registered_field_types' ) ) {
	/**
	 * Get all registered field types.
	 *
	 * @since 1.0.0
	 * @return array Field type => class name mapping
	 */
	function pos_get_registered_field_types() {
		$registry = FieldsRegistry::getInstance();
		return $registry->getFields();
	}
}
