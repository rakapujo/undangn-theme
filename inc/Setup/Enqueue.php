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
 * Conditional bundle (CLAUDE.md Bagian 6.4):
 * - frontend = core, di-load di semua page undangan
 * - bundle fitur lain (gallery, countdown, music, rsvp) ditambah saat fitur diimplementasi
 */
final class Enqueue {

	/**
	 * Handle prefix.
	 */
	private const HANDLE_PREFIX = 'mgu-';

	/**
	 * Hook handler untuk wp_enqueue_scripts.
	 *
	 * @return void
	 */
	public function enqueueFrontend(): void {
		if ( is_admin() ) {
			return;
		}

		$this->enqueueCoreBundle();
	}

	/**
	 * Enqueue core bundle (frontend.js + frontend.css).
	 *
	 * @return void
	 */
	private function enqueueCoreBundle(): void {
		$asset = $this->loadAssetManifest( 'frontend' );

		if ( null === $asset ) {
			return;
		}

		$handle = self::HANDLE_PREFIX . 'frontend';

		wp_enqueue_script(
			$handle,
			MGU_THEME_URI . '/build/frontend.js',
			$asset['dependencies'],
			$asset['version'],
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		wp_enqueue_style(
			$handle,
			MGU_THEME_URI . '/build/frontend.css',
			array(),
			$asset['version']
		);
	}

	/**
	 * Load asset manifest yang di-generate oleh @wordpress/scripts.
	 *
	 * @param string $bundle Nama bundle (tanpa ekstensi).
	 * @return array{dependencies: array<int, string>, version: string}|null
	 */
	private function loadAssetManifest( string $bundle ): ?array {
		$path = MGU_THEME_DIR . '/build/' . $bundle . '.asset.php';

		if ( ! is_readable( $path ) ) {
			return null;
		}

		$asset = require $path;

		if ( ! is_array( $asset ) || ! isset( $asset['dependencies'], $asset['version'] ) ) {
			return null;
		}

		return $asset;
	}
}
