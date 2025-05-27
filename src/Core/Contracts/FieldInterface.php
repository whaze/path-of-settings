<?php

namespace PathOfSettings\Core\Contracts;

/**
 * Contract for field implementations in PathOfSettings.
 *
 * Defines the interface that all field types must implement to provide
 * consistent behavior for form field handling, validation, and data processing.
 *
 * @package PathOfSettings\Core\Contracts
 * @since 1.0.0
 */
interface FieldInterface {

	/**
	 * Get the unique field identifier.
	 *
	 * @since 1.0.0
	 * @return string The field ID
	 */
	public function getId(): string;

	/**
	 * Get the field type identifier.
	 *
	 * @since 1.0.0
	 * @return string The field type (e.g., 'text', 'select', 'checkbox')
	 */
	public function getType(): string;

	/**
	 * Get the field configuration array.
	 *
	 * @since 1.0.0
	 * @return array Field configuration including label, description, default value, etc.
	 */
	public function getConfig(): array;

	/**
	 * Get the current field value.
	 *
	 * @since 1.0.0
	 * @return mixed The current field value
	 */
	public function getValue();

	/**
	 * Set the field value.
	 *
	 * @since 1.0.0
	 * @param mixed $value The value to set
	 * @return self For method chaining
	 */
	public function setValue( $value ): self;

	/**
	 * Validate a field value.
	 *
	 * @since 1.0.0
	 * @param mixed $value The value to validate
	 * @return bool|\WP_Error True if valid, WP_Error if validation fails
	 */
	public function validate( $value );

	/**
	 * Sanitize a field value for safe storage.
	 *
	 * @since 1.0.0
	 * @param mixed $value The value to sanitize
	 * @return mixed The sanitized value
	 */
	public function sanitize( $value );

	/**
	 * Convert field to array representation for API responses.
	 *
	 * @since 1.0.0
	 * @return array Field data as associative array
	 */
	public function toArray(): array;
}
