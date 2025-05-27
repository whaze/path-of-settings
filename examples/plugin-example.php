<?php
/**
 * Plugin Name: PathOfSettings Example Plugin
 * Plugin URI: https://github.com/whaze/path-of-settings
 * Description: Example implementation of PathOfSettings package in a WordPress plugin
 * Version: 1.0.0
 * Author: Jerome Buquet
 * Requires PHP: 7.4
 * Text Domain: pos-example-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Example plugin demonstrating PathOfSettings usage.
 *
 * Shows how to integrate PathOfSettings package into a WordPress plugin,
 * including proper initialization, error handling, and settings registration.
 *
 * @package PathOfSettings\Examples
 * @since 1.0.0
 */
class PathOfSettingsExamplePlugin {

	/**
	 * Singleton instance.
	 *
	 * @since 1.0.0
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @since 1.0.0
	 * @return self
	 */
	public static function getInstance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the example plugin.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Initialize plugin components and dependencies.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {
		if ( ! $this->loadDependencies() ) {
			return;
		}

		$this->initPathOfSettings();
		add_action( 'pos_register_pages', [ $this, 'registerSettingsPages' ] );
	}

	/**
	 * Load required dependencies and check for PathOfSettings package.
	 *
	 * @since 1.0.0
	 * @return bool True if dependencies loaded successfully
	 */
	private function loadDependencies(): bool {
		if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			add_action( 'admin_notices', [ $this, 'showComposerNotice' ] );
			return false;
		}

		require_once __DIR__ . '/vendor/autoload.php';

		if ( ! class_exists( '\PathOfSettings\PathOfSettings' ) ) {
			add_action( 'admin_notices', [ $this, 'showPackageNotice' ] );
			return false;
		}

		return true;
	}

	/**
	 * Initialize PathOfSettings package.
	 *
	 * @since 1.0.0
	 */
	private function initPathOfSettings(): void {
		\PathOfSettings\PathOfSettings::getInstance()->init(
			[
				'version' => '1.0.0',
				'path'    => plugin_dir_path( __FILE__ ),
				'url'     => plugin_dir_url( __FILE__ ),
				'file'    => __FILE__,
			]
		);
	}

	/**
	 * Display notice when Composer dependencies are missing.
	 *
	 * @since 1.0.0
	 */
	public function showComposerNotice(): void {
		echo '<div class="notice notice-error"><p>';
		echo esc_html__( 'PathOfSettings Example Plugin: Please run "composer install" in the plugin directory.', 'pos-example-plugin' );
		echo '</p></div>';
	}

	/**
	 * Display notice when PathOfSettings package is missing.
	 *
	 * @since 1.0.0
	 */
	public function showPackageNotice(): void {
		echo '<div class="notice notice-error"><p>';
		echo esc_html__( 'PathOfSettings Example Plugin: The whaze/path-of-settings package is not installed. Run "composer require whaze/path-of-settings".', 'pos-example-plugin' );
		echo '</p></div>';
	}

	/**
	 * Register example settings pages and fields.
	 *
	 * Demonstrates various field types and configuration options.
	 *
	 * @since 1.0.0
	 */
	public function registerSettingsPages(): void {
		$this->registerGeneralSettings();
		$this->registerAdvancedSettings();
	}

	/**
	 * Register general settings page.
	 *
	 * @since 1.0.0
	 */
	private function registerGeneralSettings(): void {
		pos_register_page(
			'example-general',
			[
				'title'      => __( 'General Settings', 'pos-example-plugin' ),
				'menu_title' => __( 'Example Settings', 'pos-example-plugin' ),
				'capability' => 'manage_options',
			]
		);

		pos_add_field(
			'example-general',
			'text',
			'site_name',
			[
				'label'       => __( 'Site Name', 'pos-example-plugin' ),
				'description' => __( 'Enter your site name for branding purposes', 'pos-example-plugin' ),
				'default'     => get_bloginfo( 'name' ),
				'required'    => true,
				'placeholder' => __( 'My Awesome Site', 'pos-example-plugin' ),
			]
		);

		pos_add_field(
			'example-general',
			'textarea',
			'site_description',
			[
				'label'       => __( 'Site Description', 'pos-example-plugin' ),
				'description' => __( 'Provide a brief description of your website', 'pos-example-plugin' ),
				'default'     => get_bloginfo( 'description' ),
				'placeholder' => __( 'A fantastic WordPress website...', 'pos-example-plugin' ),
				'rows'        => 4,
			]
		);

		pos_add_field(
			'example-general',
			'select',
			'theme_style',
			[
				'label'       => __( 'Theme Style', 'pos-example-plugin' ),
				'description' => __( 'Choose the visual style for your site', 'pos-example-plugin' ),
				'default'     => 'modern',
				'options'     => [
					'classic' => __( 'Classic', 'pos-example-plugin' ),
					'modern'  => __( 'Modern', 'pos-example-plugin' ),
					'minimal' => __( 'Minimal', 'pos-example-plugin' ),
					'bold'    => __( 'Bold', 'pos-example-plugin' ),
				],
			]
		);

		pos_add_field(
			'example-general',
			'checkbox',
			'enable_features',
			[
				'label'       => __( 'Enable Advanced Features', 'pos-example-plugin' ),
				'description' => __( 'Check to enable advanced functionality', 'pos-example-plugin' ),
				'default'     => false,
			]
		);

		pos_add_field(
			'example-general',
			'text',
			'api_key',
			[
				'label'       => __( 'API Key', 'pos-example-plugin' ),
				'description' => __( 'Enter your API key for external services', 'pos-example-plugin' ),
				'placeholder' => __( 'sk-...', 'pos-example-plugin' ),
			]
		);
	}

	/**
	 * Register advanced settings page.
	 *
	 * @since 1.0.0
	 */
	private function registerAdvancedSettings(): void {
		pos_register_page(
			'example-advanced',
			[
				'title'      => __( 'Advanced Settings', 'pos-example-plugin' ),
				'menu_title' => __( 'Advanced', 'pos-example-plugin' ),
				'capability' => 'manage_options',
			]
		);

		pos_add_field(
			'example-advanced',
			'select',
			'cache_duration',
			[
				'label'       => __( 'Cache Duration', 'pos-example-plugin' ),
				'description' => __( 'Select how long to cache data', 'pos-example-plugin' ),
				'default'     => '3600',
				'options'     => [
					'300'   => __( '5 minutes', 'pos-example-plugin' ),
					'1800'  => __( '30 minutes', 'pos-example-plugin' ),
					'3600'  => __( '1 hour', 'pos-example-plugin' ),
					'86400' => __( '24 hours', 'pos-example-plugin' ),
				],
			]
		);

		pos_add_field(
			'example-advanced',
			'checkbox',
			'debug_mode',
			[
				'label'       => __( 'Debug Mode', 'pos-example-plugin' ),
				'description' => __( 'Enable debug mode (not recommended for production)', 'pos-example-plugin' ),
				'default'     => false,
			]
		);
	}
}

PathOfSettingsExamplePlugin::getInstance();

/**
 * Utility functions for retrieving example settings.
 */

/**
 * Get the configured site name.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Site name
 */
function pos_example_get_site_name( string $default = '' ): string {
	return pos_get_setting( 'example-general', 'site_name', $default );
}

/**
 * Get the configured site description.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Site description
 */
function pos_example_get_site_description( string $default = '' ): string {
	return pos_get_setting( 'example-general', 'site_description', $default );
}

/**
 * Get the selected theme style.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Theme style
 */
function pos_example_get_theme_style( string $default = 'modern' ): string {
	return pos_get_setting( 'example-general', 'theme_style', $default );
}

/**
 * Check if advanced features are enabled.
 *
 * @since 1.0.0
 * @return bool True if advanced features are enabled
 */
function pos_example_is_features_enabled(): bool {
	return (bool) pos_get_setting( 'example-general', 'enable_features', false );
}

/**
 * Get the API key.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string API key
 */
function pos_example_get_api_key( string $default = '' ): string {
	return pos_get_setting( 'example-general', 'api_key', $default );
}

/**
 * Get the cache duration in seconds.
 *
 * @since 1.0.0
 * @param int $default Default value if setting not found
 * @return int Cache duration in seconds
 */
function pos_example_get_cache_duration( int $default = 3600 ): int {
	return (int) pos_get_setting( 'example-advanced', 'cache_duration', $default );
}

/**
 * Check if debug mode is enabled.
 *
 * @since 1.0.0
 * @return bool True if debug mode is enabled
 */
function pos_example_is_debug_enabled(): bool {
	return (bool) pos_get_setting( 'example-advanced', 'debug_mode', false );
}
