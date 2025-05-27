# PathOfSettings

A modern, developer-friendly WordPress options page builder with React UI and object-oriented architecture.

[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![WordPress](https://img.shields.io/badge/WordPress-%3E%3D%205.8-21759B.svg)](https://wordpress.org/)
[![Packagist](https://img.shields.io/packagist/v/whaze/path-of-settings.svg)](https://packagist.org/packages/whaze/path-of-settings)

## Overview

PathOfSettings is a comprehensive package for creating custom settings pages in WordPress applications. It provides a clean, object-oriented API for registering pages and fields, with a modern React-based admin interface that integrates seamlessly with WordPress core components.

### Key Features

- **🚀 Ultra-Simple API**: One-line initialization, no configuration required
- **⚡ Plug & Play**: Built assets included, no build step needed
- **🎨 Modern UI**: React-powered interface using WordPress Gutenberg components
- **🏗️ Clean Architecture**: Object-oriented design with interfaces and services
- **🔌 Flexible Integration**: Works in both plugins and themes via Composer
- **🛡️ Type Safety**: Full PHP 7.4+ type hints and comprehensive PHPDoc
- **🌐 REST API**: Custom endpoints for secure settings management
- **📦 Extensible**: Support for custom field types
- **🌍 i18n Ready**: Full internationalization support

---

## Installation

### Via Composer (Recommended)

```bash
composer require whaze/path-of-settings
```

**That's it!** Built assets are included in the package, no additional build step required.

### Manual Installation

1. Download the latest release from [GitHub](https://github.com/whaze/path-of-settings/releases)
2. Extract to your plugin or theme directory
3. Include the autoloader in your code

---

## Quick Start

### 🚀 Ultra-Simple Integration

```php
<?php
// 1. Include Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// 2. Initialize PathOfSettings (one line!)
\PathOfSettings\PathOfSettings::getInstance()->init();

// 3. Register your settings
add_action('pos_register_pages', function() {
    // Create a settings page
    pos_register_page('my-settings', [
        'title' => __('My Settings', 'textdomain'),
        'menu_title' => __('Settings', 'textdomain'),
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

**That's it! 🎉** Your settings page is ready with a modern React interface.

### 🔌 Plugin Integration

```php
<?php
/**
 * Plugin Name: My Awesome Plugin
 */

// Include Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Initialize on plugin load
add_action('plugins_loaded', function() {
    \PathOfSettings\PathOfSettings::getInstance()->init();
});

// Register plugin settings
add_action('pos_register_pages', function() {
    pos_register_page('my-plugin-settings', [
        'title' => __('My Plugin Settings', 'my-plugin'),
        'capability' => 'manage_options',
    ]);
    
    pos_add_field('my-plugin-settings', 'text', 'api_key', [
        'label' => __('API Key', 'my-plugin'),
        'required' => true,
    ]);
    
    pos_add_field('my-plugin-settings', 'checkbox', 'enable_feature', [
        'label' => __('Enable Advanced Feature', 'my-plugin'),
        'default' => false,
    ]);
});

// Use settings in your plugin
function my_plugin_get_api_key() {
    return pos_get_setting('my-plugin-settings', 'api_key', '');
}
```

### 🎨 Theme Integration

```php
<?php
// In your theme's functions.php
require_once get_template_directory() . '/vendor/autoload.php';

// Initialize for theme
\PathOfSettings\PathOfSettings::getInstance()->init();

// Register theme options
add_action('pos_register_pages', function() {
    pos_register_page('theme-options', [
        'title' => __('Theme Options', 'my-theme'),
        'capability' => 'edit_theme_options',
    ]);
    
    pos_add_field('theme-options', 'text', 'primary_color', [
        'label' => __('Primary Color', 'my-theme'),
        'placeholder' => '#007cba',
    ]);
    
    pos_add_field('theme-options', 'textarea', 'custom_css', [
        'label' => __('Custom CSS', 'my-theme'),
        'rows' => 8,
    ]);
});

// Use in your theme
function get_theme_primary_color() {
    return pos_get_setting('theme-options', 'primary_color', '#007cba');
}
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
  - `menu_title` (string): Menu title (optional, defaults to title)
  - `capability` (string): Required capability (default: `manage_options`)

**Returns:** `PageInterface` object

**Example:**
```php
pos_register_page('my-settings', [
    'title' => 'My Settings Page',
    'menu_title' => 'Settings',
    'capability' => 'manage_options',
]);
```

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

**Example:**
```php
$api_key = pos_get_setting('my-settings', 'api_key', '');
$is_enabled = pos_get_setting('my-settings', 'enable_feature', false);
```

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
    'placeholder' => 'Enter your content here...',
]);
```

#### Select Field
```php
pos_add_field('page-id', 'select', 'field-id', [
    'label' => 'Choose Option',
    'description' => 'Select an option from the dropdown',
    'options' => [
        'option1' => 'Option 1',
        'option2' => 'Option 2',
        'option3' => 'Option 3',
    ],
    'default' => 'option1',
]);
```

#### Checkbox Field
```php
pos_add_field('page-id', 'checkbox', 'field-id', [
    'label' => 'Enable Feature',
    'description' => 'Check to enable this feature',
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

### Advanced Initialization

For advanced use cases, you can pass configuration options:

```php
\PathOfSettings\PathOfSettings::getInstance()->init([
    'version' => '2.1.0',  // Custom version
    'path' => plugin_dir_path(__FILE__),  // For custom textdomain loading
    'url' => plugin_dir_url(__FILE__),
    'file' => __FILE__,
]);
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
    if ($pageId === 'my-settings') {
        $settings['api_key'] = strtoupper($settings['api_key']);
    }
    return $settings;
}, 10, 2);

add_action('pos_after_save_settings', function($settings, $pageId) {
    // Do something after settings are saved
    if ($pageId === 'my-settings') {
        // Clear cache, send notification, etc.
    }
}, 10, 2);
```

### REST API

PathOfSettings automatically creates REST endpoints:

- `GET /wp-json/pos/v1/settings/{page-id}` - Retrieve settings
- `POST /wp-json/pos/v1/settings/{page-id}` - Update settings

**Example API usage:**
```javascript
// Get settings
fetch('/wp-json/pos/v1/settings/my-settings')
    .then(response => response.json())
    .then(settings => console.log(settings));

// Update settings
fetch('/wp-json/pos/v1/settings/my-settings', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': wpApiSettings.nonce
    },
    body: JSON.stringify({
        api_key: 'new-api-key',
        enable_feature: true
    })
});
```

---

## Examples

Complete working examples are available in the `/examples` directory:

- [`examples/plugin-example.php`](examples/plugin-example.php) - Full plugin implementation
- [`examples/theme-example.php`](examples/theme-example.php) - Complete theme integration

### Real-World Example: Contact Form Plugin

```php
<?php
/**
 * Plugin Name: Simple Contact Form
 */

require_once __DIR__ . '/vendor/autoload.php';

// Initialize PathOfSettings
add_action('plugins_loaded', function() {
    \PathOfSettings\PathOfSettings::getInstance()->init();
});

// Register contact form settings
add_action('pos_register_pages', function() {
    pos_register_page('contact-form-settings', [
        'title' => __('Contact Form Settings', 'simple-contact-form'),
        'menu_title' => __('Contact Form', 'simple-contact-form'),
    ]);
    
    // Email settings
    pos_add_field('contact-form-settings', 'text', 'recipient_email', [
        'label' => __('Recipient Email', 'simple-contact-form'),
        'description' => __('Email address to receive form submissions', 'simple-contact-form'),
        'required' => true,
        'placeholder' => 'admin@example.com',
    ]);
    
    pos_add_field('contact-form-settings', 'text', 'subject_prefix', [
        'label' => __('Email Subject Prefix', 'simple-contact-form'),
        'default' => '[Contact Form]',
        'placeholder' => '[Contact Form]',
    ]);
    
    // Form settings
    pos_add_field('contact-form-settings', 'checkbox', 'require_phone', [
        'label' => __('Require Phone Number', 'simple-contact-form'),
        'description' => __('Make phone number field required', 'simple-contact-form'),
        'default' => false,
    ]);
    
    pos_add_field('contact-form-settings', 'select', 'form_style', [
        'label' => __('Form Style', 'simple-contact-form'),
        'options' => [
            'default' => __('Default', 'simple-contact-form'),
            'modern' => __('Modern', 'simple-contact-form'),
            'minimal' => __('Minimal', 'simple-contact-form'),
        ],
        'default' => 'default',
    ]);
});

// Use settings in your plugin
function scf_get_recipient_email() {
    return pos_get_setting('contact-form-settings', 'recipient_email', get_admin_email());
}

function scf_is_phone_required() {
    return pos_get_setting('contact-form-settings', 'require_phone', false);
}
```

---

## Requirements

- **PHP**: 7.4 or higher
- **WordPress**: 5.8 or higher
- **Browser**: Modern browser with JavaScript enabled (for admin interface)

---

## Installation Troubleshooting

### Common Issues

#### Assets Not Loading
If the React interface doesn't appear:

1. Check browser console for JavaScript errors
2. Verify `build/` directory exists in the package
3. Enable `WP_DEBUG` to see detailed error messages

#### Permission Issues
If you can't access settings pages:

1. Verify user has required capability (`manage_options` by default)
2. Check if pages are registered correctly
3. Ensure `pos_register_pages` action is firing

#### Composer Issues
If Composer installation fails:

1. Update Composer: `composer self-update`
2. Clear cache: `composer clear-cache`
3. Try without cache: `composer install --no-cache`

---

## Development

### For Contributors

```bash
# Clone repository
git clone https://github.com/whaze/path-of-settings.git
cd path-of-settings

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Build assets for development
npm run start

# Build assets for production
npm run build
```

### Code Standards

```bash
# Check PHP code standards
composer run phpcs

# Fix PHP code standards
composer run phpcbf

# Check JavaScript standards
npm run lint:js

# Fix JavaScript standards
npm run lint:js:fix
```

### Testing

```bash
# Run JavaScript tests
npm run test:unit

# Format code
npm run format
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
- Test in both plugin and theme contexts

---

## Changelog

### [1.0.1] - 2025-05-27
- **Added**: Auto-detection of package assets path
- **Added**: Simplified initialization API
- **Added**: Better error handling and debugging
- **Improved**: Documentation with more examples
- **Fixed**: Asset loading in theme contexts

### [1.0.0] - 2025-05-27
- Initial release
- React-powered admin interface
- Support for text, textarea, select, and checkbox fields
- REST API endpoints
- Composer package support

---

## License

This project is licensed under the GPL-2.0-or-later License. See the [LICENSE](LICENSE) file for details.

---

## Support

- **Issues**: [GitHub Issues](https://github.com/whaze/path-of-settings/issues)
- **Documentation**: [GitHub Wiki](https://github.com/whaze/path-of-settings/wiki)
- **Discussions**: [GitHub Discussions](https://github.com/whaze/path-of-settings/discussions)

---

## Credits

Developed by [Jerome Buquet (Whaze)](https://whodunit.fr)

Built with ❤️ for the WordPress community.

### Special Thanks

- WordPress Core Team for the excellent Gutenberg components
- The Composer team for making PHP package management awesome
- All contributors and users of this package

---

## Roadmap

### Planned Features
- 🎨 Additional field types (color picker, media uploader, date picker)
- 🔗 Field groups and conditional logic
- 📊 Import/export functionality
- 🌐 Multi-site network support
- 🎯 Advanced validation rules
- 🔌 WordPress Customizer integration
- 📱 Better mobile responsive design

### Performance Improvements
- ⚡ Field lazy loading
- 💾 Advanced settings caching
- 📦 Asset optimization
- 🗄️ Database optimization

Want to contribute to any of these features? Check out our [contributing guide](#contributing)!
