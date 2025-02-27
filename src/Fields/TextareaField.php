<?php
namespace PathOfSettings\Fields;

use PathOfSettings\Core\Contracts\FieldInterface;

class TextareaField implements FieldInterface {
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
    private $value = '';
    
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
            'default' => '',
            'placeholder' => '',
            'rows' => 5,
            'required' => false,
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
        return 'textarea';
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
        $this->value = $value;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function validate($value) {
        if ($this->config['required'] && empty($value)) {
            return new \WP_Error(
                'required_field',
                sprintf(__('The field "%s" is required.', 'path-of-settings'), $this->config['label'])
            );
        }
        
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function sanitize($value) {
        return sanitize_textarea_field($value);
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