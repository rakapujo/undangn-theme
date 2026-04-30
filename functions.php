<?php
/**
 * Undangan Theme — bootstrap.
 *
 * File ini sengaja dibuat tipis. Semua logika theme ada di inc/Theme.php
 * dan dimuat lewat Composer PSR-4 autoload (namespace Mengundang\Theme\).
 *
 * @package Mengundang\Theme
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

/**
 * Theme constants.
 */
define( 'MGU_THEME_VERSION', '0.1.0' );
define( 'MGU_THEME_DIR', __DIR__ );
define( 'MGU_THEME_URI', get_template_directory_uri() );

/**
 * Composer autoload — wajib ada untuk PSR-4 namespace.
 * Kalau tidak ada, theme tidak bisa boot.
 */
$mgu_autoload = MGU_THEME_DIR . '/vendor/autoload.php';
if ( ! is_readable( $mgu_autoload ) ) {
	add_action(
		'admin_notices',
		static function (): void {
			echo '<div class="notice notice-error"><p><strong>Undangan theme:</strong> ';
			echo esc_html__( 'Composer autoload tidak ditemukan. Jalankan `composer install` di root theme.', 'undangan' );
			echo '</p></div>';
		}
	);
	return;
}
require_once $mgu_autoload;
unset( $mgu_autoload );

/**
 * Boot theme.
 */
\Mengundang\Theme\Theme::boot();
