<?php
/**
 * Undangan Theme — bootstrap.
 *
 * @package Mengundang\Theme
 */

declare(strict_types=1);

// Guard: cegah akses langsung.
defined( 'ABSPATH' ) || exit;

/**
 * Theme constants.
 */
define( 'MGU_THEME_VERSION', '0.1.0' );
define( 'MGU_THEME_DIR', __DIR__ );
define( 'MGU_THEME_URI', get_template_directory_uri() );

/**
 * Composer autoload (akan ada setelah `composer install`).
 * Untuk sekarang, file ini di-skip kalau belum ada.
 */
$mgu_autoload = MGU_THEME_DIR . '/vendor/autoload.php';
if ( is_readable( $mgu_autoload ) ) {
	require_once $mgu_autoload;
}
unset( $mgu_autoload );

/**
 * Bootstrap theme — akan dipindah ke Mengundang\Theme\Theme::boot()
 * setelah Composer + PSR-4 autoload aktif.
 */
add_action(
	'after_setup_theme',
	static function (): void {
		// Initial theme supports — akan dipindah ke inc/Setup/ThemeSupports.php nanti.
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'script', 'style' ) );
		add_theme_support( 'post-thumbnails' );

		load_theme_textdomain( 'undangan', MGU_THEME_DIR . '/languages' );
	}
);
