<?php
namespace PathOfSettings\Core\Contracts;

interface FieldInterface {
    /**
     * Get field ID
     * 
     * @return string
     */
    public function getId(): string;
    
    /**
     * Get field type
     * 
     * @return string
     */
    public function getType(): string;
    
    /**
     * Get field configuration
     * 
     * @return array
     */
    public function getConfig(): array;
    
    /**
     * Get field value
     * 
     * @return mixed
     */
    public function getValue();
    
    /**
     * Set field value
     * 
     * @param mixed $value
     * @return self
     */
    public function setValue($value): self;
    
    /**
     * Validate field value
     * 
     * @param mixed $value
     * @return bool|\WP_Error
     */
    public function validate($value);
    
    /**
     * Sanitize field value
     * 
     * @param mixed $value
     * @return mixed
     */
    public function sanitize($value);
    
    /**
     * Get field as array
     * 
     * @return array
     */
    public function toArray(): array;
}