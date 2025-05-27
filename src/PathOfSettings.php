<?php

namespace PathOfSettings;

use PathOfSettings\Core\Registries\FieldsRegistry;
use PathOfSettings\Core\Registries\PagesRegistry;
use PathOfSettings\RestApi\SettingsController;

/**
 * Classe principale du package PathOfSettings.
 */
class PathOfSettings {

	/**
	 * Instance singleton.
	 */
	private static $instance = null;

	/**
	 * État d'initialisation.
	 */
	private static $initialized = false;

	/**
	 * Get singleton instance.
	 *
	 * @return self
	 */
	public static function getInstance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructeur privé pour singleton.
	 */
	private function __construct() {
		// Construction privée pour singleton
	}

	/**
	 * Initialiser le package.
	 *
	 * @param array $config Configuration du package
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
	 * Définir les constantes.
	 *
	 * @param array $config
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
	 * Initialiser les hooks WordPress.
	 */
	private function initHooks(): void {
		add_action( 'plugins_loaded', [ $this, 'loadTextdomain' ] );
		add_action( 'init', [ $this, 'initRegistries' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'registerAssets' ] );
		add_action( 'rest_api_init', [ $this, 'registerRestRoutes' ] );
	}

	/**
	 * Charger les traductions.
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
	 * Initialiser les registres.
	 */
	public function initRegistries(): void {
		// Initialiser les registres
		PagesRegistry::getInstance();
		FieldsRegistry::getInstance();

		// Déclencher l'action pour l'enregistrement des pages
		do_action( 'pos_register_pages' );
	}

	/**
	 * Enregistrer les assets.
	 *
	 * @param string $hook Current admin page
	 */
	public function registerAssets( string $hook ): void {
		// Charger seulement sur nos pages d'options
		if ( ! $this->isOptionsPage( $hook ) ) {
			return;
		}

		$this->enqueueAssets();
	}

	/**
	 * Charger les assets JavaScript et CSS.
	 */
	private function enqueueAssets(): void {
		if ( ! defined( 'POS_PATH' ) || ! defined( 'POS_URL' ) || ! POS_PATH || ! POS_URL ) {
			return;
		}

		$assetFile = POS_PATH . 'build/index.asset.php';

		if ( ! file_exists( $assetFile ) ) {
			return;
		}

		$assets = include $assetFile;

		// Enregistrer et charger le script
		wp_register_script(
			'pos-admin',
			POS_URL . 'build/index.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);

		wp_enqueue_script( 'pos-admin' );
		wp_enqueue_style( 'wp-components' );

		// Passer les données au script
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
	 * Vérifier si la page actuelle est une de nos pages d'options.
	 *
	 * @param string $hook Current admin page
	 *
	 * @return bool
	 */
	private function isOptionsPage( string $hook ): bool {
		$registry = PagesRegistry::getInstance();

		return $registry->isOptionsPage( $hook );
	}

	/**
	 * Obtenir les données de la page actuelle.
	 *
	 * @return array|null
	 */
	private function getCurrentPage(): ?array {
		$registry = PagesRegistry::getInstance();

		return $registry->getCurrentPage();
	}

	/**
	 * Enregistrer les routes REST API.
	 */
	public function registerRestRoutes(): void {
		$controller = new SettingsController();
		$controller->register_routes();
	}
}
