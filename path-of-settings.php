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
 */

// Empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Charger l'autoloader Composer
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Initialiser le package via la nouvelle classe d'entrée
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
		}
	}
);

// Les helpers restent chargés pour la compatibilité API procédurale
if ( file_exists( __DIR__ . '/helpers.php' ) ) {
	require_once __DIR__ . '/helpers.php';
}
