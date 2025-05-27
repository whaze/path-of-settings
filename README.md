# PathOfSettings

A modern, developer-friendly WordPress options page builder with React UI and object-oriented architecture.

[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![WordPress](https://img.shields.io/badge/WordPress-%3E%3D%205.8-21759B.svg)](https://wordpress.org/)

## Overview

PathOfSettings is a comprehensive package for creating custom settings pages in WordPress applications. It provides a clean, object-oriented API for registering pages and fields, with a modern React-based admin interface that integrates seamlessly with WordPress core components.

### Key Features

- **Modern Architecture**: Clean OOP design with interfaces, registries, and services
- **React-Powered UI**: Built with WordPress Gutenberg components for native look and feel  
- **Flexible Integration**: Works in both plugins and themes via Composer
- **Type Safety**: Full PHP 7.4+ type hints and comprehensive PHPDoc
- **REST API**: Custom endpoints for secure settings management
- **Field Types**: Text, textarea, select, checkbox with extensible architecture
- **Developer Experience**: Simple procedural API with powerful underlying architecture

---

## Installation

### Via Composer (Recommended)

```bash
composer require whaze/path-of-settings
```

### Manual Installation

1. Download the latest release
2. Extract to your plugin or theme directory
3. Include the autoloader in your code

---

## Quick Start

### Plugin Integration

```php
<?php
// Include Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Initialize PathOfSettings
add_action('plugins_loaded', function() {
    \PathOfSettings\PathOfSettings::getInstance()->init([
        'version' => '1.0.0',
        'path' => plugin_dir_path(__FILE__),
        'url' => plugin_dir_url(__FILE__),
        'file' => __FILE__,
    ]);
});

// Register settings pages
add_action('pos_register_pages', function() {
    // Create a settings page
    pos_register_page('my-settings', [
        'title' => __('My Settings', 'textdomain'),
        'menu_title' => __('Settings', 'textdomain'),
        'capability' => 'manage_options',
    ]);
    
    // Add fields
    pos_add_field('my-settings', 'text', 'api_key', [
        'label' => __('API Key', 'textdomain'),
        'description' => __('Enter your API key', 'textdomain'),
        'required' => true,
    ]);
    
    pos_add_field('my-settings', 'select', 'mode', [
        'label' => __('Operation Mode', 'textdomain'),
        'options' => [
            'live' => __('Live', 'textdomain'),
            'test' => __('Test', 'textdomain'),
        ],
        'default' => 'test',
    ]);
});
```

### Theme Integration

```php
<?php
// In your theme's functions.php
require_once get_template_directory() . '/vendor/autoload.php';

add_action('after_setup_theme', function() {
    \PathOfSettings\PathOfSettings::getInstance()->init([
        'version' => wp_get_theme()->get('Version') ?: '1.0.0',
        'path' => get_template_directory() . '/',
        'url' => get_template_directory_uri() . '/',
        'file' => get_template_directory() . '/style.css',
    ]);
});

// Register theme options
add_action('pos_register_pages', function() {
    pos_register_page('theme-options', [
        'title' => __('Theme Options', 'textdomain'),
        'capability' => 'edit_theme_options',
    ]);
    
    pos_add_field('theme-options', 'text', 'primary_color', [
        'label' => __('Primary Color', 'textdomain'),
        'placeholder' => '#007cba',
    ]);
});
```

---

## API Reference

### Core Functions

#### `pos_register_page($id, $args)`

Register a new settings page.

**Parameters:**
- `$id` (string): Unique page identifier
- `$args` (array): Page configuration
  - `title` (string): Page title
  - `menu_title` (string): Menu title (optional)
  - `capability` (string): Required capability (default: `manage_options`)

**Returns:** `PageInterface` object

#### `pos_add_field($pageId, $type, $id, $args)`

Add a field to a settings page.

**Parameters:**
- `$pageId` (string): Target page ID
- `$type` (string): Field type (`text`, `textarea`, `select`, `checkbox`)
- `$id` (string): Unique field identifier
- `$args` (array): Field configuration
  - `label` (string): Field label
  - `description` (string): Help text (optional)
  - `default` (mixed): Default value (optional)
  - `required` (bool): Whether field is required (optional)
  - `placeholder` (string): Placeholder text (optional)
  - `options` (array): Options for select fields (optional)

**Returns:** `FieldInterface` object

#### `pos_get_setting($pageId, $fieldId, $default)`

Retrieve a setting value.

**Parameters:**
- `$pageId` (string): Page identifier
- `$fieldId` (string): Field identifier  
- `$default` (mixed): Default value if not found

**Returns:** Mixed setting value

#### `pos_get_settings($pageId)`

Retrieve all settings for a page.

**Parameters:**
- `$pageId` (string): Page identifier

**Returns:** Array of all page settings

### Field Types

#### Text Field
```php
pos_add_field('page-id', 'text', 'field-id', [
    'label' => 'Text Input',
    'description' => 'Enter some text',
    'placeholder' => 'Type here...',
    'required' => true,
]);
```

#### Textarea Field
```php
pos_add_field('page-id', 'textarea', 'field-id', [
    'label' => 'Long Text',
    'description' => 'Enter longer content',
    'rows' => 5,
]);
```

#### Select Field
```php
pos_add_field('page-id', 'select', 'field-id', [
    'label' => 'Choose Option',
    'options' => [
        'option1' => 'Option 1',
        'option2' => 'Option 2',
    ],
    'default' => 'option1',
]);
```

#### Checkbox Field
```php
pos_add_field('page-id', 'checkbox', 'field-id', [
    'label' => 'Enable Feature',
    'description' => 'Check to enable',
    'default' => false,
]);
```

---

## Advanced Usage

### Custom Field Types

Extend the package by creating custom field types:

```php
use PathOfSettings\Core\Contracts\FieldInterface;

class CustomField implements FieldInterface {
    // Implement required methods
    public function getId(): string { /* ... */ }
    public function getType(): string { return 'custom'; }
    // ... other methods
}

// Register the field type
add_action('init', function() {
    $registry = \PathOfSettings\Core\Registries\FieldsRegistry::getInstance();
    $registry->register('custom', CustomField::class);
});
```

### Hooks and Filters

#### Actions
- `pos_register_pages` - Register your settings pages
- `pos_after_save_settings` - Fired after settings are saved

#### Filters
- `pos_before_save_settings` - Filter settings before saving

```php
add_filter('pos_before_save_settings', function($settings, $pageId) {
    // Modify settings before saving
    return $settings;
}, 10, 2);
```

### REST API

PathOfSettings automatically creates REST endpoints:

- `GET /wp-json/pos/v1/settings/{page-id}` - Retrieve settings
- `POST /wp-json/pos/v1/settings/{page-id}` - Update settings

---

## Examples

Complete working examples are available in the `/examples` directory:

- [`examples/plugin-example.php`](examples/plugin-example.php) - Full plugin implementation
- [`examples/theme-example.php`](examples/theme-example.php) - Complete theme integration

---

## Requirements

- **PHP**: 7.4 or higher
- **WordPress**: 5.8 or higher
- **Node.js**: 14+ (for building assets)

---

## Development

### Building Assets

```bash
# Install dependencies
npm install

# Build for production
npm run build

# Development mode with watch
npm run start
```

### Code Standards

```bash
# Check PHP code standards
composer run phpcs

# Fix PHP code standards
composer run phpcbf
```

---

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

- Follow WordPress coding standards
- Add PHPDoc for all public methods
- Include unit tests for new features
- Update documentation for API changes

---

## License

This project is licensed under the GPL-2.0-or-later License. See the [LICENSE](LICENSE) file for details.

---

## Support

- **Issues**: [GitHub Issues](https://github.com/whaze/path-of-settings/issues)
- **Documentation**: [Wiki](https://github.com/whaze/path-of-settings/wiki)
- **Discussions**: [GitHub Discussions](https://github.com/whaze/path-of-settings/discussions)

---

## Credits

Developed by [Jerome Buquet (Whaze)](https://whodunit.fr)

Built with ❤️ for the WordPress community.
```

