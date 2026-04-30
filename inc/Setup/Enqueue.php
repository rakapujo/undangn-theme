<?php
/**
 * Frontend asset enqueue.
 *
 * @package Mengundang\Theme\Setup
 */

declare(strict_types=1);

namespace Mengundang\Theme\Setup;

defined( 'ABSPATH' ) || exit;

/**
 * Asset enqueue handler.
 *
 * Implementasi penuh menunggu build chain wp-scripts aktif (CLAUDE.md Bagian 6.4).
 * Sementara: hanya bootstrap kosong, asset di-load via theme.json + block patterns.
 */
final class Enqueue {

	/**
	 * Hook handler untuk wp_enqueue_scripts.
	 *
	 * @return void
	 */
	public function enqueueFrontend(): void {
		// Placeholder — implementasi conditional bundle akan ditambah saat src/ aktif.
	}
}
