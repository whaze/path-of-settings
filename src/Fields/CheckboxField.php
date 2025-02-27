<?php
namespace PathOfSettings\Fields;

use PathOfSettings\Core\Contracts\FieldInterface;

class CheckboxField implements FieldInterface {
    /**
     * Field ID
     */
    private string $id;
    
    /**
     * Field configuration
     */
    private array $config;
    
    /**
     * Field value
     */
    private $value = false;
    
    /**
     * Constructor
     * 
     * @param string $id
     * @param array $config
     */
    public function __construct(string $id, array $config = []) {
        $this->id = $id;
        $this->config = wp_parse_args($config, [
            'label' => '',
            'description' => '',
            'default' => false,
        ]);
        
        $this->value = $this->config['default'];
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
    public function getType(): string {
        return 'checkbox';
    }
    
    /**
     * {@inheritDoc}
     */
    public function getConfig(): array {
        return $this->config;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getValue() {
        return $this->value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setValue($value): self {
        $this->value = (bool) $value;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function validate($value) {
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function sanitize($value) {
        return (bool) $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'type' => $this->getType(),
            'config' => $this->config,
            'value' => $this->value,
        ];
    }
}