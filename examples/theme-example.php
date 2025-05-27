<?php
/**
 * Exemple d'utilisation du package PathOfSettings dans un thème WordPress
 *
 * Ce fichier doit être inclus dans le functions.php de votre thème :
 * require_once get_template_directory() . '/examples/theme-example.php';
 *
 * Ou vous pouvez copier le contenu directement dans functions.php
 */

// Empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PathOfSettingsThemeExample {
	private static $instance = null;
	public static function getInstance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	private function __construct() {
		add_action( 'after_setup_theme', [ $this, 'init' ] );
	}
	public function init() {
		$autoloader = get_template_directory() . '/vendor/autoload.php';
		if ( file_exists( $autoloader ) ) {
			require_once $autoloader;
		} else {
			add_action( 'admin_notices', [ $this, 'composerNotice' ] );
			return;
		}
		if ( ! class_exists( '\PathOfSettings\PathOfSettings' ) ) {
			add_action( 'admin_notices', [ $this, 'packageNotice' ] );
			return;
		}
		\PathOfSettings\PathOfSettings::getInstance()->init(
			[
				'version' => wp_get_theme()->get( 'Version' ) ?: '1.0.0',
				'path'    => get_template_directory() . '/',
				'url'     => get_template_directory_uri() . '/',
				'file'    => get_template_directory() . '/style.css',
			]
		);
		add_action( 'pos_register_pages', [ $this, 'registerThemePages' ] );
		add_action( 'wp_head', [ $this, 'addCustomCSS' ] );
		add_action( 'wp_footer', [ $this, 'addCustomJS' ] );
	}
	public function composerNotice() {
		if ( current_user_can( 'edit_theme_options' ) ) {
			echo '<div class="notice notice-error"><p>';
			echo __( 'Thème: Veuillez exécuter "composer install" dans le répertoire du thème pour utiliser PathOfSettings.', 'textdomain' );
			echo '</p></div>';
		}
	}
	public function packageNotice() {
		if ( current_user_can( 'edit_theme_options' ) ) {
			echo '<div class="notice notice-error"><p>';
			echo __( 'Thème: Le package whaze/path-of-settings n\'est pas installé. Exécutez "composer require whaze/path-of-settings".', 'textdomain' );
			echo '</p></div>';
		}
	}
	public function registerThemePages() {
		pos_register_page(
			'theme-general',
			[
				'title'      => __( 'Options du thème', 'textdomain' ),
				'menu_title' => __( 'Thème', 'textdomain' ),
				'capability' => 'edit_theme_options',
			]
		);
		pos_add_field(
			'theme-general',
			'select',
			'layout',
			[
				'label'       => __( 'Mise en page', 'textdomain' ),
				'description' => __( 'Choisissez la mise en page par défaut', 'textdomain' ),
				'default'     => 'full-width',
				'options'     => [
					'full-width'    => __( 'Pleine largeur', 'textdomain' ),
					'boxed'         => __( 'Encadrée', 'textdomain' ),
					'sidebar-left'  => __( 'Barre latérale à gauche', 'textdomain' ),
					'sidebar-right' => __( 'Barre latérale à droite', 'textdomain' ),
				],
			]
		);
		pos_add_field(
			'theme-general',
			'select',
			'color_scheme',
			[
				'label'       => __( 'Schéma de couleurs', 'textdomain' ),
				'description' => __( 'Sélectionnez le schéma de couleurs du thème', 'textdomain' ),
				'default'     => 'default',
				'options'     => [
					'default' => __( 'Par défaut', 'textdomain' ),
					'dark'    => __( 'Sombre', 'textdomain' ),
					'light'   => __( 'Clair', 'textdomain' ),
					'blue'    => __( 'Bleu', 'textdomain' ),
					'green'   => __( 'Vert', 'textdomain' ),
					'custom'  => __( 'Personnalisé', 'textdomain' ),
				],
			]
		);
		pos_add_field(
			'theme-general',
			'text',
			'primary_color',
			[
				'label'       => __( 'Couleur principale', 'textdomain' ),
				'description' => __( 'Code couleur hexadécimal pour la couleur principale (ex: #ff0000)', 'textdomain' ),
				'placeholder' => '#000000',
			]
		);
		pos_add_field(
			'theme-general',
			'text',
			'secondary_color',
			[
				'label'       => __( 'Couleur secondaire', 'textdomain' ),
				'description' => __( 'Code couleur hexadécimal pour la couleur secondaire', 'textdomain' ),
				'placeholder' => '#ffffff',
			]
		);
		pos_add_field(
			'theme-general',
			'select',
			'font_family',
			[
				'label'       => __( 'Police de caractères', 'textdomain' ),
				'description' => __( 'Choisissez la police principale du site', 'textdomain' ),
				'default'     => 'system',
				'options'     => [
					'system'    => __( 'Police système', 'textdomain' ),
					'arial'     => 'Arial, sans-serif',
					'helvetica' => 'Helvetica, sans-serif',
					'georgia'   => 'Georgia, serif',
					'times'     => 'Times New Roman, serif',
					'roboto'    => 'Roboto (Google Fonts)',
					'open-sans' => 'Open Sans (Google Fonts)',
				],
			]
		);
		pos_add_field(
			'theme-general',
			'select',
			'font_size',
			[
				'label'       => __( 'Taille de police', 'textdomain' ),
				'description' => __( 'Taille de police de base', 'textdomain' ),
				'default'     => '16',
				'options'     => [
					'14' => '14px',
					'15' => '15px',
					'16' => '16px',
					'17' => '17px',
					'18' => '18px',
					'20' => '20px',
				],
			]
		);
		pos_add_field(
			'theme-general',
			'checkbox',
			'show_search',
			[
				'label'       => __( 'Afficher la recherche dans l\'en-tête', 'textdomain' ),
				'description' => __( 'Cochez pour afficher le formulaire de recherche', 'textdomain' ),
				'default'     => true,
			]
		);
		pos_add_field(
			'theme-general',
			'textarea',
			'footer_text',
			[
				'label'       => __( 'Texte du pied de page', 'textdomain' ),
				'description' => __( 'Texte à afficher dans le pied de page', 'textdomain' ),
				'default'     => sprintf( __( '© %s - Tous droits réservés', 'textdomain' ), date( 'Y' ) ),
				'rows'        => 3,
			]
		);
		pos_register_page(
			'theme-advanced',
			[
				'title'      => __( 'Options avancées du thème', 'textdomain' ),
				'menu_title' => __( 'Avancé', 'textdomain' ),
				'capability' => 'edit_theme_options',
			]
		);
		pos_add_field(
			'theme-advanced',
			'textarea',
			'custom_css',
			[
				'label'       => __( 'CSS personnalisé', 'textdomain' ),
				'description' => __( 'Ajoutez votre CSS personnalisé ici', 'textdomain' ),
				'placeholder' => '/* Votre CSS ici */',
				'rows'        => 10,
			]
		);
		pos_add_field(
			'theme-advanced',
			'textarea',
			'custom_js',
			[
				'label'       => __( 'JavaScript personnalisé', 'textdomain' ),
				'description' => __( 'Ajoutez votre JavaScript personnalisé ici (sans les balises script)', 'textdomain' ),
				'placeholder' => '// Votre JavaScript ici',
				'rows'        => 10,
			]
		);
		pos_add_field(
			'theme-advanced',
			'text',
			'google_analytics',
			[
				'label'       => __( 'ID Google Analytics', 'textdomain' ),
				'description' => __( 'Entrez votre ID de suivi Google Analytics (ex: G-XXXXXXXXXX)', 'textdomain' ),
				'placeholder' => 'G-XXXXXXXXXX',
			]
		);
		pos_add_field(
			'theme-advanced',
			'checkbox',
			'minify_css',
			[
				'label'       => __( 'Minifier le CSS', 'textdomain' ),
				'description' => __( 'Activer la minification du CSS pour améliorer les performances', 'textdomain' ),
				'default'     => false,
			]
		);
		pos_add_field(
			'theme-advanced',
			'checkbox',
			'enable_lazy_loading',
			[
				'label'       => __( 'Chargement paresseux des images', 'textdomain' ),
				'description' => __( 'Activer le lazy loading pour les images', 'textdomain' ),
				'default'     => true,
			]
		);
	}
	public function addCustomCSS() {
		$custom_css      = pos_get_setting( 'theme-advanced', 'custom_css', '' );
		$primary_color   = pos_get_setting( 'theme-general', 'primary_color', '' );
		$secondary_color = pos_get_setting( 'theme-general', 'secondary_color', '' );
		$font_family     = pos_get_setting( 'theme-general', 'font_family', 'system' );
		$font_size       = pos_get_setting( 'theme-general', 'font_size', '16' );

		if ( $custom_css || $primary_color || $secondary_color || $font_family !== 'system' ) {
			echo '<style id="theme-custom-css">';
			if ( $primary_color || $secondary_color ) {
				echo ':root {';
				if ( $primary_color ) {
					echo '--theme-primary-color: ' . esc_attr( $primary_color ) . ';';
				}
				if ( $secondary_color ) {
					echo '--theme-secondary-color: ' . esc_attr( $secondary_color ) . ';';
				}
				echo '}';
			}
			if ( $font_family !== 'system' ) {
				$font_stack = $this->getFontStack( $font_family );
				echo 'body { font-family: ' . $font_stack . '; font-size: ' . intval( $font_size ) . 'px; }';
			}
			if ( $custom_css ) {
				echo $custom_css;
			}
			echo '</style>';
		}

		$ga_id = pos_get_setting( 'theme-advanced', 'google_analytics', '' );
		if ( $ga_id ) {
			echo '<!-- Google Analytics -->';
			echo "<script async src='https://www.googletagmanager.com/gtag/js?id=" . esc_attr( $ga_id ) . "'></script>";
			echo '<script>';
			echo 'window.dataLayer = window.dataLayer || [];';
			echo 'function gtag(){dataLayer.push(arguments);}';
			echo "gtag('js', new Date());";
			echo "gtag('config', '" . esc_attr( $ga_id ) . "');";
			echo '</script>';
		}
	}
	public function addCustomJS() {
		$custom_js = pos_get_setting( 'theme-advanced', 'custom_js', '' );
		if ( $custom_js ) {
			echo '<script id="theme-custom-js">';
			echo $custom_js;
			echo '</script>';
		}
	}
	private function getFontStack( $font_family ) {
		$fonts = [
			'system'    => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
			'arial'     => 'Arial, Helvetica, sans-serif',
			'helvetica' => 'Helvetica, Arial, sans-serif',
			'georgia'   => 'Georgia, "Times New Roman", serif',
			'times'     => '"Times New Roman", Times, serif',
			'roboto'    => 'Roboto, Arial, sans-serif',
			'open-sans' => '"Open Sans", Arial, sans-serif',
		];
		return $fonts[ $font_family ] ?? $fonts['system'];
	}
}
PathOfSettingsThemeExample::getInstance();

// Fonctions utilitaires pour le thème

function theme_get_layout( $default = 'full-width' ) {
	return pos_get_setting( 'theme-general', 'layout', $default );
}
function theme_get_color_scheme( $default = 'default' ) {
	return pos_get_setting( 'theme-general', 'color_scheme', $default );
}
function theme_get_primary_color( $default = '' ) {
	return pos_get_setting( 'theme-general', 'primary_color', $default );
}
function theme_get_secondary_color( $default = '' ) {
	return pos_get_setting( 'theme-general', 'secondary_color', $default );
}
function theme_show_search() {
	return (bool) pos_get_setting( 'theme-general', 'show_search', true );
}
function theme_get_footer_text( $default = '' ) {
	if ( ! $default ) {
		$default = sprintf( __( '© %s - Tous droits réservés', 'textdomain' ), date( 'Y' ) );
	}
	return pos_get_setting( 'theme-general', 'footer_text', $default );
}
function theme_get_font_family( $default = 'system' ) {
	return pos_get_setting( 'theme-general', 'font_family', $default );
}
function theme_get_font_size( $default = '16' ) {
	return pos_get_setting( 'theme-general', 'font_size', $default );
}
function theme_get_custom_css( $default = '' ) {
	return pos_get_setting( 'theme-advanced', 'custom_css', $default );
}
function theme_get_custom_js( $default = '' ) {
	return pos_get_setting( 'theme-advanced', 'custom_js', $default );
}
function theme_get_google_analytics( $default = '' ) {
	return pos_get_setting( 'theme-advanced', 'google_analytics', $default );
}
function theme_is_minify_css_enabled() {
	return (bool) pos_get_setting( 'theme-advanced', 'minify_css', false );
}
function theme_is_lazy_loading_enabled() {
	return (bool) pos_get_setting( 'theme-advanced', 'enable_lazy_loading', true );
}
