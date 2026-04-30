<?php
/**
 * Subdomain detection & validation.
 *
 * @package Mengundang\Theme\Routing
 */

declare(strict_types=1);

namespace Mengundang\Theme\Routing;

defined( 'ABSPATH' ) || exit;

/**
 * Pure utility class — parse host menjadi subdomain + root domain.
 *
 * Tidak punya side effect / hook. Bisa di-unit-test dengan inject host manual.
 */
final class SubdomainRouter {

	/**
	 * Subdomain yang TIDAK dianggap sebagai akun user.
	 *
	 * @var array<int, string>
	 */
	private const RESERVED_SUBDOMAINS = array( 'www', 'app', 'api', 'admin', 'dashboard' );

	/**
	 * Root domain yang valid (dev + prod).
	 *
	 * @var array<int, string>
	 */
	private readonly array $rootDomains;

	/**
	 * Constructor.
	 *
	 * @param array<int, string>|null $rootDomains Optional override (untuk testing).
	 */
	public function __construct( ?array $rootDomains = null ) {
		$this->rootDomains = $rootDomains ?? array( 'mengundang.mu', 'undangan.test' );
	}

	/**
	 * Host saat ini (lowercase, tanpa port).
	 *
	 * @return string Empty string kalau tidak ada (mis. CLI context).
	 */
	public function currentHost(): string {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$host = $_SERVER['HTTP_HOST'] ?? '';
		if ( ! is_string( $host ) ) {
			return '';
		}

		$host = strtolower( $host );
		$host = (string) preg_replace( '/:\d+$/', '', $host );

		return $host;
	}

	/**
	 * Ekstrak subdomain dari host saat ini.
	 *
	 * @return string|null Null kalau host = root domain, www, atau bukan subdomain valid.
	 */
	public function currentSubdomain(): ?string {
		return $this->subdomainFromHost( $this->currentHost() );
	}

	/**
	 * Ekstrak subdomain dari host yang diberikan.
	 *
	 * @param string $host Host (sudah lowercase, tanpa port).
	 * @return string|null
	 */
	public function subdomainFromHost( string $host ): ?string {
		if ( '' === $host ) {
			return null;
		}

		foreach ( $this->rootDomains as $root ) {
			if ( $host === $root ) {
				return null;
			}

			$suffix = '.' . $root;
			if ( ! str_ends_with( $host, $suffix ) ) {
				continue;
			}

			$sub = substr( $host, 0, strlen( $host ) - strlen( $suffix ) );

			// Reject nested subdomain (mis. "foo.bar.mengundang.mu") untuk MVP.
			if ( str_contains( $sub, '.' ) ) {
				return null;
			}

			if ( in_array( $sub, self::RESERVED_SUBDOMAINS, true ) ) {
				return null;
			}

			if ( ! $this->isValidSubdomain( $sub ) ) {
				return null;
			}

			return $sub;
		}

		return null;
	}

	/**
	 * Validasi format subdomain (RFC 1035 simplified).
	 *
	 * @param string $sub Subdomain candidate.
	 * @return bool
	 */
	public function isValidSubdomain( string $sub ): bool {
		if ( '' === $sub || strlen( $sub ) > 63 ) {
			return false;
		}

		return 1 === preg_match( '/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $sub );
	}

	/**
	 * Apakah request saat ini ada di subdomain user (bukan root domain).
	 *
	 * @return bool
	 */
	public function isOnUserSubdomain(): bool {
		return null !== $this->currentSubdomain();
	}
}
