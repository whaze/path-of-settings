<?php

namespace PathOfSettings\Fields;

use PathOfSettings\Core\Contracts\FieldInterface;

/**
 * Checkbox field implementation.
 *
 * Provides a boolean checkbox field with proper type casting
 * and validation for true/false values.
 *
 * @package PathOfSettings\Fields
 * @since 1.0.0
 */
class CheckboxField implements FieldInterface {

	/**
	 * Unique field identifier.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $id;

	/**
	 * Field configuration options.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private array $config;

	/**
	 * Current field value.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	private $value = false;

	/**
	 * Create a new checkbox field.
	 *
	 * @since 1.0.0
	 * @param string $id Unique field identifier
	 * @param array  $config Field configuration options
	 */
	public function __construct( string $id, array $config = [] ) {
		$this->id     = $id;
		$this->config = wp_parse_args(
			$config,
			[
				'label'       => '',
				'description' => '',
				'default'     => false,
			]
		);

		$this->value = (bool) $this->config['default'];
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
	public function setValue( $value ): self {
		$this->value = (bool) $value;
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate( $value ) {
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( $value ) {
		return (bool) $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array {
		return [
			'id'     => $this->id,
			'type'   => $this->getType(),
			'config' => $this->config,
			'value'  => $this->value,
		];
	}
}
