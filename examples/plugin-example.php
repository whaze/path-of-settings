<?php

/**
 * Plugin Name: PathOfSettings Example Plugin
 * Plugin URI: https://github.com/whaze/path-of-settings
 * Description: Exemple d'utilisation du package PathOfSettings dans un plugin WordPress
 * Version: 1.0.0
 * Author: Jerome Buquet
 * Requires PHP: 7.4
 * Text Domain: pos-example-plugin.
 */

// Empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe principale du plugin d'exemple.
 */
class PathOfSettingsExamplePlugin {

	/**
	 * Instance singleton.
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 */
	public static function getInstance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructeur.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Initialiser le plugin.
	 */
	public function init() {
		// Charger l'autoloader Composer
		if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			require_once __DIR__ . '/vendor/autoload.php';
		} else {
			add_action( 'admin_notices', [ $this, 'composerNotice' ] );

			return;
		}

		// Vérifier que la classe PathOfSettings existe
		if ( ! class_exists( '\PathOfSettings\PathOfSettings' ) ) {
			add_action( 'admin_notices', [ $this, 'packageNotice' ] );

			return;
		}

		// Initialiser PathOfSettings
		\PathOfSettings\PathOfSettings::getInstance()->init(
			[
				'version' => '1.0.0',
				'path'    => plugin_dir_path( __FILE__ ),
				'url'     => plugin_dir_url( __FILE__ ),
				'file'    => __FILE__,
			]
		);

		// Enregistrer nos pages d'options
		add_action( 'pos_register_pages', [ $this, 'registerPages' ] );
	}

	/**
	 * Notice si Composer n'est pas installé.
	 */
	public function composerNotice() {
		echo '<div class="notice notice-error"><p>';
		echo __( 'PathOfSettings Example Plugin: Veuillez exécuter "composer install" dans le répertoire du plugin.', 'pos-example-plugin' );
		echo '</p></div>';
	}

	/**
	 * Notice si le package n'est pas installé.
	 */
	public function packageNotice() {
		echo '<div class="notice notice-error"><p>';
		echo __( 'PathOfSettings Example Plugin: Le package whaze/path-of-settings n\'est pas installé. Exécutez "composer require whaze/path-of-settings".', 'pos-example-plugin' );
		echo '</p></div>';
	}

	/**
	 * Enregistrer les pages d'options.
	 */
	public function registerPages() {
		// Page principale
		$mainPage = pos_register_page(
			'example-settings',
			[
				'title'      => __( 'Paramètres d\'exemple', 'pos-example-plugin' ),
				'menu_title' => __( 'Exemple POS', 'pos-example-plugin' ),
				'capability' => 'manage_options',
			]
		);

		// Champs de la page principale
		pos_add_field(
			'example-settings',
			'text',
			'site_name',
			[
				'label'       => __( 'Nom du site', 'pos-example-plugin' ),
				'description' => __( 'Entrez le nom de votre site', 'pos-example-plugin' ),
				'default'     => get_bloginfo( 'name' ),
				'required'    => true,
				'placeholder' => __( 'Mon super site', 'pos-example-plugin' ),
			]
		);

		pos_add_field(
			'example-settings',
			'textarea',
			'site_description',
			[
				'label'       => __( 'Description du site', 'pos-example-plugin' ),
				'description' => __( 'Décrivez votre site en quelques mots', 'pos-example-plugin' ),
				'default'     => get_bloginfo( 'description' ),
				'placeholder' => __( 'Un site WordPress fantastique...', 'pos-example-plugin' ),
				'rows'        => 4,
			]
		);

		pos_add_field(
			'example-settings',
			'select',
			'color_scheme',
			[
				'label'       => __( 'Schéma de couleurs', 'pos-example-plugin' ),
				'description' => __( 'Choisissez le schéma de couleurs de votre site', 'pos-example-plugin' ),
				'default'     => 'light',
				'options'     => [
					'light'  => __( 'Clair', 'pos-example-plugin' ),
					'dark'   => __( 'Sombre', 'pos-example-plugin' ),
					'auto'   => __( 'Automatique', 'pos-example-plugin' ),
					'custom' => __( 'Personnalisé', 'pos-example-plugin' ),
				],
			]
		);

		pos_add_field(
			'example-settings',
			'checkbox',
			'enable_features',
			[
				'label'       => __( 'Activer les fonctionnalités avancées', 'pos-example-plugin' ),
				'description' => __( 'Cochez cette case pour activer les fonctionnalités avancées', 'pos-example-plugin' ),
				'default'     => false,
			]
		);

		pos_add_field(
			'example-settings',
			'text',
			'api_key',
			[
				'label'       => __( 'Clé API', 'pos-example-plugin' ),
				'description' => __( 'Entrez votre clé API pour les services externes', 'pos-example-plugin' ),
				'placeholder' => __( 'sk-...', 'pos-example-plugin' ),
			]
		);

		// Page secondaire pour montrer les possibilités
		$advancedPage = pos_register_page(
			'example-advanced',
			[
				'title'      => __( 'Paramètres avancés', 'pos-example-plugin' ),
				'menu_title' => __( 'Avancé', 'pos-example-plugin' ),
				'capability' => 'manage_options',
			]
		);

		pos_add_field(
			'example-advanced',
			'select',
			'cache_duration',
			[
				'label'       => __( 'Durée du cache', 'pos-example-plugin' ),
				'description' => __( 'Choisissez la durée de mise en cache', 'pos-example-plugin' ),
				'default'     => '3600',
				'options'     => [
					'300'   => __( '5 minutes', 'pos-example-plugin' ),
					'1800'  => __( '30 minutes', 'pos-example-plugin' ),
					'3600'  => __( '1 heure', 'pos-example-plugin' ),
					'86400' => __( '24 heures', 'pos-example-plugin' ),
				],
			]
		);

		pos_add_field(
			'example-advanced',
			'checkbox',
			'debug_mode',
			[
				'label'       => __( 'Mode debug', 'pos-example-plugin' ),
				'description' => __( 'Activer le mode debug (attention en production)', 'pos-example-plugin' ),
				'default'     => false,
			]
		);
	}
}

// Initialiser le plugin
PathOfSettingsExamplePlugin::getInstance();

/**
 * Fonctions utilitaires pour récupérer les settings.
 */

/**
 * Obtenir le nom du site configuré.
 */
function pos_example_get_site_name( $default = '' ) {
	return pos_get_setting( 'example-settings', 'site_name', $default );
}

/**
 * Obtenir la description du site configurée.
 */
function pos_example_get_site_description( $default = '' ) {
	return pos_get_setting( 'example-settings', 'site_description', $default );
}

/**
 * Obtenir le schéma de couleurs.
 */
function pos_example_get_color_scheme( $default = 'light' ) {
	return pos_get_setting( 'example-settings', 'color_scheme', $default );
}

/**
 * Vérifier si les fonctionnalités avancées sont activées.
 */
function pos_example_is_features_enabled() {
	return (bool) pos_get_setting( 'example-settings', 'enable_features', false );
}

/**
 * Obtenir la clé API.
 */
function pos_example_get_api_key( $default = '' ) {
	return pos_get_setting( 'example-settings', 'api_key', $default );
}

/**
 * Obtenir la durée du cache.
 */
function pos_example_get_cache_duration( $default = 3600 ) {
	return (int) pos_get_setting( 'example-advanced', 'cache_duration', $default );
}

/**
 * Vérifier si le mode debug est activé.
 */
function pos_example_is_debug_enabled() {
	return (bool) pos_get_setting( 'example-advanced', 'debug_mode', false );
}
