<?php
namespace PathOfSettings\Core\Models;

use PathOfSettings\Core\Contracts\PageInterface;
use PathOfSettings\Core\Contracts\FieldInterface;

class Page implements PageInterface {
    /**
     * Page ID
     */
    private string $id;
    
    /**
     * Page configuration
     */
    private array $config;
    
    /**
     * Page fields
     */
    private array $fields = [];
    
    /**
     * Constructor
     * 
     * @param string $id
     * @param array $config
     */
    public function __construct(string $id, array $config = []) {
        $this->id = $id;
        $this->config = wp_parse_args($config, [
            'title' => '',
            'menu_title' => '',
            'capability' => 'manage_options',
            'parent_slug' => '',
            'icon' => '',
            'position' => null,
        ]);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getId(): string {
        return $this->id;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTitle(): string {
        return $this->config['title'];
    }
    
    /**
     * {@inheritDoc}
     */
    public function getMenuTitle(): string {
        return !empty($this->config['menu_title']) ? $this->config['menu_title'] : $this->getTitle();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getCapability(): string {
        return $this->config['capability'];
    }
    
    /**
     * {@inheritDoc}
     */
    public function getHook(): string {
        return 'settings_page_' . $this->id;
    }
    
    /**
     * {@inheritDoc}
     */
    public function addField(FieldInterface $field): self {
        $this->fields[$field->getId()] = $field;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getFields(): array {
        return $this->fields;
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray(): array {
        $fields = [];
        
        foreach ($this->fields as $field) {
            $fields[] = $field->toArray();  // This creates a sequential array
        }
        
        return [
            'id' => $this->id,
            'title' => $this->getTitle(),
            'menu_title' => $this->getMenuTitle(),
            'capability' => $this->getCapability(),
            'fields' => $fields,
        ];
    }
}