<?php
/**
 * Main theme bootstrap class.
 *
 * @package Mengundang\Theme
 */

declare(strict_types=1);

namespace Mengundang\Theme;

use Mengundang\Theme\PostTypes\UndanganCPT;
use Mengundang\Theme\Routing\RewriteRules;
use Mengundang\Theme\Routing\SubdomainRouter;
use Mengundang\Theme\Setup\Enqueue;
use Mengundang\Theme\Setup\ThemeSupports;

defined( 'ABSPATH' ) || exit;

/**
 * Theme orchestrator — instansiasi semua module dan registrasi hook.
 */
final class Theme {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Theme supports & image sizes module.
	 *
	 * @var ThemeSupports
	 */
	public readonly ThemeSupports $themeSupports;

	/**
	 * Asset enqueue module.
	 *
	 * @var Enqueue
	 */
	public readonly Enqueue $enqueue;

	/**
	 * Post type 'undangan'.
	 *
	 * @var UndanganCPT
	 */
	public readonly UndanganCPT $undanganCPT;

	/**
	 * Subdomain detector.
	 *
	 * @var SubdomainRouter
	 */
	public readonly SubdomainRouter $subdomainRouter;

	/**
	 * Custom routing handler.
	 *
	 * @var RewriteRules
	 */
	public readonly RewriteRules $rewriteRules;

	/**
	 * Private constructor — instansiasi sub-module.
	 */
	private function __construct() {
		$this->themeSupports   = new ThemeSupports();
		$this->enqueue         = new Enqueue();
		$this->undanganCPT     = new UndanganCPT();
		$this->subdomainRouter = new SubdomainRouter();
		$this->rewriteRules    = new RewriteRules( $this->subdomainRouter );
	}

	/**
	 * Boot theme — entry point dipanggil dari functions.php.
	 *
	 * @return void
	 */
	public static function boot(): void {
		if ( null !== self::$instance ) {
			return;
		}

		self::$instance = new self();
		self::$instance->registerHooks();
	}

	/**
	 * Akses singleton instance.
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Daftarkan WordPress hook ke handler module.
	 *
	 * @return void
	 */
	private function registerHooks(): void {
		add_action( 'after_setup_theme', array( $this->themeSupports, 'register' ) );
		add_action( 'wp_enqueue_scripts', array( $this->enqueue, 'enqueueFrontend' ) );
		add_action( 'init', array( $this->undanganCPT, 'register' ) );
		add_action( 'parse_request', array( $this->rewriteRules, 'handleParseRequest' ) );
	}
}
