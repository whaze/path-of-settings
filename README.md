# Path Of Settings

A modern options page builder for WordPress with React and object-oriented architecture.

## Description

Path Of Settings is a developer-friendly plugin that allows you to easily create custom settings pages in WordPress. It provides a simple API to register pages, add fields, and retrieve settings values. The plugin uses React for the admin interface, providing a modern and responsive user experience.

## Features

- Simple API for creating settings pages
- Support for various field types (text, textarea, select, checkbox, etc.)
- Modern React-based admin interface
- Object-oriented architecture
- Clean separation of concerns
- Custom REST API endpoints for handling settings data
- Lightweight and performant

## Installation

1. Download the plugin ZIP file
2. Upload it to your WordPress site
3. Activate the plugin through the WordPress admin interface

## Requirements

- PHP 7.4 or higher
- WordPress 5.8 or higher

## Usage

### Creating a Settings Page

To create a settings page, use the `pos_register_page` function inside the `pos_register_pages` action hook:

```php
/**
 * Register a custom options page
 */
add_action('pos_register_pages', function() {
    // Create a new page
    $page = pos_register_page('my-settings', [
        'title' => __('My Custom Settings', 'my-plugin'),
        'menu_title' => __('My Settings', 'my-plugin'),
        'capability' => 'manage_options',
    ]);
    
    // Add fields to the page
    pos_add_field('my-settings', 'text', 'site_title', [
        'label' => __('Site Title', 'my-plugin'),
        'description' => __('Enter your site title', 'my-plugin'),
        'default' => get_bloginfo('name'),
        'required' => true,
    ]);
    
    pos_add_field('my-settings', 'textarea', 'site_description', [
        'label' => __('Site Description', 'my-plugin'),
        'description' => __('Enter your site description', 'my-plugin'),
        'default' => get_bloginfo('description'),
    ]);
    
    pos_add_field('my-settings', 'select', 'color_scheme', [
        'label' => __('Color Scheme', 'my-plugin'),
        'description' => __('Select your color scheme', 'my-plugin'),
        'default' => 'light',
        'options' => [
            'light' => __('Light', 'my-plugin'),
            'dark' => __('Dark', 'my-plugin'),
            'custom' => __('Custom', 'my-plugin'),
        ],
    ]);
    
    pos_add_field('my-settings', 'checkbox', 'enable_feature', [
        'label' => __('Enable Feature', 'my-plugin'),
        'description' => __('Enable this awesome feature', 'my-plugin'),
        'default' => true,
    ]);
});
```

### Retrieving Settings Values

To retrieve a single setting value:

```php
// Get a setting value
$siteTitle = pos_get_setting('my-settings', 'site_title', 'Default Title');
```

To retrieve all settings for a page:

```php
// Get all settings for a page
$allSettings = pos_get_settings('my-settings');
```

## API Reference

### Functions

#### `pos_register_page($id, $args)`

Registers a new settings page.

**Parameters:**
- `$id` (string): Unique identifier for the page
- `$args` (array): Page arguments
  - `title` (string): Page title
  - `menu_title` (string): Menu title
  - `capability` (string): Required capability to access the page (default: 'manage_options')

**Returns:** Page object

#### `pos_add_field($pageId, $type, $id, $args)`

Adds a field to a settings page.

**Parameters:**
- `$pageId` (string): Page ID
- `$type` (string): Field type (text, textarea, select, checkbox)
- `$id` (string): Field ID
- `$args` (array): Field arguments
  - `label` (string): Field label
  - `description` (string): Field description
  - `default` (mixed): Default value
  - `required` (bool): Whether the field is required
  - `options` (array): Options for select fields (key-value pairs)
  - Additional arguments specific to field types

**Returns:** Field object

#### `pos_get_setting($pageId, $fieldId, $default = null)`

Gets a setting value.

**Parameters:**
- `$pageId` (string): Page ID
- `$fieldId` (string): Field ID
- `$default` (mixed): Default value if setting is not found

**Returns:** Setting value or default

#### `pos_get_settings($pageId)`

Gets all settings for a page.

**Parameters:**
- `$pageId` (string): Page ID

**Returns:** Array of settings

## Field Types

### Text Field

```php
pos_add_field('page-id', 'text', 'field-id', [
    'label' => 'Text Field',
    'description' => 'Description',
    'default' => 'Default value',
    'placeholder' => 'Placeholder text',
    'required' => true,
]);
```

### Textarea Field

```php
pos_add_field('page-id', 'textarea', 'field-id', [
    'label' => 'Textarea Field',
    'description' => 'Description',
    'default' => 'Default value',
    'placeholder' => 'Placeholder text',
    'rows' => 5,
]);
```

### Select Field

```php
pos_add_field('page-id', 'select', 'field-id', [
    'label' => 'Select Field',
    'description' => 'Description',
    'default' => 'option1',
    'options' => [
        'option1' => 'Option 1',
        'option2' => 'Option 2',
        'option3' => 'Option 3',
    ],
]);
```

### Checkbox Field

```php
pos_add_field('page-id', 'checkbox', 'field-id', [
    'label' => 'Checkbox Field',
    'description' => 'Description',
    'default' => true,
]);
```

## License

This plugin is licensed under the GPL-2.0+ license.

## Credits

Developed by Jerome Buquet (Whaze)

