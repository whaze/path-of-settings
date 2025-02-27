<?php
namespace PathOfSettings\Core\Registries;

use PathOfSettings\Core\Contracts\FieldInterface;

class FieldsRegistry {
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Registered fields
     */
    private array $fields = [];
    
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
        // Register default field types
        $this->registerDefaultFields();
    }
    
    /**
     * Register default field types
     */
    private function registerDefaultFields(): void {
        // Register field types
        $this->register('text', '\PathOfSettings\Fields\TextField');
        $this->register('select', '\PathOfSettings\Fields\SelectField');
        $this->register('checkbox', '\PathOfSettings\Fields\CheckboxField');
        $this->register('textarea', '\PathOfSettings\Fields\TextareaField');
    }
    
    /**
     * Register a field type
     * 
     * @param string $type
     * @param string $class
     * @return self
     */
    public function register(string $type, string $class): self {
        $this->fields[$type] = $class;
        return $this;
    }
    
    /**
     * Get all registered field types
     * 
     * @return array
     */
    public function getFields(): array {
        return $this->fields;
    }
    
    /**
     * Create a field instance
     * 
     * @param string $type
     * @param string $id
     * @param array $config
     * @return FieldInterface
     */
    public function createField(string $type, string $id, array $config = []): FieldInterface {
        if (!isset($this->fields[$type])) {
            throw new \Exception("Field type '{$type}' not registered.");
        }
        
        $class = $this->fields[$type];
        return new $class($id, $config);
    }
}