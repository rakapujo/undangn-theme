<?php
/**
 * Theme supports & image sizes registration.
 *
 * @package Mengundang\Theme\Setup
 */

declare(strict_types=1);

namespace Mengundang\Theme\Setup;

defined( 'ABSPATH' ) || exit;

/**
 * Registrasi add_theme_support, image sizes, dan text domain.
 *
 * Image sizes mengikuti CLAUDE.md Bagian 5.7:
 * - mgu-thumb 400x400 crop  → thumbnail dashboard
 * - mgu-medium 800x600      → galeri grid
 * - mgu-large 1600x1200     → galeri full / hero
 * - mgu-og 1200x630 crop    → OG image share sosmed
 */
final class ThemeSupports {

	/**
	 * Hook handler untuk after_setup_theme.
	 *
	 * @return void
	 */
	public function register(): void {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support(
			'html5',
			array( 'search-form', 'gallery', 'caption', 'script', 'style' )
		);

		$this->registerImageSizes();
		$this->loadTextDomain();
	}

	/**
	 * Daftarkan custom image sizes (CLAUDE.md Bagian 5.7).
	 *
	 * @return void
	 */
	private function registerImageSizes(): void {
		add_image_size( 'mgu-thumb', 400, 400, true );
		add_image_size( 'mgu-medium', 800, 600, false );
		add_image_size( 'mgu-large', 1600, 1200, false );
		add_image_size( 'mgu-og', 1200, 630, true );
	}

	/**
	 * Load text domain undangan.
	 *
	 * @return void
	 */
	private function loadTextDomain(): void {
		load_theme_textdomain( 'undangan', MGU_THEME_DIR . '/languages' );
	}
}
