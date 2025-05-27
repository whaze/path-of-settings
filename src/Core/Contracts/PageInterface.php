<?php

namespace PathOfSettings\Core\Contracts;

/**
 * Contract for settings page implementations in PathOfSettings.
 *
 * Defines the interface that all settings pages must implement to provide
 * consistent behavior for page registration, field management, and WordPress integration.
 *
 * @package PathOfSettings\Core\Contracts
 * @since 1.0.0
 */
interface PageInterface {

	/**
	 * Get the unique page identifier.
	 *
	 * @since 1.0.0
	 * @return string The page ID used in WordPress admin
	 */
	public function getId(): string;

	/**
	 * Get the page title displayed in browser title bar.
	 *
	 * @since 1.0.0
	 * @return string The page title
	 */
	public function getTitle(): string;

	/**
	 * Get the menu title displayed in WordPress admin menu.
	 *
	 * @since 1.0.0
	 * @return string The menu title
	 */
	public function getMenuTitle(): string;

	/**
	 * Get the required capability to access this page.
	 *
	 * @since 1.0.0
	 * @return string WordPress capability required to access the page
	 */
	public function getCapability(): string;

	/**
	 * Get the WordPress admin page hook for this page.
	 *
	 * @since 1.0.0
	 * @return string The WordPress page hook
	 */
	public function getHook(): string;

	/**
	 * Add a field to this settings page.
	 *
	 * @since 1.0.0
	 * @param FieldInterface $field The field to add
	 * @return self For method chaining
	 */
	public function addField( FieldInterface $field ): self;

	/**
	 * Get all fields registered to this page.
	 *
	 * @since 1.0.0
	 * @return array Array of FieldInterface objects indexed by field ID
	 */
	public function getFields(): array;

	/**
	 * Convert page to array representation for API responses.
	 *
	 * @since 1.0.0
	 * @return array Page data including ID, titles, capability, and fields
	 */
	public function toArray(): array;
}
