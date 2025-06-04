<?php

namespace PathOfSettings\Core\Registries;

use PathOfSettings\Core\Contracts\FieldInterface;

/**
 * Registry for managing field types in PathOfSettings.
 *
 * Provides a centralized registry for field type classes, handles field type
 * registration, and creates field instances. Uses singleton pattern to ensure
 * consistent field type management across the application.
 *
 * @package PathOfSettings\Core\Registries
 * @since 1.0.0
 */
class FieldsRegistry {

	/**
	 * Singleton instance.
	 *
	 * @since 1.0.0
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Registered field type classes.
	 *
	 * @since 1.0.0
	 * @var array Field type => class name mapping
	 */
	private array $fields = [];

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
	 * Initialize the registry and register default field types.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->registerDefaultFields();
	}

	/**
	 * Register the built-in field types.
	 *
	 * @since 1.0.0
	 */
	private function registerDefaultFields(): void {
		$this->register( 'text', '\PathOfSettings\Fields\TextField' );
		$this->register( 'select', '\PathOfSettings\Fields\SelectField' );
		$this->register( 'checkbox', '\PathOfSettings\Fields\CheckboxField' );
		$this->register( 'textarea', '\PathOfSettings\Fields\TextareaField' );
		$this->register( 'image', '\PathOfSettings\Fields\ImageField' );
	}

	/**
	 * Register a new field type.
	 *
	 * @since 1.0.0
	 * @param string $type Field type identifier
	 * @param string $class Fully qualified class name implementing FieldInterface
	 * @return self For method chaining
	 * @throws \InvalidArgumentException If class doesn't implement FieldInterface
	 */
	public function register( string $type, string $class ): self {
		if ( ! class_exists( $class ) ) {
			throw new \InvalidArgumentException( "Field class '{$class}' does not exist." );
		}

		if ( ! is_subclass_of( $class, FieldInterface::class ) ) {
			throw new \InvalidArgumentException( "Field class '{$class}' must implement FieldInterface." );
		}

		$this->fields[ $type ] = $class;
		return $this;
	}

	/**
	 * Get all registered field types.
	 *
	 * @since 1.0.0
	 * @return array Field type => class name mapping
	 */
	public function getFields(): array {
		return $this->fields;
	}

	/**
	 * Check if a field type is registered.
	 *
	 * @since 1.0.0
	 * @param string $type Field type identifier
	 * @return bool True if the field type is registered
	 */
	public function hasField( string $type ): bool {
		return isset( $this->fields[ $type ] );
	}

	/**
	 * Create a field instance of the specified type.
	 *
	 * @since 1.0.0
	 * @param string $type Field type identifier
	 * @param string $id Unique field identifier
	 * @param array  $config Field configuration options
	 * @return FieldInterface The created field instance
	 * @throws \InvalidArgumentException If field type is not registered
	 */
	public function createField( string $type, string $id, array $config = [] ): FieldInterface {
		if ( ! $this->hasField( $type ) ) {
			throw new \InvalidArgumentException( "Field type '{$type}' is not registered." );
		}

		$class = $this->fields[ $type ];
		return new $class( $id, $config );
	}
}
