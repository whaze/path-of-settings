<?php
/**
 * Plugin Name: PathOfSettings
 * Plugin URI: https://github.com/whaze/path-of-settings
 * Description: A modern options page builder for WordPress with React and object-oriented architecture
 * Version: 1.0.0
 * Author: Jerome Buquet (Whaze)
 * Author URI: https://whodunit.fr
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: path-of-settings
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Network: false
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initialize PathOfSettings as a WordPress plugin.
 *
 * This file serves as the main plugin bootstrap when PathOfSettings
 * is used as a standalone WordPress plugin rather than a Composer package.
 *
 * @since 1.0.0
 */

// Load Composer autoloader if available
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Initialize PathOfSettings package
add_action(
	'plugins_loaded',
	function () {
		if ( class_exists( '\PathOfSettings\PathOfSettings' ) ) {
			\PathOfSettings\PathOfSettings::getInstance()->init(
				[
					'version' => '1.0.0',
					'path'    => plugin_dir_path( __FILE__ ),
					'url'     => plugin_dir_url( __FILE__ ),
					'file'    => __FILE__,
				]
			);
		} else {
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-error"><p>';
					echo esc_html__( 'PathOfSettings: Package classes not found. Please run "composer install" or check your installation.', 'path-of-settings' );
					echo '</p></div>';
				}
			);
		}
	}
);

// Load helper functions for backward compatibility
if ( file_exists( __DIR__ . '/helpers.php' ) ) {
	require_once __DIR__ . '/helpers.php';
}
