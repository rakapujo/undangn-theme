<?php
/**
 * Hijack request parsing untuk subdomain routing.
 *
 * @package Mengundang\Theme\Routing
 */

declare(strict_types=1);

namespace Mengundang\Theme\Routing;

use Mengundang\Theme\PostTypes\UndanganCPT;

defined( 'ABSPATH' ) || exit;

/**
 * Translate URL `subdomain.domain/{slug}` → query CPT undangan.
 *
 * Strategi: hook `parse_request` (lebih awal dari WP_Query) — kalau request
 * datang dari subdomain user, override query_vars supaya path 1-segment
 * di-resolve sebagai post_type=undangan.
 *
 * Tidak butuh flush rewrite rules karena tidak menambah aturan rewrite.
 */
final class RewriteRules {

	/**
	 * Constructor.
	 *
	 * @param SubdomainRouter $router Subdomain detector.
	 */
	public function __construct(
		private readonly SubdomainRouter $router,
	) {}

	/**
	 * Hook handler untuk parse_request.
	 *
	 * @param \WP $wp WordPress request object.
	 * @return void
	 */
	public function handleParseRequest( \WP $wp ): void {
		if ( ! $this->router->isOnUserSubdomain() ) {
			return;
		}

		$path = isset( $wp->request ) ? trim( (string) $wp->request, '/' ) : '';

		// Subdomain root: biarkan WP handle (akan render homepage). Phase 2 bisa override.
		if ( '' === $path ) {
			return;
		}

		// Multi-segment path: tidak relevan untuk MVP (slug undangan = 1 segment).
		if ( str_contains( $path, '/' ) ) {
			return;
		}

		$slug = sanitize_title( $path );
		if ( '' === $slug ) {
			return;
		}

		// Override query: paksa lookup sebagai CPT undangan.
		$wp->query_vars = array(
			'post_type'            => UndanganCPT::POST_TYPE,
			'name'                 => $slug,
			UndanganCPT::POST_TYPE => $slug,
		);

		// Bersihkan matched rule supaya WP tidak bingung dengan rule lain.
		$wp->matched_rule  = '';
		$wp->matched_query = 'post_type=' . UndanganCPT::POST_TYPE . '&name=' . $slug;
	}
}
