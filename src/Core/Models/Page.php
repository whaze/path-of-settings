<?php

namespace PathOfSettings\Core\Models;

use PathOfSettings\Core\Contracts\PageInterface;
use PathOfSettings\Core\Contracts\FieldInterface;

/**
 * Settings page model implementation.
 *
 * Represents a settings page in the WordPress admin with associated fields.
 * Handles page configuration, field management, and data serialization.
 *
 * @package PathOfSettings\Core\Models
 * @since 1.0.0
 */
class Page implements PageInterface {

	/**
	 * Unique page identifier.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $id;

	/**
	 * Page configuration array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private array $config;

	/**
	 * Collection of fields registered to this page.
	 *
	 * @since 1.0.0
	 * @var array Array of FieldInterface objects
	 */
	private array $fields = [];

	/**
	 * Create a new settings page.
	 *
	 * @since 1.0.0
	 * @param string $id Unique page identifier
	 * @param array  $config Page configuration options
	 */
	public function __construct( string $id, array $config = [] ) {
		$this->id     = $id;
		$this->config = wp_parse_args(
			$config,
			[
				'title'       => '',
				'menu_title'  => '',
				'capability'  => 'manage_options',
				'parent_slug' => '',
				'icon'        => '',
				'position'    => null,
			]
		);
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
		return ! empty( $this->config['menu_title'] ) ? $this->config['menu_title'] : $this->getTitle();
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
	public function addField( FieldInterface $field ): self {
		$this->fields[ $field->getId() ] = $field;
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

		foreach ( $this->fields as $field ) {
			$fields[] = $field->toArray();
		}

		return [
			'id'         => $this->id,
			'title'      => $this->getTitle(),
			'menu_title' => $this->getMenuTitle(),
			'capability' => $this->getCapability(),
			'fields'     => $fields,
		];
	}
}
