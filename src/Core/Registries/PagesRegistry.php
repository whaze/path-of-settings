<?php

namespace PathOfSettings\Core\Registries;

use PathOfSettings\Core\Contracts\PageInterface;

/**
 * Registry for managing settings pages in PathOfSettings.
 *
 * Provides centralized management of settings pages, handles WordPress admin
 * menu registration, and manages page rendering. Uses singleton pattern to
 * ensure consistent page management across the application.
 *
 * @package PathOfSettings\Core\Registries
 * @since 1.0.0
 */
class PagesRegistry {

	/**
	 * Singleton instance.
	 *
	 * @since 1.0.0
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Registered settings pages.
	 *
	 * @since 1.0.0
	 * @var array Page ID => PageInterface mapping
	 */
	private array $pages = [];

	/**
	 * Get the singleton instance.
	 *
	 * @since 1.0.0
	 * @return self The registry instance
	 */
	public static function getInstance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the registry and set up WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'admin_menu', [ $this, 'registerPages' ] );
	}

	/**
	 * Register a settings page.
	 *
	 * @since 1.0.0
	 * @param PageInterface $page The page to register
	 * @return self For method chaining
	 */
	public function register( PageInterface $page ): self {
		$this->pages[ $page->getId() ] = $page;
		return $this;
	}

	/**
	 * Get all registered pages.
	 *
	 * @since 1.0.0
	 * @return array Array of PageInterface objects indexed by page ID
	 */
	public function getPages(): array {
		return $this->pages;
	}

	/**
	 * Get a specific page by ID.
	 *
	 * @since 1.0.0
	 * @param string $id Page identifier
	 * @return PageInterface|null The page object or null if not found
	 */
	public function getPage( string $id ): ?PageInterface {
		return $this->pages[ $id ] ?? null;
	}

	/**
	 * Check if a page is registered.
	 *
	 * @since 1.0.0
	 * @param string $id Page identifier
	 * @return bool True if the page is registered
	 */
	public function hasPage( string $id ): bool {
		return isset( $this->pages[ $id ] );
	}

	/**
	 * Register all pages with WordPress admin menu system.
	 *
	 * Called automatically via admin_menu hook.
	 *
	 * @since 1.0.0
	 */
	public function registerPages(): void {
		foreach ( $this->pages as $page ) {
			add_options_page(
				$page->getTitle(),
				$page->getMenuTitle(),
				$page->getCapability(),
				$page->getId(),
				[ $this, 'renderPage' ]
			);
		}
	}

	/**
	 * Render the React application container for settings pages.
	 *
	 * Called automatically by WordPress when a settings page is accessed.
	 *
	 * @since 1.0.0
	 */
	public function renderPage(): void {
		echo '<div id="pos-app" class="wrap"></div>';
	}

	/**
	 * Check if the current admin page belongs to PathOfSettings.
	 *
	 * @since 1.0.0
	 * @param string $hook Current WordPress admin page hook
	 * @return bool True if the current page is a PathOfSettings page
	 */
	public function isOptionsPage( string $hook ): bool {
		foreach ( $this->pages as $page ) {
			if ( 'settings_page_' . $page->getId() === $hook ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get current page data for JavaScript consumption.
	 *
	 * @since 1.0.0
	 * @return array|null Current page data or null if not a PathOfSettings page
	 */
	public function getCurrentPage(): ?array {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return null;
		}

		foreach ( $this->pages as $page ) {
			if ( 'settings_page_' . $page->getId() === $screen->id ) {
				return $page->toArray();
			}
		}

		return null;
	}
}
