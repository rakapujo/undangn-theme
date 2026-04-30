<?php
/**
 * Custom Post Type: Undangan.
 *
 * @package Mengundang\Theme\PostTypes
 */

declare(strict_types=1);

namespace Mengundang\Theme\PostTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Register CPT 'undangan' — entitas inti SaaS.
 *
 * Setiap row CPT ini mewakili satu undangan dengan slug unik (mis. 'tommy-rasta').
 * Slug yang sama bisa diakses dari beberapa subdomain berbeda — handling
 * dilakukan oleh SubdomainRouter (bukan oleh post type sendiri).
 */
final class UndanganCPT {

	/**
	 * Post type slug.
	 */
	public const POST_TYPE = 'undangan';

	/**
	 * Hook handler untuk init.
	 *
	 * @return void
	 */
	public function register(): void {
		register_post_type( self::POST_TYPE, $this->buildArgs() );
	}

	/**
	 * Argumen register_post_type.
	 *
	 * @return array<string, mixed>
	 */
	private function buildArgs(): array {
		return array(
			'labels'             => $this->buildLabels(),
			'description'        => __( 'Undangan digital yang dapat dibagikan via subdomain unik.', 'undangan' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => false,
			'show_in_admin_bar'  => true,
			'show_in_rest'       => true,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-heart',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array(
				'title',
				'editor',
				'thumbnail',
				'custom-fields',
				'revisions',
				'author',
			),
			'rewrite'            => array(
				'slug'       => 'undangan',
				'with_front' => false,
				'feeds'      => false,
				'pages'      => false,
			),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
		);
	}

	/**
	 * Label terjemahan Indonesia.
	 *
	 * @return array<string, string>
	 */
	private function buildLabels(): array {
		return array(
			'name'                  => _x( 'Undangan', 'post type general name', 'undangan' ),
			'singular_name'         => _x( 'Undangan', 'post type singular name', 'undangan' ),
			'menu_name'             => _x( 'Undangan', 'admin menu', 'undangan' ),
			'name_admin_bar'        => _x( 'Undangan', 'add new on admin bar', 'undangan' ),
			'add_new'               => __( 'Tambah Baru', 'undangan' ),
			'add_new_item'          => __( 'Tambah Undangan Baru', 'undangan' ),
			'new_item'              => __( 'Undangan Baru', 'undangan' ),
			'edit_item'             => __( 'Edit Undangan', 'undangan' ),
			'view_item'             => __( 'Lihat Undangan', 'undangan' ),
			'view_items'            => __( 'Lihat Undangan', 'undangan' ),
			'all_items'             => __( 'Semua Undangan', 'undangan' ),
			'search_items'          => __( 'Cari Undangan', 'undangan' ),
			'parent_item_colon'     => __( 'Undangan Induk:', 'undangan' ),
			'not_found'             => __( 'Tidak ada undangan ditemukan.', 'undangan' ),
			'not_found_in_trash'    => __( 'Tidak ada undangan di tempat sampah.', 'undangan' ),
			'featured_image'        => __( 'Foto Utama', 'undangan' ),
			'set_featured_image'    => __( 'Set foto utama', 'undangan' ),
			'remove_featured_image' => __( 'Hapus foto utama', 'undangan' ),
			'use_featured_image'    => __( 'Gunakan sebagai foto utama', 'undangan' ),
			'archives'              => __( 'Arsip Undangan', 'undangan' ),
			'insert_into_item'      => __( 'Sisipkan ke undangan', 'undangan' ),
			'uploaded_to_this_item' => __( 'Diunggah ke undangan ini', 'undangan' ),
			'filter_items_list'     => __( 'Filter daftar undangan', 'undangan' ),
			'items_list_navigation' => __( 'Navigasi daftar undangan', 'undangan' ),
			'items_list'            => __( 'Daftar undangan', 'undangan' ),
		);
	}
}
