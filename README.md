# Path Of Settings

Un constructeur moderne de pages d'options pour WordPress avec React et architecture orientée objet.

## Description

Path Of Settings permet de créer facilement des pages d’options personnalisées pour WordPress, en plugin ou en thème, via Composer. Architecture OOP, UI React moderne, API simple.

---

## Installation

### Avec Composer

```bash
composer require whaze/path-of-settings
```

---

## Utilisation dans un plugin

```php
// Inclure l'autoloader Composer (en général déjà présent)
require_once __DIR__ . '/vendor/autoload.php';

add_action('plugins_loaded', function() {
    \PathOfSettings\PathOfSettings::getInstance()->init([
        'version' => '1.0.0',
        'path' => plugin_dir_path(__FILE__),
        'url' => plugin_dir_url(__FILE__),
        'file' => __FILE__,
    ]);
});

// Ensuite, dans le hook pos_register_pages :
add_action('pos_register_pages', function() {
    $page = pos_register_page('my-settings', [
        'title' => __('Mon réglage', 'mon-plugin'),
        'menu_title' => __('Réglages', 'mon-plugin'),
        'capability' => 'manage_options',
    ]);
    // Ajouter des champs...
    pos_add_field('my-settings', 'text', 'site_title', [
        'label' => __('Titre du site', 'mon-plugin'),
        'default' => get_bloginfo('name'),
    ]);
});
```

---

## Utilisation dans un thème

```php
require_once get_template_directory() . '/vendor/autoload.php';

add_action('after_setup_theme', function() {
    \PathOfSettings\PathOfSettings::getInstance()->init([
        'version' => wp_get_theme()->get('Version') ?: '1.0.0',
        'path' => get_template_directory() . '/',
        'url' => get_template_directory_uri() . '/',
        'file' => get_template_directory() . '/style.css',
    ]);
});
```
Voir un exemple détaillé dans `examples/theme-example.php` de ce dépôt.

---

## API

- `pos_register_page($id, $args)` : enregistre une page d'options.
- `pos_add_field($pageId, $type, $id, $args)` : ajoute un champ à une page.
- `pos_get_setting($pageId, $fieldId, $default = null)` : récupère la valeur d’un champ.
- `pos_get_settings($pageId)` : récupère toutes les valeurs d’une page.

---

## Exemples complets

Voir :
- [`examples/plugin-example.php`](examples/plugin-example.php)
- [`examples/theme-example.php`](examples/theme-example.php)

---

## Contribuer / Support

Issues : https://github.com/whaze/path-of-settings/issues

---

## Licence

GPL-2.0-or-later

Développé par [Jerome Buquet (Whaze)](https://whodunit.fr)
```

---

