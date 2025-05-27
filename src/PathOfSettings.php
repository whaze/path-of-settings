<?php

namespace PathOfSettings;

use PathOfSettings\Core\Registries\FieldsRegistry;
use PathOfSettings\Core\Registries\PagesRegistry;
use PathOfSettings\RestApi\SettingsController;

/**
 * Main PathOfSettings package class.
 *
 * Entry point for the PathOfSettings package. Handles initialization,
 * WordPress integration, and coordinates all package components.
 * Uses singleton pattern to ensure single instance across the application.
 *
 * @package PathOfSettings
 * @since 1.0.0
 */
class PathOfSettings {

	/**
	 * Singleton instance.
	 *
	 * @since 1.0.0
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Package initialization status.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	private static $initialized = false;

	/**
	 * Get the singleton instance.
	 *
	 * @since 1.0.0
	 * @return self The package instance
	 */
	public static function getInstance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Private constructor to enforce singleton pattern.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Prevent direct instantiation
	}

	/**
	 * Initialize the PathOfSettings package.
	 *
	 * Sets up constants, registers WordPress hooks, and initializes core components.
	 * Can be called multiple times safely - will only initialize once.
	 *
	 * @since 1.0.0
	 * @param array $config Package configuration options
	 */
	public function init( array $config = [] ): void {
		if ( self::$initialized ) {
			return;
		}

		$this->defineConstants( $config );
		$this->initHooks();

		self::$initialized = true;
	}

	/**
	 * Define package constants from configuration.
	 *
	 * @since 1.0.0
	 * @param array $config Configuration array
	 */
	private function defineConstants( array $config ): void {
		if ( ! defined( 'POS_VERSION' ) ) {
			define( 'POS_VERSION', $config['version'] ?? '1.0.0' );
		}

		if ( ! defined( 'POS_PATH' ) ) {
			define( 'POS_PATH', $config['path'] ?? '' );
		}

		if ( ! defined( 'POS_URL' ) ) {
			define( 'POS_URL', $config['url'] ?? '' );
		}

		if ( ! defined( 'POS_FILE' ) ) {
			define( 'POS_FILE', $config['file'] ?? '' );
		}
	}

	/**
	 * Initialize WordPress hooks and actions.
	 *
	 * @since 1.0.0
	 */
	private function initHooks(): void {
		add_action( 'plugins_loaded', [ $this, 'loadTextdomain' ] );
		add_action( 'init', [ $this, 'initRegistries' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'registerAssets' ] );
		add_action( 'rest_api_init', [ $this, 'registerRestRoutes' ] );
	}

	/**
	 * Load translation files for internationalization.
	 *
	 * @since 1.0.0
	 */
	public function loadTextdomain(): void {
		if ( defined( 'POS_PATH' ) && POS_PATH ) {
			load_plugin_textdomain(
				'path-of-settings',
				false,
				basename( POS_PATH ) . '/languages'
			);
		}
	}

	/**
	 * Initialize core registries and trigger page registration.
	 *
	 * @since 1.0.0
	 */
	public function initRegistries(): void {
		PagesRegistry::getInstance();
		FieldsRegistry::getInstance();

		/**
		 * Action hook for registering settings pages.
		 *
		 * @since 1.0.0
		 */
		do_action( 'pos_register_pages' );
	}

	/**
	 * Register and enqueue admin assets when needed.
	 *
	 * @since 1.0.0
	 * @param string $hook Current WordPress admin page hook
	 */
	public function registerAssets( string $hook ): void {
		if ( ! $this->isOptionsPage( $hook ) ) {
			return;
		}

		$this->enqueueAssets();
	}

	/**
	 * Enqueue JavaScript and CSS assets for the admin interface.
	 *
	 * @since 1.0.0
	 */
	private function enqueueAssets(): void {
		if ( ! defined( 'POS_PATH' ) || ! defined( 'POS_URL' ) || ! POS_PATH || ! POS_URL ) {
			return;
		}

		$assetFile = POS_PATH . 'build/index.asset.php';
		$jsFile    = POS_PATH . 'build/index.js';

		// Check if build files exist
		if ( ! file_exists( $assetFile ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PathOfSettings: Asset file not found at ' . $assetFile . '. Please run "npm run build".' );
			}
			return;
		}

		if ( ! file_exists( $jsFile ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PathOfSettings: JavaScript file not found at ' . $jsFile . '. Please run "npm run build".' );
			}
			return;
		}

		$assets = include $assetFile;

		// Validate asset file structure
		if ( ! is_array( $assets ) || ! isset( $assets['dependencies'] ) || ! isset( $assets['version'] ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PathOfSettings: Invalid asset file structure. Please rebuild assets with "npm run build".' );
			}
			return;
		}

		wp_register_script(
			'pos-admin',
			POS_URL . 'build/index.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);

		wp_enqueue_script( 'pos-admin' );
		wp_enqueue_style( 'wp-components' );

		wp_localize_script(
			'pos-admin',
			'posData',
			[
				'restUrl'     => esc_url_raw( rest_url( 'pos/v1' ) ),
				'nonce'       => wp_create_nonce( 'wp_rest' ),
				'currentPage' => $this->getCurrentPage(),
			]
		);
	}

	/**
	 * Check if the current admin page belongs to PathOfSettings.
	 *
	 * @since 1.0.0
	 * @param string $hook WordPress admin page hook
	 * @return bool True if this is a PathOfSettings page
	 */
	private function isOptionsPage( string $hook ): bool {
		$registry = PagesRegistry::getInstance();
		return $registry->isOptionsPage( $hook );
	}

	/**
	 * Get current page data for JavaScript consumption.
	 *
	 * @since 1.0.0
	 * @return array|null Current page data or null if not applicable
	 */
	private function getCurrentPage(): ?array {
		$registry = PagesRegistry::getInstance();
		return $registry->getCurrentPage();
	}

	/**
	 * Register REST API routes for settings management.
	 *
	 * @since 1.0.0
	 */
	public function registerRestRoutes(): void {
		$controller = new SettingsController();
		$controller->register_routes();
	}

	/**
	 * Check if the package has been initialized.
	 *
	 * @since 1.0.0
	 * @return bool True if package is initialized
	 */
	public static function isInitialized(): bool {
		return self::$initialized;
	}

	/**
	 * Get package version.
	 *
	 * @since 1.0.0
	 * @return string Package version
	 */
	public static function getVersion(): string {
		return defined( 'POS_VERSION' ) ? POS_VERSION : '1.0.0';
	}
}
