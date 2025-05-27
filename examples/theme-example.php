<?php
/**
 * PathOfSettings Theme Integration Example
 *
 * This file demonstrates how to integrate PathOfSettings package into a WordPress theme.
 * Include this file in your theme's functions.php:
 * require_once get_template_directory() . '/examples/theme-example.php';
 *
 * Or copy the relevant parts directly into your functions.php file.
 *
 * @package PathOfSettings\Examples
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme options manager using PathOfSettings.
 *
 * Demonstrates how to create comprehensive theme options with PathOfSettings,
 * including appearance settings, typography, and advanced customization options.
 *
 * @since 1.0.0
 */
class PathOfSettingsThemeExample {

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
	 * Initialize theme options.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'after_setup_theme', [ $this, 'init' ] );
	}

	/**
	 * Initialize theme integration.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {
		if ( ! $this->loadDependencies() ) {
			return;
		}

		$this->initPathOfSettings();
		$this->registerHooks();
	}

	/**
	 * Load dependencies and check for PathOfSettings package.
	 *
	 * @since 1.0.0
	 * @return bool True if dependencies loaded successfully
	 */
	private function loadDependencies(): bool {
		$autoloader = get_template_directory() . '/vendor/autoload.php';

		if ( ! file_exists( $autoloader ) ) {
			add_action( 'admin_notices', [ $this, 'showComposerNotice' ] );
			return false;
		}

		require_once $autoloader;

		if ( ! class_exists( '\PathOfSettings\PathOfSettings' ) ) {
			add_action( 'admin_notices', [ $this, 'showPackageNotice' ] );
			return false;
		}

		return true;
	}

	/**
	 * Initialize PathOfSettings for theme usage.
	 *
	 * @since 1.0.0
	 */
	private function initPathOfSettings(): void {
		\PathOfSettings\PathOfSettings::getInstance()->init(
			[
				'version' => wp_get_theme()->get( 'Version' ) ?: '1.0.0',
				'path'    => get_template_directory() . '/',
				'url'     => get_template_directory_uri() . '/',
				'file'    => get_template_directory() . '/style.css',
			]
		);
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	private function registerHooks(): void {
		add_action( 'pos_register_pages', [ $this, 'registerThemePages' ] );
		add_action( 'wp_head', [ $this, 'outputCustomCSS' ] );
		add_action( 'wp_footer', [ $this, 'outputCustomJS' ] );
	}

	/**
	 * Show notice when Composer is not installed.
	 *
	 * @since 1.0.0
	 */
	public function showComposerNotice(): void {
		if ( current_user_can( 'edit_theme_options' ) ) {
			echo '<div class="notice notice-error"><p>';
			echo esc_html__( 'Theme: Please run "composer install" in the theme directory to use PathOfSettings.', 'textdomain' );
			echo '</p></div>';
		}
	}

	/**
	 * Show notice when PathOfSettings package is missing.
	 *
	 * @since 1.0.0
	 */
	public function showPackageNotice(): void {
		if ( current_user_can( 'edit_theme_options' ) ) {
			echo '<div class="notice notice-error"><p>';
			echo esc_html__( 'Theme: The whaze/path-of-settings package is not installed. Run "composer require whaze/path-of-settings".', 'textdomain' );
			echo '</p></div>';
		}
	}

	/**
	 * Register theme options pages.
	 *
	 * @since 1.0.0
	 */
	public function registerThemePages(): void {
		$this->registerAppearanceSettings();
		$this->registerAdvancedSettings();
	}

	/**
	 * Register appearance settings page.
	 *
	 * @since 1.0.0
	 */
	private function registerAppearanceSettings(): void {
		pos_register_page(
			'theme-appearance',
			[
				'title'      => __( 'Theme Appearance', 'textdomain' ),
				'menu_title' => __( 'Theme Options', 'textdomain' ),
				'capability' => 'edit_theme_options',
			]
		);

		// Layout Settings
		pos_add_field(
			'theme-appearance',
			'select',
			'layout',
			[
				'label'       => __( 'Default Layout', 'textdomain' ),
				'description' => __( 'Choose the default page layout for your site', 'textdomain' ),
				'default'     => 'full-width',
				'options'     => [
					'full-width'    => __( 'Full Width', 'textdomain' ),
					'boxed'         => __( 'Boxed', 'textdomain' ),
					'sidebar-left'  => __( 'Left Sidebar', 'textdomain' ),
					'sidebar-right' => __( 'Right Sidebar', 'textdomain' ),
				],
			]
		);

		// Color Scheme
		pos_add_field(
			'theme-appearance',
			'select',
			'color_scheme',
			[
				'label'       => __( 'Color Scheme', 'textdomain' ),
				'description' => __( 'Select the color scheme for your theme', 'textdomain' ),
				'default'     => 'default',
				'options'     => [
					'default' => __( 'Default', 'textdomain' ),
					'dark'    => __( 'Dark Mode', 'textdomain' ),
					'light'   => __( 'Light Mode', 'textdomain' ),
					'blue'    => __( 'Blue Theme', 'textdomain' ),
					'green'   => __( 'Green Theme', 'textdomain' ),
					'custom'  => __( 'Custom Colors', 'textdomain' ),
				],
			]
		);

		// Custom Colors
		pos_add_field(
			'theme-appearance',
			'text',
			'primary_color',
			[
				'label'       => __( 'Primary Color', 'textdomain' ),
				'description' => __( 'Hexadecimal color code for the primary theme color (e.g., #ff0000)', 'textdomain' ),
				'placeholder' => '#007cba',
			]
		);

		pos_add_field(
			'theme-appearance',
			'text',
			'secondary_color',
			[
				'label'       => __( 'Secondary Color', 'textdomain' ),
				'description' => __( 'Hexadecimal color code for the secondary theme color', 'textdomain' ),
				'placeholder' => '#ffffff',
			]
		);

		// Typography
		pos_add_field(
			'theme-appearance',
			'select',
			'font_family',
			[
				'label'       => __( 'Font Family', 'textdomain' ),
				'description' => __( 'Choose the primary font for your site', 'textdomain' ),
				'default'     => 'system',
				'options'     => [
					'system'    => __( 'System Font', 'textdomain' ),
					'arial'     => 'Arial, sans-serif',
					'helvetica' => 'Helvetica, sans-serif',
					'georgia'   => 'Georgia, serif',
					'times'     => 'Times New Roman, serif',
					'roboto'    => 'Roboto (Google Fonts)',
					'open-sans' => 'Open Sans (Google Fonts)',
				],
			]
		);

		pos_add_field(
			'theme-appearance',
			'select',
			'font_size',
			[
				'label'       => __( 'Base Font Size', 'textdomain' ),
				'description' => __( 'Default font size for body text', 'textdomain' ),
				'default'     => '16',
				'options'     => [
					'14' => '14px',
					'15' => '15px',
					'16' => '16px',
					'17' => '17px',
					'18' => '18px',
					'20' => '20px',
				],
			]
		);

		// Header/Footer Options
		pos_add_field(
			'theme-appearance',
			'checkbox',
			'show_search',
			[
				'label'       => __( 'Show Search in Header', 'textdomain' ),
				'description' => __( 'Display search form in the site header', 'textdomain' ),
				'default'     => true,
			]
		);

		pos_add_field(
			'theme-appearance',
			'textarea',
			'footer_text',
			[
				'label'       => __( 'Footer Text', 'textdomain' ),
				'description' => __( 'Custom text to display in the site footer', 'textdomain' ),
				'default'     => sprintf( __( '© %s - All rights reserved', 'textdomain' ), date( 'Y' ) ),
				'rows'        => 3,
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
			'theme-advanced',
			[
				'title'      => __( 'Advanced Theme Settings', 'textdomain' ),
				'menu_title' => __( 'Advanced', 'textdomain' ),
				'capability' => 'edit_theme_options',
			]
		);

		// Custom Code
		pos_add_field(
			'theme-advanced',
			'textarea',
			'custom_css',
			[
				'label'       => __( 'Custom CSS', 'textdomain' ),
				'description' => __( 'Add custom CSS styles here', 'textdomain' ),
				'placeholder' => '/* Your custom CSS here */',
				'rows'        => 10,
			]
		);

		pos_add_field(
			'theme-advanced',
			'textarea',
			'custom_js',
			[
				'label'       => __( 'Custom JavaScript', 'textdomain' ),
				'description' => __( 'Add custom JavaScript code here (without script tags)', 'textdomain' ),
				'placeholder' => '// Your custom JavaScript here',
				'rows'        => 10,
			]
		);

		// Analytics
		pos_add_field(
			'theme-advanced',
			'text',
			'google_analytics',
			[
				'label'       => __( 'Google Analytics ID', 'textdomain' ),
				'description' => __( 'Enter your Google Analytics tracking ID (e.g., G-XXXXXXXXXX)', 'textdomain' ),
				'placeholder' => 'G-XXXXXXXXXX',
			]
		);

		// Performance
		pos_add_field(
			'theme-advanced',
			'checkbox',
			'minify_css',
			[
				'label'       => __( 'Minify CSS', 'textdomain' ),
				'description' => __( 'Enable CSS minification for better performance', 'textdomain' ),
				'default'     => false,
			]
		);

		pos_add_field(
			'theme-advanced',
			'checkbox',
			'enable_lazy_loading',
			[
				'label'       => __( 'Lazy Load Images', 'textdomain' ),
				'description' => __( 'Enable lazy loading for images to improve page load speed', 'textdomain' ),
				'default'     => true,
			]
		);
	}

	/**
	 * Output custom CSS in the document head.
	 *
	 * @since 1.0.0
	 */
	public function outputCustomCSS(): void {
		$custom_css      = pos_get_setting( 'theme-advanced', 'custom_css', '' );
		$primary_color   = pos_get_setting( 'theme-appearance', 'primary_color', '' );
		$secondary_color = pos_get_setting( 'theme-appearance', 'secondary_color', '' );
		$font_family     = pos_get_setting( 'theme-appearance', 'font_family', 'system' );
		$font_size       = pos_get_setting( 'theme-appearance', 'font_size', '16' );

		if ( $custom_css || $primary_color || $secondary_color || $font_family !== 'system' ) {
			echo '<style id="theme-custom-css">';

			// CSS Custom Properties
			if ( $primary_color || $secondary_color ) {
				echo ':root {';
				if ( $primary_color ) {
					echo '--theme-primary-color: ' . esc_attr( $primary_color ) . ';';
				}
				if ( $secondary_color ) {
					echo '--theme-secondary-color: ' . esc_attr( $secondary_color ) . ';';
				}
				echo '}';
			}

			// Typography
			if ( $font_family !== 'system' ) {
				$font_stack = $this->getFontStack( $font_family );
				echo 'body { font-family: ' . $font_stack . '; font-size: ' . intval( $font_size ) . 'px; }';
			}

			// Custom CSS
			if ( $custom_css ) {
				echo wp_strip_all_tags( $custom_css );
			}

			echo '</style>';
		}

		$this->outputGoogleAnalytics();
	}

	/**
	 * Output Google Analytics tracking code.
	 *
	 * @since 1.0.0
	 */
	private function outputGoogleAnalytics(): void {
		$ga_id = pos_get_setting( 'theme-advanced', 'google_analytics', '' );

		if ( $ga_id && ! is_admin() ) {
			echo "<!-- Google Analytics -->\n";
			echo "<script async src='https://www.googletagmanager.com/gtag/js?id=" . esc_attr( $ga_id ) . "'></script>\n";
			echo "<script>\n";
			echo "window.dataLayer = window.dataLayer || [];\n";
			echo "function gtag(){dataLayer.push(arguments);}\n";
			echo "gtag('js', new Date());\n";
			echo "gtag('config', '" . esc_attr( $ga_id ) . "');\n";
			echo "</script>\n";
		}
	}

	/**
	 * Output custom JavaScript in the document footer.
	 *
	 * @since 1.0.0
	 */
	public function outputCustomJS(): void {
		$custom_js = pos_get_setting( 'theme-advanced', 'custom_js', '' );

		if ( $custom_js && ! is_admin() ) {
			echo '<script id="theme-custom-js">';
			echo wp_strip_all_tags( $custom_js );
			echo '</script>';
		}
	}

	/**
	 * Get font stack for the specified font family.
	 *
	 * @since 1.0.0
	 * @param string $font_family Font family identifier
	 * @return string CSS font stack
	 */
	private function getFontStack( string $font_family ): string {
		$fonts = [
			'system'    => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
			'arial'     => 'Arial, Helvetica, sans-serif',
			'helvetica' => 'Helvetica, Arial, sans-serif',
			'georgia'   => 'Georgia, "Times New Roman", serif',
			'times'     => '"Times New Roman", Times, serif',
			'roboto'    => 'Roboto, Arial, sans-serif',
			'open-sans' => '"Open Sans", Arial, sans-serif',
		];

		return $fonts[ $font_family ] ?? $fonts['system'];
	}
}

PathOfSettingsThemeExample::getInstance();

/**
 * Theme utility functions for retrieving settings.
 */

/**
 * Get the theme layout setting.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Layout setting
 */
function theme_get_layout( string $default = 'full-width' ): string {
	return pos_get_setting( 'theme-appearance', 'layout', $default );
}

/**
 * Get the color scheme setting.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Color scheme setting
 */
function theme_get_color_scheme( string $default = 'default' ): string {
	return pos_get_setting( 'theme-appearance', 'color_scheme', $default );
}

/**
 * Get the primary color setting.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Primary color hex code
 */
function theme_get_primary_color( string $default = '' ): string {
	return pos_get_setting( 'theme-appearance', 'primary_color', $default );
}

/**
 * Get the secondary color setting.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Secondary color hex code
 */
function theme_get_secondary_color( string $default = '' ): string {
	return pos_get_setting( 'theme-appearance', 'secondary_color', $default );
}

/**
 * Check if search should be shown in header.
 *
 * @since 1.0.0
 * @return bool True if search should be displayed
 */
function theme_should_show_search(): bool {
	return (bool) pos_get_setting( 'theme-appearance', 'show_search', true );
}

/**
 * Get the footer text setting.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Footer text
 */
function theme_get_footer_text( string $default = '' ): string {
	if ( empty( $default ) ) {
		$default = sprintf( __( '© %s - All rights reserved', 'textdomain' ), date( 'Y' ) );
	}
	return pos_get_setting( 'theme-appearance', 'footer_text', $default );
}

/**
 * Get the font family setting.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Font family setting
 */
function theme_get_font_family( string $default = 'system' ): string {
	return pos_get_setting( 'theme-appearance', 'font_family', $default );
}

/**
 * Get the font size setting.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Font size in pixels
 */
function theme_get_font_size( string $default = '16' ): string {
	return pos_get_setting( 'theme-appearance', 'font_size', $default );
}

/**
 * Get the custom CSS setting.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Custom CSS code
 */
function theme_get_custom_css( string $default = '' ): string {
	return pos_get_setting( 'theme-advanced', 'custom_css', $default );
}

/**
 * Get the custom JavaScript setting.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Custom JavaScript code
 */
function theme_get_custom_js( string $default = '' ): string {
	return pos_get_setting( 'theme-advanced', 'custom_js', $default );
}

/**
 * Get the Google Analytics ID setting.
 *
 * @since 1.0.0
 * @param string $default Default value if setting not found
 * @return string Google Analytics tracking ID
 */
function theme_get_google_analytics( string $default = '' ): string {
	return pos_get_setting( 'theme-advanced', 'google_analytics', $default );
}

/**
 * Check if CSS minification is enabled.
 *
 * @since 1.0.0
 * @return bool True if CSS minification is enabled
 */
function theme_is_css_minification_enabled(): bool {
	return (bool) pos_get_setting( 'theme-advanced', 'minify_css', false );
}

/**
 * Check if lazy loading is enabled.
 *
 * @since 1.0.0
 * @return bool True if lazy loading is enabled
 */
function theme_is_lazy_loading_enabled(): bool {
	return (bool) pos_get_setting( 'theme-advanced', 'enable_lazy_loading', true );
}

/**
 * Get all theme appearance settings.
 *
 * @since 1.0.0
 * @return array All appearance settings
 */
function theme_get_appearance_settings(): array {
	return pos_get_settings( 'theme-appearance' );
}

/**
 * Get all theme advanced settings.
 *
 * @since 1.0.0
 * @return array All advanced settings
 */
function theme_get_advanced_settings(): array {
	return pos_get_settings( 'theme-advanced' );
}
