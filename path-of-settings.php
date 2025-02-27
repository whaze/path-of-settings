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

namespace PathOfSettings;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('POS_VERSION', '1.0.0');
define('POS_FILE', __FILE__);
define('POS_PATH', plugin_dir_path(__FILE__));
define('POS_URL', plugin_dir_url(__FILE__));

// Composer autoloader
if (file_exists(POS_PATH . 'vendor/autoload.php')) {
    require_once POS_PATH . 'vendor/autoload.php';
}

/**
 * Main plugin class.
 */
class PathOfSettings {
    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Get singleton instance
     * 
     * @return PathOfSettings
     */
    public static function getInstance(): self {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->initHooks();
    }

    /**
     * Initialize hooks
     */
    private function initHooks(): void {
        add_action('plugins_loaded', [$this, 'loadTextdomain']);
        add_action('init', [$this, 'init']);
        add_action('admin_enqueue_scripts', [$this, 'registerAssets']);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
    }

    /**
     * Load plugin textdomain
     */
    public function loadTextdomain(): void {
        load_plugin_textdomain(
            'path-of-settings',
            false,
            basename(dirname(POS_FILE)) . '/languages'
        );
    }

    /**
     * Initialize plugin
     */
    public function init(): void {
        // Initialize registries
        Core\Registries\PagesRegistry::getInstance();
        Core\Registries\FieldsRegistry::getInstance();
        
        // Register settings pages
        do_action('pos_register_pages');
    }

    /**
     * Register assets
     * 
     * @param string $hook Current admin page
     */
    public function registerAssets(string $hook): void {
        // Only load on our settings pages
        if (!$this->isOptionsPage($hook)) {
            return;
        }
        
        $assetFile = include(POS_PATH . 'build/index.asset.php');
        
        // Register and enqueue scripts
        wp_register_script(
            'pos-admin',
            POS_URL . 'build/index.js',
            $assetFile['dependencies'],
            $assetFile['version'],
            true
        );
        
        wp_enqueue_script('pos-admin');
        wp_enqueue_style('wp-components');
        
        // Pass data to script
        wp_localize_script('pos-admin', 'posData', [
            'restUrl' => esc_url_raw(rest_url('pos/v1')),
            'nonce' => wp_create_nonce('wp_rest'),
            'currentPage' => $this->getCurrentPage(),
        ]);
    }
    
    /**
     * Check if current page is one of our options pages
     * 
     * @param string $hook Current admin page
     * @return bool
     */
    private function isOptionsPage(string $hook): bool {
        $registry = Core\Registries\PagesRegistry::getInstance();
        return $registry->isOptionsPage($hook);
    }
    
    /**
     * Get current page data
     * 
     * @return array|null
     */
    private function getCurrentPage(): ?array {
        $registry = Core\Registries\PagesRegistry::getInstance();
        return $registry->getCurrentPage();
    }
    
    /**
     * Register REST API routes
     */
    public function registerRestRoutes(): void {
        $controller = new RestApi\SettingsController();
        $controller->register_routes();
    }
}

// Initialize the plugin
add_action('plugins_loaded', function() {
    PathOfSettings::getInstance();
});

// Load helper functions
require_once POS_PATH . 'helpers.php';