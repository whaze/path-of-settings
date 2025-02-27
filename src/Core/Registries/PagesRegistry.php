<?php
namespace PathOfSettings\Core\Registries;

use PathOfSettings\Core\Contracts\PageInterface;

class PagesRegistry {
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Registered pages
     */
    private array $pages = [];
    
    /**
     * Get singleton instance
     * 
     * @return self
     */
    public static function getInstance(): self {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Private constructor
     */
    private function __construct() {
        add_action('admin_menu', [$this, 'registerPages']);
    }
    
    /**
     * Register a page
     * 
     * @param PageInterface $page
     * @return self
     */
    public function register(PageInterface $page): self {
        $this->pages[$page->getId()] = $page;
        return $this;
    }
    
    /**
     * Get all registered pages
     * 
     * @return array
     */
    public function getPages(): array {
        return $this->pages;
    }
    
    /**
     * Get a specific page
     * 
     * @param string $id
     * @return PageInterface|null
     */
    public function getPage(string $id): ?PageInterface {
        return $this->pages[$id] ?? null;
    }
    
    /**
     * Register pages in WordPress admin
     */
    public function registerPages(): void {
        foreach ($this->pages as $page) {
            add_options_page(
                $page->getTitle(),
                $page->getMenuTitle(),
                $page->getCapability(),
                $page->getId(),
                [$this, 'renderPage']
            );
        }
    }
    
    /**
     * Render page
     */
    public function renderPage(): void {
        echo '<div id="pos-app" class="wrap"></div>';
    }
    
    /**
     * Check if current page is one of our options pages
     * 
     * @param string $hook Current admin page
     * @return bool
     */
    public function isOptionsPage(string $hook): bool {
        foreach ($this->pages as $page) {
            if ('settings_page_' . $page->getId() === $hook) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get current page data
     * 
     * @return array|null
     */
    public function getCurrentPage(): ?array {
        $screen = get_current_screen();
        if (!$screen) {
            return null;
        }
        
        foreach ($this->pages as $page) {
            if ('settings_page_' . $page->getId() === $screen->id) {
                return $page->toArray();
            }
        }
        
        return null;
    }
}