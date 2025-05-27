<?php

namespace PathOfSettings\Fields;

use PathOfSettings\Core\Contracts\FieldInterface;

/**
 * Text input field implementation.
 *
 * Provides a standard text input field with validation, sanitization,
 * and support for required fields and placeholders.
 *
 * @package PathOfSettings\Fields
 * @since 1.0.0
 */
class TextField implements FieldInterface {

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
	 * @var string
	 */
	private $value = '';

	/**
	 * Create a new text field.
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
				'default'     => '',
				'placeholder' => '',
				'required'    => false,
			]
		);

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
		return 'text';
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
		$this->value = (string) $value;
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate( $value ) {
		if ( $this->config['required'] && empty( trim( $value ) ) ) {
			return new \WP_Error(
				'required_field',
				sprintf( __( 'The field "%s" is required.', 'path-of-settings' ), $this->config['label'] )
			);
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( $value ) {
		return sanitize_text_field( $value );
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
