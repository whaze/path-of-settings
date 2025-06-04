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
	 * @param array $config Package configuration options (optional)
	 *                     - version: Package version (optional)
	 *                     - path: User project path (optional, used for textdomain)
	 *                     - url: User project URL (optional)
	 *                     - file: User project main file (optional)
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
		// Version - can be auto-detected from composer.json
		if ( ! defined( 'POS_VERSION' ) ) {
			define( 'POS_VERSION', $config['version'] ?? $this->getPackageVersion() );
		}

		// These constants are now optional
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
	 * Auto-detect package version from composer.json.
	 *
	 * @since 1.0.0
	 * @return string Package version
	 */
	private function getPackageVersion(): string {
		$packagePath = $this->getPackagePath();
		if ( $packagePath ) {
			$composerFile = $packagePath . 'composer.json';
			if ( file_exists( $composerFile ) ) {
				$composer = json_decode( file_get_contents( $composerFile ), true );
				if ( isset( $composer['version'] ) ) {
					return $composer['version'];
				}
			}
		}
		return '1.0.0';
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
		// Use package path for translations
		$packagePath = $this->getPackagePath();
		if ( $packagePath ) {
			$languagesPath = $packagePath . 'languages';
			if ( is_dir( $languagesPath ) ) {
				load_plugin_textdomain(
					'path-of-settings',
					false,
					basename( $packagePath ) . '/languages'
				);
				return;
			}
		}

		// Fallback to old system if defined
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
		// Auto-detect package path using reflection
		$packagePath = $this->getPackagePath();
		$packageUrl  = $this->getPackageUrl( $packagePath );

		if ( ! $packagePath || ! $packageUrl ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PathOfSettings: Unable to detect package path or URL.' );
			}
			return;
		}

		$assetFile = $packagePath . 'build/index.asset.php';
		$jsFile    = $packagePath . 'build/index.js';

		// Check if build files exist
		if ( ! file_exists( $assetFile ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PathOfSettings: Asset file not found at ' . $assetFile );
			}
			return;
		}

		if ( ! file_exists( $jsFile ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PathOfSettings: JavaScript file not found at ' . $jsFile );
			}
			return;
		}

		$assets = include $assetFile;

		if ( ! is_array( $assets ) || ! isset( $assets['dependencies'] ) || ! isset( $assets['version'] ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PathOfSettings: Invalid asset file structure.' );
			}
			return;
		}

		wp_register_script(
			'pos-admin',
			$packageUrl . 'build/index.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);
	
		wp_register_style(
			'pos-admin',
			$packageUrl . 'build/index.css',
			[],
			$assets['version']
		);

		wp_enqueue_script( 'pos-admin' );
		wp_enqueue_style( 'wp-components' );

		// Enqueue media library for image fields
    	wp_enqueue_media();

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
	 * Get the absolute path to the PathOfSettings package directory.
	 *
	 * Uses reflection to detect the actual location of the package,
	 * regardless of how it was installed (Composer, manual, etc.).
	 *
	 * @since 1.0.0
	 * @return string|null Package absolute path or null if detection fails
	 */
	private function getPackagePath(): ?string {
		try {
			$reflection = new \ReflectionClass( $this );
			$classFile  = $reflection->getFileName();

			if ( ! $classFile ) {
				return null;
			}

			// Go up from src/PathOfSettings.php to package root
			return dirname( dirname( $classFile ) ) . '/';
		} catch ( \Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PathOfSettings: Failed to detect package path - ' . $e->getMessage() );
			}
			return null;
		}
	}

	/**
	 * Convert package absolute path to WordPress URL.
	 *
	 * @since 1.0.0
	 * @param string $packagePath Absolute path to package directory
	 * @return string|null Package URL or null if conversion fails
	 */
	private function getPackageUrl( string $packagePath ): ?string {
		if ( ! $packagePath ) {
			return null;
		}

		$packagePath = wp_normalize_path( $packagePath );

		// Try ABSPATH first (most common)
		$abspath = wp_normalize_path( ABSPATH );
		if ( strpos( $packagePath, $abspath ) === 0 ) {
			$relativePath = substr( $packagePath, strlen( $abspath ) );
			return home_url( $relativePath );
		}

		// Try WP_CONTENT_DIR (for packages in wp-content)
		$wpContentDir = wp_normalize_path( WP_CONTENT_DIR );
		if ( strpos( $packagePath, $wpContentDir ) === 0 ) {
			$relativePath = substr( $packagePath, strlen( $wpContentDir ) );
			return content_url( $relativePath );
		}

		// Try wp-content relative path (fallback)
		if ( strpos( $packagePath, '/wp-content/' ) !== false ) {
			$wpContentPos = strpos( $packagePath, '/wp-content/' );
			$relativePath = substr( $packagePath, $wpContentPos + strlen( '/wp-content' ) );
			return content_url( $relativePath );
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'PathOfSettings: Unable to convert package path to URL - ' . $packagePath );
		}

		return null;
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
