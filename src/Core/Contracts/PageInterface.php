<?php
namespace PathOfSettings\Core\Contracts;

interface PageInterface {
    /**
     * Get page ID
     * 
     * @return string
     */
    public function getId(): string;
    
    /**
     * Get page title
     * 
     * @return string
     */
    public function getTitle(): string;
    
    /**
     * Get page menu title
     * 
     * @return string
     */
    public function getMenuTitle(): string;
    
    /**
     * Get page capability
     * 
     * @return string
     */
    public function getCapability(): string;
    
    /**
     * Get page hook
     * 
     * @return string
     */
    public function getHook(): string;
    
    /**
     * Add field to page
     * 
     * @param FieldInterface $field
     * @return self
     */
    public function addField(FieldInterface $field): self;
    
    /**
     * Get all fields
     * 
     * @return array
     */
    public function getFields(): array;
    
    /**
     * Get page as array
     * 
     * @return array
     */
    public function toArray(): array;
}