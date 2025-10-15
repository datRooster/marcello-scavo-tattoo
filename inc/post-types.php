<?php
/**
 * Custom Post Types and Taxonomies
 *
 * @package MarcelloScavoTattoo
 * @subpackage PostTypes
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom Post Types
 */
function marcello_scavo_custom_post_types() {
	// Portfolio/Tatuaggi.
	register_post_type(
		'portfolio',
		array(
			'labels'             => array(
				'name'               => __( 'Portfolio', 'marcello-scavo-tattoo' ),
				'singular_name'      => __( 'Lavoro Portfolio', 'marcello-scavo-tattoo' ),
				'add_new'            => __( 'Aggiungi Nuovo', 'marcello-scavo-tattoo' ),
				'add_new_item'       => __( 'Aggiungi Nuovo Lavoro', 'marcello-scavo-tattoo' ),
				'edit_item'          => __( 'Modifica Lavoro', 'marcello-scavo-tattoo' ),
				'new_item'           => __( 'Nuovo Lavoro', 'marcello-scavo-tattoo' ),
				'view_item'          => __( 'Visualizza Lavoro', 'marcello-scavo-tattoo' ),
				'search_items'       => __( 'Cerca Lavori', 'marcello-scavo-tattoo' ),
				'not_found'          => __( 'Nessun lavoro trovato', 'marcello-scavo-tattoo' ),
				'not_found_in_trash' => __( 'Nessun lavoro nel cestino', 'marcello-scavo-tattoo' ),
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'portfolio' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-art',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'show_in_rest'       => true,
		)
	);

	// Galleria Arte & Tatuaggi.
	register_post_type(
		'gallery',
		array(
			'labels'             => array(
				'name'               => __( 'Galleria', 'marcello-scavo-tattoo' ),
				'singular_name'      => __( 'Immagine Galleria', 'marcello-scavo-tattoo' ),
				'add_new'            => __( 'Aggiungi Nuova', 'marcello-scavo-tattoo' ),
				'add_new_item'       => __( 'Aggiungi Nuova Immagine', 'marcello-scavo-tattoo' ),
				'edit_item'          => __( 'Modifica Immagine', 'marcello-scavo-tattoo' ),
				'new_item'           => __( 'Nuova Immagine', 'marcello-scavo-tattoo' ),
				'view_item'          => __( 'Visualizza Immagine', 'marcello-scavo-tattoo' ),
				'search_items'       => __( 'Cerca Immagini', 'marcello-scavo-tattoo' ),
				'not_found'          => __( 'Nessuna immagine trovata', 'marcello-scavo-tattoo' ),
				'not_found_in_trash' => __( 'Nessuna immagine nel cestino', 'marcello-scavo-tattoo' ),
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'galleria' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 6,
			'menu_icon'          => 'dashicons-format-gallery',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'show_in_rest'       => true,
		)
	);

	// Prodotti Shop.
	register_post_type(
		'shop_product',
		array(
			'labels'             => array(
				'name'               => __( 'Prodotti Shop', 'marcello-scavo-tattoo' ),
				'singular_name'      => __( 'Prodotto', 'marcello-scavo-tattoo' ),
				'add_new'            => __( 'Aggiungi Prodotto', 'marcello-scavo-tattoo' ),
				'add_new_item'       => __( 'Aggiungi Nuovo Prodotto', 'marcello-scavo-tattoo' ),
				'edit_item'          => __( 'Modifica Prodotto', 'marcello-scavo-tattoo' ),
				'new_item'           => __( 'Nuovo Prodotto', 'marcello-scavo-tattoo' ),
				'view_item'          => __( 'Visualizza Prodotto', 'marcello-scavo-tattoo' ),
				'search_items'       => __( 'Cerca Prodotti', 'marcello-scavo-tattoo' ),
				'not_found'          => __( 'Nessun prodotto trovato', 'marcello-scavo-tattoo' ),
				'not_found_in_trash' => __( 'Nessun prodotto nel cestino', 'marcello-scavo-tattoo' ),
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'shop' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 7,
			'menu_icon'          => 'dashicons-store',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'show_in_rest'       => true,
		)
	);
}
add_action( 'init', 'marcello_scavo_custom_post_types' );

/**
 * Custom Taxonomies
 */
function marcello_scavo_custom_taxonomies() {
	// Portfolio Categories.
	register_taxonomy(
		'portfolio_category',
		'portfolio',
		array(
			'labels'             => array(
				'name'          => __( 'Categorie Portfolio', 'marcello-scavo-tattoo' ),
				'singular_name' => __( 'Categoria Portfolio', 'marcello-scavo-tattoo' ),
				'search_items'  => __( 'Cerca Categorie', 'marcello-scavo-tattoo' ),
				'all_items'     => __( 'Tutte le Categorie', 'marcello-scavo-tattoo' ),
				'edit_item'     => __( 'Modifica Categoria', 'marcello-scavo-tattoo' ),
				'update_item'   => __( 'Aggiorna Categoria', 'marcello-scavo-tattoo' ),
				'add_new_item'  => __( 'Aggiungi Nuova Categoria', 'marcello-scavo-tattoo' ),
				'new_item_name' => __( 'Nome Nuova Categoria', 'marcello-scavo-tattoo' ),
				'menu_name'     => __( 'Categorie', 'marcello-scavo-tattoo' ),
			),
			'hierarchical'       => true,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'portfolio-category' ),
			'show_in_rest'       => true,
		)
	);

	// Portfolio Tags.
	register_taxonomy(
		'portfolio_tag',
		'portfolio',
		array(
			'labels'             => array(
				'name'          => __( 'Tag Portfolio', 'marcello-scavo-tattoo' ),
				'singular_name' => __( 'Tag Portfolio', 'marcello-scavo-tattoo' ),
				'search_items'  => __( 'Cerca Tag', 'marcello-scavo-tattoo' ),
				'all_items'     => __( 'Tutti i Tag', 'marcello-scavo-tattoo' ),
				'edit_item'     => __( 'Modifica Tag', 'marcello-scavo-tattoo' ),
				'update_item'   => __( 'Aggiorna Tag', 'marcello-scavo-tattoo' ),
				'add_new_item'  => __( 'Aggiungi Nuovo Tag', 'marcello-scavo-tattoo' ),
				'new_item_name' => __( 'Nome Nuovo Tag', 'marcello-scavo-tattoo' ),
				'menu_name'     => __( 'Tag', 'marcello-scavo-tattoo' ),
			),
			'hierarchical'       => false,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'portfolio-tag' ),
			'show_in_rest'       => true,
		)
	);

	// Gallery Categories.
	register_taxonomy(
		'gallery_category',
		'gallery',
		array(
			'labels'             => array(
				'name'          => __( 'Categorie Galleria', 'marcello-scavo-tattoo' ),
				'singular_name' => __( 'Categoria Galleria', 'marcello-scavo-tattoo' ),
				'search_items'  => __( 'Cerca Categorie', 'marcello-scavo-tattoo' ),
				'all_items'     => __( 'Tutte le Categorie', 'marcello-scavo-tattoo' ),
				'edit_item'     => __( 'Modifica Categoria', 'marcello-scavo-tattoo' ),
				'update_item'   => __( 'Aggiorna Categoria', 'marcello-scavo-tattoo' ),
				'add_new_item'  => __( 'Aggiungi Nuova Categoria', 'marcello-scavo-tattoo' ),
				'new_item_name' => __( 'Nome Nuova Categoria', 'marcello-scavo-tattoo' ),
				'menu_name'     => __( 'Categorie', 'marcello-scavo-tattoo' ),
			),
			'hierarchical'       => true,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'galleria-categoria' ),
			'show_in_rest'       => true,
		)
	);
}
add_action( 'init', 'marcello_scavo_custom_taxonomies' );

/**
 * Create default gallery categories
 */
function marcello_scavo_create_default_gallery_categories() {
	// Check if categories already exist.
	if ( get_option( 'marcello_gallery_categories_created' ) ) {
		return;
	}

	$default_categories = array(
		array(
			'name'        => 'Tatuaggi',
			'slug'        => 'tattoos',
			'description' => 'Collezione di tatuaggi realizzati',
		),
		array(
			'name'        => 'Pitture',
			'slug'        => 'paintings',
			'description' => 'Opere d\'arte su tela e carta',
		),
		array(
			'name'        => 'Disegni',
			'slug'        => 'drawings',
			'description' => 'Schizzi e disegni preparatori',
		),
		array(
			'name'        => 'Design',
			'slug'        => 'design',
			'description' => 'Lavori di graphic design',
		),
	);

	foreach ( $default_categories as $category ) {
		if ( ! term_exists( $category['slug'], 'gallery_category' ) ) {
			wp_insert_term(
				$category['name'],
				'gallery_category',
				array(
					'slug'        => $category['slug'],
					'description' => $category['description'],
				)
			);
		}
	}

	// Mark as created.
	update_option( 'marcello_gallery_categories_created', true );
}
add_action( 'init', 'marcello_scavo_create_default_gallery_categories' );
