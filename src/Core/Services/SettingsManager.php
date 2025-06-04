<?php

namespace PathOfSettings\Core\Services;

/**
 * Service for managing settings data storage and retrieval.
 *
 * Handles all interactions with WordPress options API for PathOfSettings.
 * Provides methods for saving, retrieving, and managing settings data
 * with proper filtering and action hooks.
 *
 * @package PathOfSettings\Core\Services
 * @since 1.0.0
 */
class SettingsManager {

	/**
	 * Option name prefix for PathOfSettings.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const OPTION_PREFIX = 'pos_settings_';

	/**
	 * Get all settings for a specific page.
	 *
	 * @since 1.0.0
	 * @param string $pageId Page identifier
	 * @return array Settings data as associative array
	 */
	public function getSettings( string $pageId ): array {
		$optionName = $this->getOptionName( $pageId );
		$settings = get_option( $optionName, [] );
		
		// Récupérer la page pour avoir accès aux champs
		$pagesRegistry = \PathOfSettings\Core\Registries\PagesRegistry::getInstance();
		$page = $pagesRegistry->getPage( $pageId );
		
		if ( $page ) {
			// Pour chaque champ, définir la valeur depuis les settings
			foreach ( $page->getFields() as $field ) {
				$fieldId = $field->getId();
				if ( isset( $settings[ $fieldId ] ) ) {
					$field->setValue( $settings[ $fieldId ] );
				}
			}
		}
		
		return $settings;
	}

	/**
	 * Get a specific setting value.
	 *
	 * @since 1.0.0
	 * @param string $pageId Page identifier
	 * @param string $fieldId Field identifier
	 * @param mixed  $default Default value to return if setting doesn't exist
	 * @return mixed The setting value or default value
	 */
	public function getSetting( string $pageId, string $fieldId, $default = null ) {
		$settings = $this->getSettings( $pageId );
		return $settings[ $fieldId ] ?? $default;
	}

	/**
	 * Save settings for a specific page.
	 *
	 * Applies filters before saving and triggers actions after successful save.
	 *
	 * @since 1.0.0
	 * @param string $pageId Page identifier
	 * @param array  $settings Settings data to save
	 * @return bool True if settings were saved successfully
	 */
	public function saveSettings( string $pageId, array $settings ): bool {
		$optionName = $this->getOptionName( $pageId );

		/**
		 * Filter settings before saving.
		 *
		 * @since 1.0.0
		 * @param array $settings The settings data
		 * @param string $pageId The page identifier
		 */
		$settings = apply_filters( 'pos_before_save_settings', $settings, $pageId );

		$result = update_option( $optionName, $settings );

		if ( $result ) {
			/**
			 * Action fired after settings are successfully saved.
			 *
			 * @since 1.0.0
			 * @param array $settings The saved settings data
			 * @param string $pageId The page identifier
			 */
			do_action( 'pos_after_save_settings', $settings, $pageId );
		}

		return $result;
	}

	/**
	 * Delete all settings for a specific page.
	 *
	 * @since 1.0.0
	 * @param string $pageId Page identifier
	 * @return bool True if settings were deleted successfully
	 */
	public function deleteSettings( string $pageId ): bool {
		$optionName = $this->getOptionName( $pageId );
		return delete_option( $optionName );
	}

	/**
	 * Check if settings exist for a specific page.
	 *
	 * @since 1.0.0
	 * @param string $pageId Page identifier
	 * @return bool True if settings exist for the page
	 */
	public function hasSettings( string $pageId ): bool {
		$optionName = $this->getOptionName( $pageId );
		return get_option( $optionName ) !== false;
	}

	/**
	 * Generate the WordPress option name for a page.
	 *
	 * @since 1.0.0
	 * @param string $pageId Page identifier
	 * @return string The WordPress option name
	 */
	private function getOptionName( string $pageId ): string {
		return self::OPTION_PREFIX . $pageId;
	}
}
