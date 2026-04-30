<?php
/**
 * Auto-flush rewrite rules saat versi berubah.
 *
 * @package Mengundang\Theme\Setup
 */

declare(strict_types=1);

namespace Mengundang\Theme\Setup;

defined( 'ABSPATH' ) || exit;

/**
 * Hindari masalah "kena 404 di slug baru sampai user save permalinks manual".
 *
 * Strategi: simpan versi rewrite di wp_options. Setiap request `init`,
 * compare versi tersimpan dengan versi konstan. Kalau beda → flush sekali,
 * lalu update versi tersimpan supaya flush tidak terjadi lagi sampai
 * developer naikkan REWRITE_VERSION.
 *
 * Bump REWRITE_VERSION setiap kali:
 * - CPT rewrite slug berubah
 * - Add/remove rewrite rule custom
 * - SubdomainRouter logic mempengaruhi URL pattern
 */
final class RewriteFlush {

	/**
	 * Versi rewrite saat ini. Naikkan saat aturan berubah.
	 */
	private const REWRITE_VERSION = '1';

	/**
	 * Option key untuk simpan versi yang sudah di-flush.
	 */
	private const OPTION_KEY = 'mgu_rewrite_version';

	/**
	 * Hook handler — jalan di init priority 99 (setelah CPT register).
	 *
	 * @return void
	 */
	public function maybeFlush(): void {
		if ( get_option( self::OPTION_KEY ) === self::REWRITE_VERSION ) {
			return;
		}

		flush_rewrite_rules( false );
		update_option( self::OPTION_KEY, self::REWRITE_VERSION );
	}
}
