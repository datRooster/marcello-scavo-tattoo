<?php

/**
 * Marcello Scavo Tattoo Theme Functions
 * 
 * @package MarcelloScavoTattoo
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Stub functions for IDE compatibility
 * These functions are only defined if the actual plugins are not present
 */
if (!function_exists('pll_current_language')) {
	function pll_current_language()
	{
		return 'it'; // Default fallback
	}
}

if (!function_exists('pll_the_languages')) {
	function pll_the_languages($args = array())
	{
		// Stub function for IDE compatibility
		return '';
	}
}

if (!function_exists('icl_get_current_language')) {
	function icl_get_current_language()
	{
		return 'it'; // Default fallback
	}
}

if (!function_exists('icl_get_languages')) {
	function icl_get_languages($skip_missing = '')
	{
		return array(); // Empty array fallback
	}
}

/**
 * Theme Setup
 */
function marcello_scavo_theme_setup()
{
	// Add theme support
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('custom-logo');
	add_theme_support('custom-header');
	add_theme_support('custom-background');
	add_theme_support('customize-selective-refresh-widgets');
	add_theme_support('wp-block-styles');
	add_theme_support('align-wide');

	// HTML5 support
	add_theme_support('html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'script',
		'style'
	));

	// Register navigation menus
	register_nav_menus(array(
		'primary' => __('Menu Principale', 'marcello-scavo-tattoo'),
		'footer' => __('Menu Footer', 'marcello-scavo-tattoo'),
		'social' => __('Social Media', 'marcello-scavo-tattoo'),
	));

	// Image sizes
	add_image_size('portfolio-thumb', 400, 300, true);
	add_image_size('portfolio-large', 800, 600, true);
	add_image_size('hero-bg', 1920, 1080, true);

	// Load theme textdomain for translations
	load_theme_textdomain('marcello-scavo-tattoo', get_template_directory() . '/languages');

	// Hook for manual language switching support
	add_action('init', 'marcello_scavo_handle_language_switch');
}
add_action('after_setup_theme', 'marcello_scavo_theme_setup');

/**
 * Enqueue scripts and styles
 */
function marcello_scavo_scripts()
{
	// Get file modification time for cache busting
	$style_version = filemtime(get_template_directory() . '/style.css');
	$js_version = filemtime(get_template_directory() . '/assets/js/main.js');

	// Enqueue CSS with cache busting
	wp_enqueue_style('marcello-scavo-style', get_stylesheet_uri(), array(), $style_version);

	// Additional components CSS
	if (file_exists(get_template_directory() . '/assets/css/components.css')) {
		$components_version = filemtime(get_template_directory() . '/assets/css/components.css');
		wp_enqueue_style('marcello-scavo-components', get_template_directory_uri() . '/assets/css/components.css', array('marcello-scavo-style'), $components_version);
	}

	// Google Fonts (updated for Poppins + Figtree)
	wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Figtree:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

	// Font Awesome for icons - using multiple CDN sources for reliability
	wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');

	// Backup Font Awesome from jsDelivr CDN
	wp_enqueue_style('font-awesome-backup', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css', array(), '6.4.0');

	// 3D Gallery CSS per categorie specifiche
	if (is_tax('portfolio_category')) {
		$current_term = get_queried_object();
		$target_categories = array('illustrazioni', 'disegni', 'quadri', 'arte', 'paintings', 'drawings');

		if (in_array($current_term->slug, $target_categories)) {
			if (file_exists(get_template_directory() . '/assets/css/3d-gallery.css')) {
				$gallery_css_version = filemtime(get_template_directory() . '/assets/css/3d-gallery.css');
				wp_enqueue_style('marcello-scavo-3d-gallery', get_template_directory_uri() . '/assets/css/3d-gallery.css', array('marcello-scavo-style'), $gallery_css_version);
			}
		}
	}

	// Add integrity check for Font Awesome
	add_filter('style_loader_tag', function ($html, $handle) {
		if ($handle === 'font-awesome') {
			$html = str_replace("rel='stylesheet'", "rel='stylesheet' crossorigin='anonymous'", $html);
		}
		if ($handle === 'font-awesome-backup') {
			$html = str_replace("rel='stylesheet'", "rel='stylesheet' crossorigin='anonymous'", $html);
		}
		return $html;
	}, 10, 2);

	// Enqueue JavaScript with cache busting
	wp_enqueue_script('marcello-scavo-script', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), time(), true);

	// Enqueue Portfolio Slider JavaScript
	wp_enqueue_script('portfolio-slider', get_template_directory_uri() . '/assets/js/portfolio-slider.js', array(), time(), true);

	// Enqueue 3D Gallery JavaScript per categorie specifiche
	if (is_tax('portfolio_category')) {
		$current_term = get_queried_object();
		$target_categories = array('illustrazioni', 'disegni', 'quadri', 'arte', 'paintings', 'drawings');

		if (in_array($current_term->slug, $target_categories)) {
			if (file_exists(get_template_directory() . '/assets/js/3d-gallery.js')) {
				$gallery_js_version = filemtime(get_template_directory() . '/assets/js/3d-gallery.js');
				wp_enqueue_script('marcello-scavo-3d-gallery', get_template_directory_uri() . '/assets/js/3d-gallery.js', array('jquery'), $gallery_js_version, true);
			}
		}
	}

	// Enqueue Instafeed.js from CDN for the Instagram widget
	wp_enqueue_script('instafeed', 'https://unpkg.com/instafeed.js@2.0.0-rc2/dist/instafeed.min.js', array('jquery'), '2.0.0', true);

	// Localize script for AJAX
	wp_localize_script('marcello-scavo-script', 'ajax_object', array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('marcello_scavo_nonce')
	));

	// Add inline script to check Font Awesome loading
	wp_add_inline_script('marcello-scavo-script', '
        jQuery(document).ready(function($) {
            // Check if Font Awesome has loaded after a short delay
            setTimeout(function() {
                var testIcon = $("<i class=\"fas fa-paint-brush\" style=\"position:absolute;top:-9999px;left:-9999px;\"></i>");
                $("body").append(testIcon);
                
                // Check if the icon has the expected font-family
                var fontFamily = testIcon.css("font-family");
                var hasFontAwesome = fontFamily && (
                    fontFamily.indexOf("Font Awesome") !== -1 || 
                    fontFamily.indexOf("FontAwesome") !== -1
                );
                
                testIcon.remove();
                
                if (!hasFontAwesome) {
                    console.log("Font Awesome not detected, applying fallbacks");
                    $("body").addClass("font-awesome-fallback");
                    
                    // Apply specific fallbacks
                    $(".fa-paint-brush").html("🎨");
                    $(".fa-pen-nib").html("✒️");
                    $(".fa-phone").html("📞");
                    $(".fa-envelope").html("✉️");
                    $(".fa-map-marker-alt").html("📍");
                    $(".fa-arrow-right").html("→");
                    $(".fa-instagram").html("📷");
                    $(".fa-facebook").html("🅵");
                    $(".fa-tiktok").html("🎵");
                    $(".fa-youtube").html("▶️");
                }
            }, 1000);
        });
    ');
}
add_action('wp_enqueue_scripts', 'marcello_scavo_scripts');

/**
 * Force cache refresh for development
 * This adds cache-busting headers to prevent browser caching during development
 */
function marcello_scavo_no_cache_headers()
{
	if (is_user_logged_in() && current_user_can('manage_options')) {
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');
	}
}
add_action('send_headers', 'marcello_scavo_no_cache_headers');

/**
 * Add version parameter to all CSS files to prevent caching
 */
function marcello_scavo_add_css_version($src, $handle)
{
	if (strpos($src, get_template_directory_uri()) !== false) {
		$src = add_query_arg('v', time(), $src);
	}
	return $src;
}
add_filter('style_loader_src', 'marcello_scavo_add_css_version', 9999, 2);

/**
 * Register Widget Areas
 */
function marcello_scavo_widgets_init()
{
	register_sidebar(array(
		'name'          => __('Sidebar Principale', 'marcello-scavo-tattoo'),
		'id'            => 'primary-sidebar',
		'description'   => __('Aggiungi widget qui per apparire nella sidebar.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Footer Colonna 1', 'marcello-scavo-tattoo'),
		'id'            => 'footer-1',
		'description'   => __('Widget per la prima colonna del footer.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Footer Colonna 2', 'marcello-scavo-tattoo'),
		'id'            => 'footer-2',
		'description'   => __('Widget per la seconda colonna del footer.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Footer Colonna 3', 'marcello-scavo-tattoo'),
		'id'            => 'footer-3',
		'description'   => __('Widget per la terza colonna del footer.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Social Media', 'marcello-scavo-tattoo'),
		'id'            => 'social-media',
		'description'   => __('Area widget per contenuti social media (Instagram feed, etc.).', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget social-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title social-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Recensioni Clienti', 'marcello-scavo-tattoo'),
		'id'            => 'reviews-section',
		'description'   => __('Area widget per recensioni Google e testimonianze clienti.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget reviews-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title reviews-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Prenotazione Booking', 'marcello-scavo-tattoo'),
		'id'            => 'booking-section',
		'description'   => __('Area widget per sistemi di prenotazione (Bookly, form contatti, etc.).', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget booking-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title booking-title">',
		'after_title'   => '</h2>',
	));

	register_sidebar(array(
		'name'          => __('Galleria Artistica', 'marcello-scavo-tattoo'),
		'id'            => 'gallery-showcase',
		'description'   => __('Area widget per mostrare la galleria dei lavori artistici e tatuaggi.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget gallery-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title gallery-title">',
		'after_title'   => '</h2>',
	));

	register_sidebar(array(
		'name'          => __('Uploader Sicuro', 'marcello-scavo-tattoo'),
		'id'            => 'secure-uploader',
		'description'   => __('Area widget per il caricamento sicuro di immagini nei media WordPress.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget uploader-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title uploader-title">',
		'after_title'   => '</h2>',
	));

	register_sidebar(array(
		'name'          => __('📧 Sezione Contatti', 'marcello-scavo-tattoo'),
		'id'            => 'contact-section',
		'description'   => __('Area widget per personalizzare la sezione "Contattaci per informazioni". Include contenuto di fallback accattivante.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget contact-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="contact-widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('🗺️ Mappa Localizzazione', 'marcello-scavo-tattoo'),
		'id'            => 'location-map',
		'description'   => __('Area widget per personalizzare la mappa di localizzazione aziendale. Supporta Google Maps, OpenStreetMap e mappe personalizzate.', 'marcello-scavo-tattoo'),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	));
}
add_action('widgets_init', 'marcello_scavo_widgets_init');

/**
 * Custom Post Types
 */
function marcello_scavo_custom_post_types()
{
	// Portfolio/Tatuaggi
	register_post_type('portfolio', array(
		'labels' => array(
			'name' => __('Portfolio', 'marcello-scavo-tattoo'),
			'singular_name' => __('Lavoro Portfolio', 'marcello-scavo-tattoo'),
			'add_new' => __('Aggiungi Nuovo', 'marcello-scavo-tattoo'),
			'add_new_item' => __('Aggiungi Nuovo Lavoro', 'marcello-scavo-tattoo'),
			'edit_item' => __('Modifica Lavoro', 'marcello-scavo-tattoo'),
			'new_item' => __('Nuovo Lavoro', 'marcello-scavo-tattoo'),
			'view_item' => __('Visualizza Lavoro', 'marcello-scavo-tattoo'),
			'search_items' => __('Cerca Lavori', 'marcello-scavo-tattoo'),
			'not_found' => __('Nessun lavoro trovato', 'marcello-scavo-tattoo'),
			'not_found_in_trash' => __('Nessun lavoro nel cestino', 'marcello-scavo-tattoo'),
		),
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'portfolio'),
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-art',
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
		'show_in_rest' => true,
	));

	// Galleria Arte & Tatuaggi
	register_post_type('gallery', array(
		'labels' => array(
			'name' => __('Galleria', 'marcello-scavo-tattoo'),
			'singular_name' => __('Immagine Galleria', 'marcello-scavo-tattoo'),
			'add_new' => __('Aggiungi Nuova', 'marcello-scavo-tattoo'),
			'add_new_item' => __('Aggiungi Nuova Immagine', 'marcello-scavo-tattoo'),
			'edit_item' => __('Modifica Immagine', 'marcello-scavo-tattoo'),
			'new_item' => __('Nuova Immagine', 'marcello-scavo-tattoo'),
			'view_item' => __('Visualizza Immagine', 'marcello-scavo-tattoo'),
			'search_items' => __('Cerca Immagini', 'marcello-scavo-tattoo'),
			'not_found' => __('Nessuna immagine trovata', 'marcello-scavo-tattoo'),
			'not_found_in_trash' => __('Nessuna immagine nel cestino', 'marcello-scavo-tattoo'),
		),
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'galleria'),
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => 6,
		'menu_icon' => 'dashicons-format-gallery',
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
		'show_in_rest' => true,
	));

	// Note: Booking system now handled by Bookly plugin
	// Custom post type 'booking' removed in favor of Bookly integration

	// Prodotti Shop
	register_post_type('shop_product', array(
		'labels' => array(
			'name' => __('Prodotti Shop', 'marcello-scavo-tattoo'),
			'singular_name' => __('Prodotto', 'marcello-scavo-tattoo'),
			'add_new' => __('Aggiungi Prodotto', 'marcello-scavo-tattoo'),
			'add_new_item' => __('Aggiungi Nuovo Prodotto', 'marcello-scavo-tattoo'),
			'edit_item' => __('Modifica Prodotto', 'marcello-scavo-tattoo'),
			'new_item' => __('Nuovo Prodotto', 'marcello-scavo-tattoo'),
			'view_item' => __('Visualizza Prodotto', 'marcello-scavo-tattoo'),
			'search_items' => __('Cerca Prodotti', 'marcello-scavo-tattoo'),
			'not_found' => __('Nessun prodotto trovato', 'marcello-scavo-tattoo'),
			'not_found_in_trash' => __('Nessun prodotto nel cestino', 'marcello-scavo-tattoo'),
		),
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'shop'),
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => 7,
		'menu_icon' => 'dashicons-store',
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
		'show_in_rest' => true,
	));
}
add_action('init', 'marcello_scavo_custom_post_types');

/**
 * Custom Taxonomies
 */
function marcello_scavo_custom_taxonomies()
{
	// Portfolio Categories
	register_taxonomy('portfolio_category', 'portfolio', array(
		'labels' => array(
			'name' => __('Categorie Portfolio', 'marcello-scavo-tattoo'),
			'singular_name' => __('Categoria Portfolio', 'marcello-scavo-tattoo'),
			'search_items' => __('Cerca Categorie', 'marcello-scavo-tattoo'),
			'all_items' => __('Tutte le Categorie', 'marcello-scavo-tattoo'),
			'edit_item' => __('Modifica Categoria', 'marcello-scavo-tattoo'),
			'update_item' => __('Aggiorna Categoria', 'marcello-scavo-tattoo'),
			'add_new_item' => __('Aggiungi Nuova Categoria', 'marcello-scavo-tattoo'),
			'new_item_name' => __('Nome Nuova Categoria', 'marcello-scavo-tattoo'),
			'menu_name' => __('Categorie', 'marcello-scavo-tattoo'),
		),
		'hierarchical' => true,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'portfolio-category'),
		'show_in_rest' => true,
	));

	// Portfolio Tags
	register_taxonomy('portfolio_tag', 'portfolio', array(
		'labels' => array(
			'name' => __('Tag Portfolio', 'marcello-scavo-tattoo'),
			'singular_name' => __('Tag Portfolio', 'marcello-scavo-tattoo'),
			'search_items' => __('Cerca Tag', 'marcello-scavo-tattoo'),
			'all_items' => __('Tutti i Tag', 'marcello-scavo-tattoo'),
			'edit_item' => __('Modifica Tag', 'marcello-scavo-tattoo'),
			'update_item' => __('Aggiorna Tag', 'marcello-scavo-tattoo'),
			'add_new_item' => __('Aggiungi Nuovo Tag', 'marcello-scavo-tattoo'),
			'new_item_name' => __('Nome Nuovo Tag', 'marcello-scavo-tattoo'),
			'menu_name' => __('Tag', 'marcello-scavo-tattoo'),
		),
		'hierarchical' => false,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'portfolio-tag'),
		'show_in_rest' => true,
	));

	// Gallery Categories
	register_taxonomy('gallery_category', 'gallery', array(
		'labels' => array(
			'name' => __('Categorie Galleria', 'marcello-scavo-tattoo'),
			'singular_name' => __('Categoria Galleria', 'marcello-scavo-tattoo'),
			'search_items' => __('Cerca Categorie', 'marcello-scavo-tattoo'),
			'all_items' => __('Tutte le Categorie', 'marcello-scavo-tattoo'),
			'edit_item' => __('Modifica Categoria', 'marcello-scavo-tattoo'),
			'update_item' => __('Aggiorna Categoria', 'marcello-scavo-tattoo'),
			'add_new_item' => __('Aggiungi Nuova Categoria', 'marcello-scavo-tattoo'),
			'new_item_name' => __('Nome Nuova Categoria', 'marcello-scavo-tattoo'),
			'menu_name' => __('Categorie', 'marcello-scavo-tattoo'),
		),
		'hierarchical' => true,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'galleria-categoria'),
		'show_in_rest' => true,
	));
}
add_action('init', 'marcello_scavo_custom_taxonomies');

/**
 * Create default gallery categories
 */
function marcello_scavo_create_default_gallery_categories()
{
	// Check if categories already exist
	if (get_option('marcello_gallery_categories_created')) {
		return;
	}

	$default_categories = array(
		array(
			'name' => 'Tatuaggi',
			'slug' => 'tattoos',
			'description' => 'Collezione di tatuaggi realizzati'
		),
		array(
			'name' => 'Pitture',
			'slug' => 'paintings',
			'description' => 'Opere d\'arte su tela e carta'
		),
		array(
			'name' => 'Disegni',
			'slug' => 'drawings',
			'description' => 'Schizzi e disegni preparatori'
		),
		array(
			'name' => 'Design',
			'slug' => 'design',
			'description' => 'Lavori di graphic design'
		)
	);

	foreach ($default_categories as $category) {
		if (!term_exists($category['slug'], 'gallery_category')) {
			wp_insert_term(
				$category['name'],
				'gallery_category',
				array(
					'slug' => $category['slug'],
					'description' => $category['description']
				)
			);
		}
	}

	// Mark as created
	update_option('marcello_gallery_categories_created', true);
}
add_action('init', 'marcello_scavo_create_default_gallery_categories');

/**
 * Add Custom Fields Support
 */
function marcello_scavo_add_meta_boxes()
{
	// Portfolio Meta Box
	add_meta_box(
		'portfolio_details',
		__('Dettagli Portfolio', 'marcello-scavo-tattoo'),
		'marcello_scavo_portfolio_meta_box_callback',
		'portfolio',
		'normal',
		'high'
	);

	// Gallery Meta Box
	add_meta_box(
		'gallery_details',
		__('Dettagli Galleria', 'marcello-scavo-tattoo'),
		'marcello_scavo_gallery_meta_box_callback',
		'gallery',
		'normal',
		'high'
	);

	// Booking Meta Box removed - using Bookly plugin instead

	// Shop Product Meta Box
	add_meta_box(
		'product_details',
		__('Dettagli Prodotto', 'marcello-scavo-tattoo'),
		'marcello_scavo_product_meta_box_callback',
		'shop_product',
		'normal',
		'high'
	);
}
add_action('add_meta_boxes', 'marcello_scavo_add_meta_boxes');

/**
 * Portfolio Meta Box Callback
 */
function marcello_scavo_portfolio_meta_box_callback($post)
{
	wp_nonce_field('save_portfolio_details', 'portfolio_meta_nonce');

	$client_name = get_post_meta($post->ID, '_portfolio_client_name', true);
	$project_date = get_post_meta($post->ID, '_portfolio_project_date', true);
	$project_location = get_post_meta($post->ID, '_portfolio_project_location', true);
	$project_type = get_post_meta($post->ID, '_portfolio_project_type', true);
	$project_duration = get_post_meta($post->ID, '_portfolio_project_duration', true);
	$gallery_images = get_post_meta($post->ID, '_portfolio_gallery', true);

?>
	<table class="form-table">
		<tr>
			<th><label for="portfolio_client_name"><?php _e('Nome Cliente', 'marcello-scavo-tattoo'); ?></label></th>
			<td><input type="text" id="portfolio_client_name" name="portfolio_client_name" value="<?php echo esc_attr($client_name); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th><label for="portfolio_project_date"><?php _e('Data Progetto', 'marcello-scavo-tattoo'); ?></label></th>
			<td><input type="date" id="portfolio_project_date" name="portfolio_project_date" value="<?php echo esc_attr($project_date); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th><label for="portfolio_project_location"><?php _e('Luogo', 'marcello-scavo-tattoo'); ?></label></th>
			<td>
				<select id="portfolio_project_location" name="portfolio_project_location" class="regular-text">
					<option value=""><?php _e('Seleziona Luogo', 'marcello-scavo-tattoo'); ?></option>
					<option value="milano" <?php selected($project_location, 'milano'); ?>><?php _e('Milano', 'marcello-scavo-tattoo'); ?></option>
					<option value="messina" <?php selected($project_location, 'messina'); ?>><?php _e('Messina', 'marcello-scavo-tattoo'); ?></option>
					<option value="altro" <?php selected($project_location, 'altro'); ?>><?php _e('Altro', 'marcello-scavo-tattoo'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="portfolio_project_type"><?php _e('Tipo Progetto', 'marcello-scavo-tattoo'); ?></label></th>
			<td>
				<select id="portfolio_project_type" name="portfolio_project_type" class="regular-text">
					<option value=""><?php _e('Seleziona Tipo', 'marcello-scavo-tattoo'); ?></option>
					<option value="tattoo" <?php selected($project_type, 'tattoo'); ?>><?php _e('Tatuaggio', 'marcello-scavo-tattoo'); ?></option>
					<option value="illustration" <?php selected($project_type, 'illustration'); ?>><?php _e('Illustrazione', 'marcello-scavo-tattoo'); ?></option>
					<option value="graphic_design" <?php selected($project_type, 'graphic_design'); ?>><?php _e('Grafica', 'marcello-scavo-tattoo'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="portfolio_project_duration"><?php _e('Durata (ore)', 'marcello-scavo-tattoo'); ?></label></th>
			<td><input type="number" id="portfolio_project_duration" name="portfolio_project_duration" value="<?php echo esc_attr($project_duration); ?>" class="small-text" min="0" step="0.5" /></td>
		</tr>
	</table>
<?php
}

/**
 * Gallery Meta Box Callback
 */
function marcello_scavo_gallery_meta_box_callback($post)
{
	wp_nonce_field('save_gallery_details', 'gallery_meta_nonce');

	$image_caption = get_post_meta($post->ID, '_gallery_image_caption', true);
	$image_alt_text = get_post_meta($post->ID, '_gallery_image_alt_text', true);
	$image_technique = get_post_meta($post->ID, '_gallery_image_technique', true);
	$image_dimensions = get_post_meta($post->ID, '_gallery_image_dimensions', true);
	$creation_date = get_post_meta($post->ID, '_gallery_creation_date', true);
	$featured_order = get_post_meta($post->ID, '_gallery_featured_order', true);
	$is_featured = get_post_meta($post->ID, '_gallery_is_featured', true);

?>
	<table class="form-table">
		<tr>
			<th><label for="gallery_image_caption"><?php _e('Didascalia Immagine', 'marcello-scavo-tattoo'); ?></label></th>
			<td>
				<textarea id="gallery_image_caption" name="gallery_image_caption" rows="3" class="large-text"><?php echo esc_textarea($image_caption); ?></textarea>
				<p class="description"><?php _e('Descrizione che apparirà sotto l\'immagine nella galleria.', 'marcello-scavo-tattoo'); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="gallery_image_alt_text"><?php _e('Testo Alternativo', 'marcello-scavo-tattoo'); ?></label></th>
			<td>
				<input type="text" id="gallery_image_alt_text" name="gallery_image_alt_text" value="<?php echo esc_attr($image_alt_text); ?>" class="regular-text" />
				<p class="description"><?php _e('Testo alternativo per l\'accessibilità.', 'marcello-scavo-tattoo'); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="gallery_image_technique"><?php _e('Tecnica', 'marcello-scavo-tattoo'); ?></label></th>
			<td>
				<select id="gallery_image_technique" name="gallery_image_technique" class="regular-text">
					<option value=""><?php _e('Seleziona Tecnica', 'marcello-scavo-tattoo'); ?></option>
					<option value="olio_su_tela" <?php selected($image_technique, 'olio_su_tela'); ?>><?php _e('Olio su tela', 'marcello-scavo-tattoo'); ?></option>
					<option value="acrilico" <?php selected($image_technique, 'acrilico'); ?>><?php _e('Acrilico', 'marcello-scavo-tattoo'); ?></option>
					<option value="disegno_matita" <?php selected($image_technique, 'disegno_matita'); ?>><?php _e('Disegno a matita', 'marcello-scavo-tattoo'); ?></option>
					<option value="tatuaggio_blackwork" <?php selected($image_technique, 'tatuaggio_blackwork'); ?>><?php _e('Tatuaggio Blackwork', 'marcello-scavo-tattoo'); ?></option>
					<option value="tatuaggio_realistico" <?php selected($image_technique, 'tatuaggio_realistico'); ?>><?php _e('Tatuaggio Realistico', 'marcello-scavo-tattoo'); ?></option>
					<option value="tatuaggio_geometrico" <?php selected($image_technique, 'tatuaggio_geometrico'); ?>><?php _e('Tatuaggio Geometrico', 'marcello-scavo-tattoo'); ?></option>
					<option value="watercolor" <?php selected($image_technique, 'watercolor'); ?>><?php _e('Acquerello', 'marcello-scavo-tattoo'); ?></option>
					<option value="misto" <?php selected($image_technique, 'misto'); ?>><?php _e('Tecnica mista', 'marcello-scavo-tattoo'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="gallery_image_dimensions"><?php _e('Dimensioni', 'marcello-scavo-tattoo'); ?></label></th>
			<td>
				<input type="text" id="gallery_image_dimensions" name="gallery_image_dimensions" value="<?php echo esc_attr($image_dimensions); ?>" class="regular-text" placeholder="es. 30x40 cm" />
				<p class="description"><?php _e('Dimensioni dell\'opera o area del tatuaggio.', 'marcello-scavo-tattoo'); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="gallery_creation_date"><?php _e('Data di Creazione', 'marcello-scavo-tattoo'); ?></label></th>
			<td><input type="date" id="gallery_creation_date" name="gallery_creation_date" value="<?php echo esc_attr($creation_date); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th><label for="gallery_is_featured"><?php _e('Immagine in Evidenza', 'marcello-scavo-tattoo'); ?></label></th>
			<td>
				<label>
					<input type="checkbox" id="gallery_is_featured" name="gallery_is_featured" value="1" <?php checked($is_featured, '1'); ?> />
					<?php _e('Mostra questa immagine in evidenza nella galleria', 'marcello-scavo-tattoo'); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th><label for="gallery_featured_order"><?php _e('Ordine in Evidenza', 'marcello-scavo-tattoo'); ?></label></th>
			<td>
				<input type="number" id="gallery_featured_order" name="gallery_featured_order" value="<?php echo esc_attr($featured_order ? $featured_order : 0); ?>" class="small-text" min="0" />
				<p class="description"><?php _e('Ordine di visualizzazione tra le immagini in evidenza (0 = primo).', 'marcello-scavo-tattoo'); ?></p>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Bookly Integration Functions
 */

/**
 * Get Bookly shortcode for booking form
 */
function marcello_scavo_get_bookly_form()
{
	// Debug: Check what's available
	$debug_info = '';
	if (current_user_can('administrator')) {
		$debug_info = '<!-- Debug Info: 
            bookly_shortcode exists: ' . (function_exists('bookly_shortcode') ? 'YES' : 'NO') . '
            Bookly class exists: ' . (class_exists('Bookly\Lib\Utils\Common') ? 'YES' : 'NO') . '
            shortcode registered: ' . (shortcode_exists('bookly-form') ? 'YES' : 'NO') . '
        -->';
	}

	// Check if Bookly is active by checking if the shortcode function exists
	if (
		function_exists('bookly_shortcode') ||
		class_exists('Bookly\Lib\Utils\Common') ||
		shortcode_exists('bookly-form')
	) {

		// Process and return the Bookly shortcode
		return $debug_info . do_shortcode('[bookly-form]');
	}

	// Alternative check: try to execute the shortcode and see if it returns processed content
	$bookly_output = do_shortcode('[bookly-form]');
	if ($bookly_output !== '[bookly-form]') {
		return $debug_info . $bookly_output;
	}

	// Fallback if Bookly is not active or configured
	return $debug_info . '<div class="bookly-fallback">
        <h3>' . __('Sistema di Prenotazione Non Disponibile', 'marcello-scavo-tattoo') . '</h3>
        <p>' . __('Il sistema di prenotazione è temporaneamente non disponibile. Contattaci direttamente per prenotare il tuo appuntamento.', 'marcello-scavo-tattoo') . '</p>
        <div class="fallback-actions">
            <a href="tel:+393123456789" class="btn btn-primary">
                <i class="fas fa-phone"></i> ' . __('Chiama Ora', 'marcello-scavo-tattoo') . '
            </a>
            <a href="mailto:info@marcelloscavo.com" class="btn btn-outline-primary">
                <i class="fas fa-envelope"></i> ' . __('Scrivi Email', 'marcello-scavo-tattoo') . '
            </a>
        </div>
    </div>';
}

/**
 * Bookly custom styles
 */
function marcello_scavo_bookly_styles()
{
	// Check if Bookly is active using frontend-compatible methods
	if (
		function_exists('bookly_shortcode') ||
		class_exists('Bookly\Lib\Utils\Common') ||
		shortcode_exists('bookly-form')
	) {
	?>
		<style>
			/* Bookly Custom Styles for Marcello Scavo Theme */

			/* Main Bookly Container */
			.bookly-form-container,
			.bookly-form {
				font-family: var(--font-primary) !important;
			}

			/* Bookly Buttons */
			.bookly-btn,
			.bookly-next-step,
			.bookly-back-step {
				background: linear-gradient(45deg, var(--primary-gold), var(--secondary-gold)) !important;
				border: none !important;
				border-radius: var(--border-radius) !important;
				color: var(--white) !important;
				font-weight: 600 !important;
				padding: var(--spacing-sm) var(--spacing-md) !important;
				transition: all 0.3s ease !important;
			}

			.bookly-btn:hover,
			.bookly-next-step:hover,
			.bookly-back-step:hover {
				transform: translateY(-2px) !important;
				box-shadow: var(--shadow-md) !important;
			}

			/* Bookly Form Fields */
			.bookly-form input[type="text"],
			.bookly-form input[type="email"],
			.bookly-form input[type="tel"],
			.bookly-form select,
			.bookly-form textarea {
				border: 2px solid var(--light-gray) !important;
				border-radius: var(--border-radius) !important;
				padding: var(--spacing-sm) !important;
				font-family: var(--font-primary) !important;
				transition: border-color 0.3s ease !important;
			}

			.bookly-form input:focus,
			.bookly-form select:focus,
			.bookly-form textarea:focus {
				border-color: var(--primary-blue) !important;
				box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1) !important;
				outline: none !important;
			}

			/* Bookly Calendar */
			.bookly-calendar {
				border-radius: var(--border-radius-lg) !important;
				overflow: hidden !important;
				box-shadow: var(--shadow-sm) !important;
			}

			.bookly-calendar .bookly-day.bookly-available:hover {
				background: var(--primary-gold) !important;
				color: var(--white) !important;
			}

			.bookly-calendar .bookly-day.bookly-selected {
				background: var(--primary-blue) !important;
				color: var(--white) !important;
			}

			/* Bookly Time Slots */
			.bookly-time-step .bookly-time-slot {
				border: 2px solid var(--light-gray) !important;
				border-radius: var(--border-radius) !important;
				margin: var(--spacing-xs) !important;
				transition: all 0.3s ease !important;
			}

			.bookly-time-step .bookly-time-slot:hover {
				border-color: var(--primary-gold) !important;
				background: var(--light-gold) !important;
			}

			.bookly-time-step .bookly-time-slot.bookly-selected {
				background: var(--primary-blue) !important;
				color: var(--white) !important;
				border-color: var(--primary-blue) !important;
			}

			/* Bookly Service Selection */
			.bookly-service-step .bookly-service {
				border: 2px solid var(--light-gray) !important;
				border-radius: var(--border-radius) !important;
				margin-bottom: var(--spacing-sm) !important;
				padding: var(--spacing-md) !important;
				transition: all 0.3s ease !important;
			}

			.bookly-service-step .bookly-service:hover {
				border-color: var(--primary-gold) !important;
				box-shadow: var(--shadow-sm) !important;
			}

			.bookly-service-step .bookly-service.bookly-selected {
				border-color: var(--primary-blue) !important;
				background: var(--light-blue) !important;
			}

			/* Bookly Progress Bar */
			.bookly-progress-bar {
				background: var(--light-gray) !important;
				border-radius: var(--border-radius) !important;
				overflow: hidden !important;
			}

			.bookly-progress-bar .bookly-progress {
				background: linear-gradient(90deg, var(--primary-blue), var(--primary-gold)) !important;
			}

			/* Bookly Loading */
			.bookly-loading {
				color: var(--primary-blue) !important;
			}

			/* Bookly Error Messages */
			.bookly-error {
				background: #fef2f2 !important;
				color: #dc2626 !important;
				border: 1px solid #fecaca !important;
				border-radius: var(--border-radius) !important;
				padding: var(--spacing-sm) !important;
			}

			/* Bookly Success Messages */
			.bookly-success {
				background: #f0fdf4 !important;
				color: #166534 !important;
				border: 1px solid #bbf7d0 !important;
				border-radius: var(--border-radius) !important;
				padding: var(--spacing-sm) !important;
			}

			/* Responsive adjustments */
			@media (max-width: 768px) {
				.bookly-form {
					padding: var(--spacing-md) !important;
				}

				.bookly-time-step .bookly-time-slot {
					width: 100% !important;
					margin: var(--spacing-xs) 0 !important;
				}
			}
		</style>
	<?php
	}
}
add_action('wp_head', 'marcello_scavo_bookly_styles');

/**
 * Shop Product Meta Box Callback
 */
function marcello_scavo_product_meta_box_callback($post)
{
	wp_nonce_field('save_product_details', 'product_meta_nonce');

	$product_price = get_post_meta($post->ID, '_product_price', true);
	$product_type = get_post_meta($post->ID, '_product_type', true);
	$product_stock = get_post_meta($post->ID, '_product_stock', true);
	$product_sku = get_post_meta($post->ID, '_product_sku', true);

	?>
	<table class="form-table">
		<tr>
			<th><label for="product_price"><?php _e('Prezzo (€)', 'marcello-scavo-tattoo'); ?></label></th>
			<td><input type="number" id="product_price" name="product_price" value="<?php echo esc_attr($product_price); ?>" class="small-text" min="0" step="0.01" required /></td>
		</tr>
		<tr>
			<th><label for="product_type"><?php _e('Tipo Prodotto', 'marcello-scavo-tattoo'); ?></label></th>
			<td>
				<select id="product_type" name="product_type" class="regular-text" required>
					<option value=""><?php _e('Seleziona Tipo', 'marcello-scavo-tattoo'); ?></option>
					<option value="flash_tattoo" <?php selected($product_type, 'flash_tattoo'); ?>><?php _e('Flash Tattoo', 'marcello-scavo-tattoo'); ?></option>
					<option value="merchandise" <?php selected($product_type, 'merchandise'); ?>><?php _e('Merchandising', 'marcello-scavo-tattoo'); ?></option>
					<option value="print" <?php selected($product_type, 'print'); ?>><?php _e('Stampa', 'marcello-scavo-tattoo'); ?></option>
					<option value="digital" <?php selected($product_type, 'digital'); ?>><?php _e('Digitale', 'marcello-scavo-tattoo'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="product_stock"><?php _e('Quantità in Stock', 'marcello-scavo-tattoo'); ?></label></th>
			<td><input type="number" id="product_stock" name="product_stock" value="<?php echo esc_attr($product_stock); ?>" class="small-text" min="0" /></td>
		</tr>
		<tr>
			<th><label for="product_sku"><?php _e('Codice Prodotto (SKU)', 'marcello-scavo-tattoo'); ?></label></th>
			<td><input type="text" id="product_sku" name="product_sku" value="<?php echo esc_attr($product_sku); ?>" class="regular-text" /></td>
		</tr>
	</table>
<?php
}

/**
 * Save Meta Box Data
 */
function marcello_scavo_save_meta_boxes($post_id)
{
	// Portfolio meta
	if (isset($_POST['portfolio_meta_nonce']) && wp_verify_nonce($_POST['portfolio_meta_nonce'], 'save_portfolio_details')) {
		if (isset($_POST['portfolio_client_name'])) {
			update_post_meta($post_id, '_portfolio_client_name', sanitize_text_field($_POST['portfolio_client_name']));
		}
		if (isset($_POST['portfolio_project_date'])) {
			update_post_meta($post_id, '_portfolio_project_date', sanitize_text_field($_POST['portfolio_project_date']));
		}
		if (isset($_POST['portfolio_project_location'])) {
			update_post_meta($post_id, '_portfolio_project_location', sanitize_text_field($_POST['portfolio_project_location']));
		}
		if (isset($_POST['portfolio_project_type'])) {
			update_post_meta($post_id, '_portfolio_project_type', sanitize_text_field($_POST['portfolio_project_type']));
		}
		if (isset($_POST['portfolio_project_duration'])) {
			update_post_meta($post_id, '_portfolio_project_duration', floatval($_POST['portfolio_project_duration']));
		}
	}

	// Gallery meta
	if (isset($_POST['gallery_meta_nonce']) && wp_verify_nonce($_POST['gallery_meta_nonce'], 'save_gallery_details')) {
		if (isset($_POST['gallery_image_caption'])) {
			update_post_meta($post_id, '_gallery_image_caption', sanitize_textarea_field($_POST['gallery_image_caption']));
		}
		if (isset($_POST['gallery_image_alt_text'])) {
			update_post_meta($post_id, '_gallery_image_alt_text', sanitize_text_field($_POST['gallery_image_alt_text']));
		}
		if (isset($_POST['gallery_image_technique'])) {
			update_post_meta($post_id, '_gallery_image_technique', sanitize_text_field($_POST['gallery_image_technique']));
		}
		if (isset($_POST['gallery_image_dimensions'])) {
			update_post_meta($post_id, '_gallery_image_dimensions', sanitize_text_field($_POST['gallery_image_dimensions']));
		}
		if (isset($_POST['gallery_creation_date'])) {
			update_post_meta($post_id, '_gallery_creation_date', sanitize_text_field($_POST['gallery_creation_date']));
		}
		if (isset($_POST['gallery_featured_order'])) {
			update_post_meta($post_id, '_gallery_featured_order', intval($_POST['gallery_featured_order']));
		}
		// Checkbox handling
		if (isset($_POST['gallery_is_featured'])) {
			update_post_meta($post_id, '_gallery_is_featured', '1');
		} else {
			delete_post_meta($post_id, '_gallery_is_featured');
		}
	}

	// Product meta
	if (isset($_POST['product_meta_nonce']) && wp_verify_nonce($_POST['product_meta_nonce'], 'save_product_details')) {
		if (isset($_POST['product_price'])) {
			update_post_meta($post_id, '_product_price', floatval($_POST['product_price']));
		}
		if (isset($_POST['product_type'])) {
			update_post_meta($post_id, '_product_type', sanitize_text_field($_POST['product_type']));
		}
		if (isset($_POST['product_stock'])) {
			update_post_meta($post_id, '_product_stock', intval($_POST['product_stock']));
		}
		if (isset($_POST['product_sku'])) {
			update_post_meta($post_id, '_product_sku', sanitize_text_field($_POST['product_sku']));
		}
	}
}
add_action('save_post', 'marcello_scavo_save_meta_boxes');

/**
 * AJAX Functions
 */

/**
 * Handle contact form submissions
 */
function marcello_scavo_handle_contact_form()
{
	// Verify nonce
	if (!wp_verify_nonce($_POST['nonce'], 'marcello_scavo_nonce')) {
		wp_die(__('Errore di sicurezza', 'marcello-scavo-tattoo'));
	}

	$name = sanitize_text_field($_POST['name']);
	$email = sanitize_email($_POST['email']);
	$subject = sanitize_text_field($_POST['subject']);
	$message = sanitize_textarea_field($_POST['message']);

	// Validate required fields
	if (empty($name) || empty($email) || empty($message)) {
		wp_send_json_error(['message' => __('Tutti i campi sono obbligatori', 'marcello-scavo-tattoo')]);
	}

	if (!is_email($email)) {
		wp_send_json_error(['message' => __('Email non valida', 'marcello-scavo-tattoo')]);
	}

	// Prepare email
	$admin_email = get_option('admin_email');
	$email_subject = sprintf(__('Nuovo messaggio da %s: %s', 'marcello-scavo-tattoo'), $name, $subject);
	$email_message = sprintf(
		__("Hai ricevuto un nuovo messaggio dal sito web:\n\nNome: %s\nEmail: %s\nOggetto: %s\n\nMessaggio:\n%s", 'marcello-scavo-tattoo'),
		$name,
		$email,
		$subject,
		$message
	);

	$headers = [
		'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>',
		'Reply-To: ' . $name . ' <' . $email . '>',
		'Content-Type: text/plain; charset=UTF-8'
	];

	// Send email
	if (wp_mail($admin_email, $email_subject, $email_message, $headers)) {
		wp_send_json_success(['message' => __('Messaggio inviato con successo!', 'marcello-scavo-tattoo')]);
	} else {
		wp_send_json_error(['message' => __('Errore nell\'invio del messaggio', 'marcello-scavo-tattoo')]);
	}
}
add_action('wp_ajax_marcello_scavo_contact', 'marcello_scavo_handle_contact_form');
add_action('wp_ajax_nopriv_marcello_scavo_contact', 'marcello_scavo_handle_contact_form');

/**
 * Handle newsletter subscription
 */
function marcello_scavo_handle_newsletter()
{
	// Verify nonce
	if (!wp_verify_nonce($_POST['nonce'], 'marcello_scavo_nonce')) {
		wp_die(__('Errore di sicurezza', 'marcello-scavo-tattoo'));
	}

	$email = sanitize_email($_POST['email']);

	if (!is_email($email)) {
		wp_send_json_error(['message' => __('Email non valida', 'marcello-scavo-tattoo')]);
	}

	// Here you would integrate with your newsletter service (MailChimp, etc.)
	// For now, we'll just send a confirmation email

	$admin_email = get_option('admin_email');
	$subject = __('Nuova iscrizione newsletter', 'marcello-scavo-tattoo');
	$message = sprintf(__('Nuova iscrizione alla newsletter: %s', 'marcello-scavo-tattoo'), $email);

	if (wp_mail($admin_email, $subject, $message)) {
		wp_send_json_success(['message' => __('Iscrizione completata!', 'marcello-scavo-tattoo')]);
	} else {
		wp_send_json_error(['message' => __('Errore nell\'iscrizione', 'marcello-scavo-tattoo')]);
	}
}
add_action('wp_ajax_marcello_scavo_newsletter', 'marcello_scavo_handle_newsletter');
add_action('wp_ajax_nopriv_marcello_scavo_newsletter', 'marcello_scavo_handle_newsletter');

/**
 * Handle gallery filter AJAX
 */
function marcello_scavo_handle_gallery_filter()
{
	// Verify nonce
	if (!wp_verify_nonce($_POST['nonce'], 'marcello_scavo_nonce')) {
		wp_die(__('Errore di sicurezza', 'marcello-scavo-tattoo'));
	}

	$category = sanitize_text_field($_POST['category']);

	$args = array(
		'post_type' => 'gallery',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'meta_key' => '_gallery_featured_order',
		'orderby' => 'meta_value_num date',
		'order' => 'ASC'
	);

	// Add taxonomy query if category is not 'all'
	if ($category !== 'all') {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'gallery_category',
				'field' => 'slug',
				'terms' => $category
			)
		);
	}

	$gallery_query = new WP_Query($args);

	$html = '';
	$has_items = false;

	if ($gallery_query->have_posts()) {
		while ($gallery_query->have_posts()) {
			$gallery_query->the_post();

			if (has_post_thumbnail()) {
				$has_items = true;
				$image_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
				$image_caption = get_post_meta(get_the_ID(), '_gallery_image_caption', true);
				$image_technique = get_post_meta(get_the_ID(), '_gallery_image_technique', true);
				$image_alt = get_post_meta(get_the_ID(), '_gallery_image_alt_text', true);

				$image_alt = $image_alt ? $image_alt : get_the_title();

				// Get categories for this item
				$item_categories = get_the_terms(get_the_ID(), 'gallery_category');
				$category_classes = 'all';
				if (!empty($item_categories)) {
					$category_slugs = array();
					foreach ($item_categories as $cat) {
						$category_slugs[] = $cat->slug;
					}
					$category_classes .= ' ' . implode(' ', $category_slugs);
				}

				$html .= '<div class="gallery-item" data-category="' . esc_attr($category_classes) . '">';
				$html .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '">';
				$html .= '<div class="gallery-overlay">';
				$html .= '<h3>' . get_the_title() . '</h3>';

				if ($image_technique) {
					$html .= '<p>' . esc_html(str_replace('_', ' ', ucfirst($image_technique))) . '</p>';
				} elseif ($image_caption) {
					$html .= '<p>' . esc_html(wp_trim_words($image_caption, 8)) . '</p>';
				}

				$html .= '</div>';
				$html .= '</div>';
			}
		}
		wp_reset_postdata();
	}

	if (!$has_items) {
		if (current_user_can('manage_options')) {
			$html = '<div class="no-gallery-items admin-message">';
			$html .= '<h3>' . __('Nessuna immagine in questa categoria', 'marcello-scavo-tattoo') . '</h3>';
			$html .= '<p>' . __('Non ci sono ancora immagini per questa categoria.', 'marcello-scavo-tattoo') . '</p>';
			$html .= '<p><a href="' . admin_url('post-new.php?post_type=gallery') . '" class="button">' . __('Aggiungi nuova immagine', 'marcello-scavo-tattoo') . '</a></p>';
			$html .= '</div>';
		} else {
			$html = '<div class="no-gallery-items"><p>' . __('Nessuna immagine trovata per questa categoria.', 'marcello-scavo-tattoo') . '</p></div>';
		}
	}

	wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_marcello_scavo_gallery_filter', 'marcello_scavo_handle_gallery_filter');
add_action('wp_ajax_nopriv_marcello_scavo_gallery_filter', 'marcello_scavo_handle_gallery_filter');

/**
 * Customizer Settings
 */
function marcello_scavo_customize_register($wp_customize)
{
	// Hero Section
	$wp_customize->add_section('hero_section', array(
		'title' => __('Sezione Hero', 'marcello-scavo-tattoo'),
		'priority' => 30,
	));

	// Hero Label (sopra il titolo) - SEMPLIFICATO
	$wp_customize->add_setting('hero_label', array(
		'default' => 'L\'ARTE DEL TATTOO',
		'sanitize_callback' => 'sanitize_text_field',
	));

	$wp_customize->add_control('hero_label', array(
		'label' => __('Etichetta Hero (sopra il titolo)', 'marcello-scavo-tattoo'),
		'description' => __('Inserisci in italiano - sarà tradotto automaticamente', 'marcello-scavo-tattoo'),
		'section' => 'hero_section',
		'type' => 'text',
	));

	// Hero Title - SEMPLIFICATO
	$wp_customize->add_setting('hero_title', array(
		'default' => 'Scopri l\'essenza dei miei tatuaggi e opere d\'arte.',
		'sanitize_callback' => 'wp_kses_post',
	));

	$wp_customize->add_control('hero_title', array(
		'label' => __('Titolo Hero Principale', 'marcello-scavo-tattoo'),
		'description' => __('Inserisci in italiano - sarà tradotto automaticamente. Usa &lt;br&gt; per andare a capo', 'marcello-scavo-tattoo'),
		'section' => 'hero_section',
		'type' => 'textarea',
	));

	// Hero Description
	$wp_customize->add_setting('hero_description', array(
		'default' => __('Benvenuti nel mio mondo creativo, dove ogni storia racconta una storia, i miei tatuaggi e le opere d\'arte nascono dall\'ispirazione e dalla passione. Che tu stia cercando un tatuaggio personalizzato per il tuo corpo o un\'opera unica per la tua parete, sei nel posto giusto. Esplora il mio portfolio e lasciati ispirare dalla fusione di arte e tatuaggio.', 'marcello-scavo-tattoo'),
		'sanitize_callback' => 'sanitize_textarea_field',
	));

	$wp_customize->add_control('hero_description', array(
		'label' => __('Descrizione Hero', 'marcello-scavo-tattoo'),
		'section' => 'hero_section',
		'type' => 'textarea',
	));

	// Hero Button Text - SEMPLIFICATO
	$wp_customize->add_setting('hero_button_text', array(
		'default' => 'Esplora Ora',
		'sanitize_callback' => 'sanitize_text_field',
	));

	$wp_customize->add_control('hero_button_text', array(
		'label' => __('Testo Bottone Hero', 'marcello-scavo-tattoo'),
		'description' => __('Inserisci in italiano - sarà tradotto automaticamente', 'marcello-scavo-tattoo'),
		'section' => 'hero_section',
		'type' => 'text',
	));

	// Rimuoviamo il vecchio hero_subtitle che non serve più

	/**
	 * Helper function per ottenere testi multilingua dal Customizer
	 */
	function get_multilingual_theme_mod($base_field, $fallback_it = '', $current_lang = null)
	{
		// Se non è specificata, rileva la lingua corrente dal localStorage o default
		if (!$current_lang) {
			$current_lang = 'it'; // Default italiano

			// In futuro potremmo rilevare da cookie/session
			// Per ora usiamo JavaScript per sincronizzare
		}

		$field_name = $base_field . '_' . $current_lang;
		$value = get_theme_mod($field_name, '');

		// Se non c'è valore personalizzato, usa il fallback
		if (empty($value)) {
			$value = $fallback_it;
		}

		return $value;
	}

	/**
	 * Sistema di traduzione automatica con Google Translate + Cache
	 */

	// Funzione per ottenere traduzione da cache o Google Translate
	function get_cached_translation($text, $target_lang, $source_lang = 'it')
	{
		if ($target_lang === $source_lang) return $text;

		// Genera chiave cache unica
		$cache_key = 'translation_' . md5($text . '_' . $source_lang . '_' . $target_lang);

		// Cerca in cache
		$cached = get_transient($cache_key);
		if ($cached !== false) {
			return $cached;
		}

		// Chiama Google Translate
		$translation = google_translate_text($text, $target_lang, $source_lang);

		if ($translation) {
			// Salva in cache per 30 giorni
			set_transient($cache_key, $translation, 30 * DAY_IN_SECONDS);
			return $translation;
		}

		// Fallback: usa il dizionario locale o testo originale
		return fallback_translation($text, $target_lang) ?: $text;
	}

	// Chiama Google Translate API (gratuita per piccoli volumi)
	function google_translate_text($text, $target_lang, $source_lang = 'it')
	{
		$text = trim($text);
		if (empty($text)) return '';

		// URL dell'API gratuita di Google Translate
		$url = 'https://translate.googleapis.com/translate_a/single?' . http_build_query([
			'client' => 'gtx',
			'sl' => $source_lang,
			'tl' => $target_lang,
			'dt' => 't',
			'q' => $text
		]);

		// Chiamata HTTP
		$response = wp_remote_get($url, [
			'timeout' => 10,
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (compatible; WordPress)'
			]
		]);

		if (is_wp_error($response)) {
			error_log('Google Translate error: ' . $response->get_error_message());
			return false;
		}

		$body = wp_remote_retrieve_body($response);

		// Parse della risposta JSON di Google
		$data = json_decode($body, true);

		if ($data && isset($data[0][0][0])) {
			$translated = $data[0][0][0];
			return $translated;
		}

		return false;
	}

	// Fallback al dizionario locale (versione semplificata)
	function fallback_translation($text, $target_lang)
	{
		$simple_dict = [
			'it' => [
				'ciao sono marcello scavo' => [
					'en' => 'hello i am marcello scavo',
					'es' => 'hola soy marcello scavo'
				],
				'l\'arte del tattoo' => [
					'en' => 'the art of tattoo',
					'es' => 'el arte del tatuaje'
				],
				'esplora ora' => [
					'en' => 'explore now',
					'es' => 'explora ahora'
				]
			]
		];

		$text_lower = strtolower(trim($text));

		if (isset($simple_dict['it'][$text_lower][$target_lang])) {
			return $simple_dict['it'][$text_lower][$target_lang];
		}

		return false;
	}

	// Contact Info
	$wp_customize->add_section('contact_info', array(
		'title' => __('Informazioni Contatto', 'marcello-scavo-tattoo'),
		'priority' => 31,
	));

	$wp_customize->add_setting('contact_milano_address', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_textarea_field',
	));

	$wp_customize->add_control('contact_milano_address', array(
		'label' => __('Indirizzo Milano', 'marcello-scavo-tattoo'),
		'section' => 'contact_info',
		'type' => 'textarea',
	));

	$wp_customize->add_setting('contact_messina_address', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_textarea_field',
	));

	$wp_customize->add_control('contact_messina_address', array(
		'label' => __('Indirizzo Messina', 'marcello-scavo-tattoo'),
		'section' => 'contact_info',
		'type' => 'textarea',
	));

	$wp_customize->add_setting('contact_phone', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_text_field',
	));

	$wp_customize->add_control('contact_phone', array(
		'label' => __('Telefono', 'marcello-scavo-tattoo'),
		'section' => 'contact_info',
		'type' => 'text',
	));

	$wp_customize->add_setting('contact_email', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_email',
	));

	$wp_customize->add_control('contact_email', array(
		'label' => __('Email', 'marcello-scavo-tattoo'),
		'section' => 'contact_info',
		'type' => 'email',
	));

	// Footer Layout Settings
	$wp_customize->add_section('footer_layout', array(
		'title' => __('Layout Footer', 'marcello-scavo-tattoo'),
		'priority' => 32,
		'description' => __('Personalizza la struttura e il contenuto del footer.', 'marcello-scavo-tattoo'),
	));

	// Footer Layout Type
	$wp_customize->add_setting('footer_layout_type', array(
		'default' => 'three_columns',
		'sanitize_callback' => 'marcello_scavo_sanitize_footer_layout',
	));

	$wp_customize->add_control('footer_layout_type', array(
		'label' => __('Tipo Layout Footer', 'marcello-scavo-tattoo'),
		'section' => 'footer_layout',
		'type' => 'select',
		'choices' => array(
			'one_column' => __('Una Colonna', 'marcello-scavo-tattoo'),
			'two_columns' => __('Due Colonne', 'marcello-scavo-tattoo'),
			'three_columns' => __('Tre Colonne (Default)', 'marcello-scavo-tattoo'),
			'four_columns' => __('Quattro Colonne', 'marcello-scavo-tattoo'),
		),
		'description' => __('Scegli il numero di colonne per il footer. Cambierà automaticamente le aree widget disponibili.', 'marcello-scavo-tattoo'),
	));

	// Social Links Settings
	$wp_customize->add_setting('footer_social_style', array(
		'default' => 'modern',
		'sanitize_callback' => 'marcello_scavo_sanitize_social_style',
	));

	$wp_customize->add_control('footer_social_style', array(
		'label' => __('Stile Icone Social', 'marcello-scavo-tattoo'),
		'section' => 'footer_layout',
		'type' => 'select',
		'choices' => array(
			'modern' => __('Moderno (Circoli colorati)', 'marcello-scavo-tattoo'),
			'minimal' => __('Minimale (Solo icone)', 'marcello-scavo-tattoo'),
			'buttons' => __('Pulsanti con testo', 'marcello-scavo-tattoo'),
			'cards' => __('Card social', 'marcello-scavo-tattoo'),
		),
	));

	// Social Links
	$social_networks = array(
		'instagram' => array('label' => 'Instagram', 'placeholder' => 'https://instagram.com/marcelloscavo_art'),
		'facebook' => array('label' => 'Facebook', 'placeholder' => 'https://facebook.com/marcelloscavo'),
		'tiktok' => array('label' => 'TikTok', 'placeholder' => 'https://tiktok.com/@marcello.scavo'),
		'youtube' => array('label' => 'YouTube', 'placeholder' => 'https://youtube.com/@MarcelloScavo'),
		'twitter' => array('label' => 'Twitter/X', 'placeholder' => 'https://twitter.com/marcelloscavo'),
		'linkedin' => array('label' => 'LinkedIn', 'placeholder' => 'https://linkedin.com/in/marcelloscavo'),
	);

	foreach ($social_networks as $network => $data) {
		$wp_customize->add_setting("social_link_{$network}", array(
			'default' => '',
			'sanitize_callback' => 'esc_url_raw',
		));

		$wp_customize->add_control("social_link_{$network}", array(
			'label' => $data['label'],
			'section' => 'footer_layout',
			'type' => 'url',
			'input_attrs' => array(
				'placeholder' => $data['placeholder'],
			),
		));
	}

	// WhatsApp Booking Section
	$wp_customize->add_section('whatsapp_booking', array(
		'title' => __('📱 WhatsApp Prenotazioni', 'marcello-scavo-tattoo'),
		'description' => __('Configurazione per il bottone "Prenota Consulenza" che invia un messaggio WhatsApp.', 'marcello-scavo-tattoo'),
		'priority' => 35,
	));

	// WhatsApp Number
	$wp_customize->add_setting('whatsapp_number', array(
		'default' => '393331234567',
		'sanitize_callback' => 'sanitize_text_field',
	));

	$wp_customize->add_control('whatsapp_number', array(
		'label' => __('Numero WhatsApp', 'marcello-scavo-tattoo'),
		'description' => __('Inserisci il numero in formato internazionale (es: 393331234567)', 'marcello-scavo-tattoo'),
		'section' => 'whatsapp_booking',
		'type' => 'text',
		'input_attrs' => array(
			'placeholder' => '393331234567',
		),
	));

	// WhatsApp Message
	$wp_customize->add_setting('whatsapp_message', array(
		'default' => 'Ciao! Vorrei prenotare una consulenza per un tatuaggio.',
		'sanitize_callback' => 'sanitize_textarea_field',
	));

	$wp_customize->add_control('whatsapp_message', array(
		'label' => __('Messaggio WhatsApp', 'marcello-scavo-tattoo'),
		'description' => __('Messaggio che verrà precompilato quando l\'utente clicca "Prenota Consulenza".', 'marcello-scavo-tattoo'),
		'section' => 'whatsapp_booking',
		'type' => 'textarea',
	));
}
add_action('customize_register', 'marcello_scavo_customize_register');

/**
 * Google Translate Integration per AJAX
 */

// AJAX endpoint per traduzioni dal frontend
function handle_translation_ajax()
{
	// DEBUG: Log della richiesta
	error_log('🌐 AJAX Translation called with: ' . print_r($_POST, true));

	if (!isset($_POST['text'], $_POST['target_lang'])) {
		error_log('❌ AJAX Translation: Missing parameters');
		wp_die('Missing parameters');
	}

	$text = sanitize_text_field($_POST['text']);
	$target_lang = sanitize_text_field($_POST['target_lang']);
	$source_lang = isset($_POST['source_lang']) ? sanitize_text_field($_POST['source_lang']) : 'it';

	error_log("🌐 AJAX Translation: text='$text', target='$target_lang', source='$source_lang'");

	// TEST: Dizionario esteso per traduzioni
	$simple_translations = [
		'Esplora Ora' => [
			'en' => 'Explore Now',
			'es' => 'Explorar Ahora'
		],
		'Portfolio' => [
			'en' => 'Portfolio',
			'es' => 'Portafolio'
		],
		'Contatti' => [
			'en' => 'Contacts',
			'es' => 'Contactos'
		],
		'Chi Sono' => [
			'en' => 'About Me',
			'es' => 'Sobre Mí'
		],
		'Home' => [
			'en' => 'Home',
			'es' => 'Inicio'
		],
		'Servizi' => [
			'en' => 'Services',
			'es' => 'Servicios'
		],
		'Shop' => [
			'en' => 'Shop',
			'es' => 'Tienda'
		],
		'Blog' => [
			'en' => 'Blog',
			'es' => 'Blog'
		],
		'Contactos' => [
			'en' => 'Contacts',
			'es' => 'Contactos'
		],
		'L\'ARTE DEL TATTOO' => [
			'en' => 'THE ART OF TATTOO',
			'es' => 'EL ARTE DEL TATUAJE'
		],
		'Scopri l\'essenza dei miei tatuaggi e opere d\'arte.' => [
			'en' => 'Discover the essence of my tattoos and works of art.',
			'es' => 'Descubre la esencia de mis tatuajes y obras de arte.'
		],
		'Ciao sono Marcello Scavo' => [
			'en' => 'Hi, I\'m Marcello Scavo',
			'es' => 'Hola, soy Marcello Scavo'
		],
		'Benvenuti nel mio mondo creativo, dove ogni storia racconta una storia, i miei tatuaggi e le opere d\'arte nascono dall\'ispirazione e dalla passione. Che tu stia cercando un tatuaggio personalizzato per il tuo corpo o un\'opera unica per la tua parete, sei nel posto giusto. Esplora il mio portfolio e lasciati ispirare dalla fusione di arte e tatuaggio.' => [
			'en' => 'Welcome to my creative world, where every story tells a story, my tattoos and artworks are born from inspiration and passion. Whether you\'re looking for a personalized tattoo for your body or a unique piece for your wall, you\'re in the right place. Explore my portfolio and be inspired by the fusion of art and tattoo.',
			'es' => 'Bienvenidos a mi mundo creativo, donde cada historia cuenta una historia, mis tatuajes y obras de arte nacen de la inspiración y la pasión. Ya sea que busques un tatuaje personalizado para tu cuerpo o una obra única para tu pared, estás en el lugar correcto. Explora mi portafolio y déjate inspirar por la fusión de arte y tatuaje.'
		]
	];

	$translation = isset($simple_translations[$text][$target_lang])
		? $simple_translations[$text][$target_lang]
		: (isset($simple_translations[wp_unslash($text)][$target_lang])
			? $simple_translations[wp_unslash($text)][$target_lang]
			: $text);

	error_log("✅ AJAX Translation result: '$translation'");

	wp_send_json_success([
		'original' => $text,
		'translated' => $translation,
		'target_lang' => $target_lang
	]);
}

// AJAX endpoint per traduzioni multiple (batch)
function handle_batch_translation_ajax()
{
	error_log('🌐 BATCH Translation called with: ' . print_r($_POST, true));

	if (!isset($_POST['texts'], $_POST['target_lang'])) {
		error_log('❌ BATCH Translation: Missing parameters');
		wp_die('Missing parameters');
	}

	$texts = $_POST['texts']; // Array di testi da tradurre
	$target_lang = sanitize_text_field($_POST['target_lang']);
	$source_lang = isset($_POST['source_lang']) ? sanitize_text_field($_POST['source_lang']) : 'it';

	// Dizionario esteso per traduzioni
	$simple_translations = [
		'Esplora Ora' => [
			'en' => 'Explore Now',
			'es' => 'Explorar Ahora'
		],
		'Portfolio' => [
			'en' => 'Portfolio',
			'es' => 'Portafolio'
		],
		'Contatti' => [
			'en' => 'Contacts',
			'es' => 'Contactos'
		],
		'Chi Sono' => [
			'en' => 'About Me',
			'es' => 'Sobre Mí'
		],
		'Home' => [
			'en' => 'Home',
			'es' => 'Inicio'
		],
		'Servizi' => [
			'en' => 'Services',
			'es' => 'Servicios'
		],
		'Shop' => [
			'en' => 'Shop',
			'es' => 'Tienda'
		],
		'Blog' => [
			'en' => 'Blog',
			'es' => 'Blog'
		],
		'Contactos' => [
			'en' => 'Contacts',
			'es' => 'Contactos'
		],
		'L\'ARTE DEL TATTOO' => [
			'en' => 'THE ART OF TATTOO',
			'es' => 'EL ARTE DEL TATUAJE'
		],
		'Scopri l\'essenza dei miei tatuaggi e opere d\'arte.' => [
			'en' => 'Discover the essence of my tattoos and works of art.',
			'es' => 'Descubre la esencia de mis tatuajes y obras de arte.'
		],
		'Ciao sono Marcello Scavo' => [
			'en' => 'Hi, I\'m Marcello Scavo',
			'es' => 'Hola, soy Marcello Scavo'
		],
		'Benvenuti nel mio mondo creativo, dove ogni storia racconta una storia, i miei tatuaggi e le opere d\'arte nascono dall\'ispirazione e dalla passione. Che tu stia cercando un tatuaggio personalizzato per il tuo corpo o un\'opera unica per la tua parete, sei nel posto giusto. Esplora il mio portfolio e lasciati ispirare dalla fusione di arte e tatuaggio.' => [
			'en' => 'Welcome to my creative world, where every story tells a story, my tattoos and artworks are born from inspiration and passion. Whether you\'re looking for a personalized tattoo for your body or a unique piece for your wall, you\'re in the right place. Explore my portfolio and be inspired by the fusion of art and tattoo.',
			'es' => 'Bienvenidos a mi mundo creativo, donde cada historia cuenta una historia, mis tatuajes y obras de arte nacen de la inspiración y la pasión. Ya sea que busques un tatuaje personalizado para tu cuerpo o una obra única para tu pared, estás en el lugar correcto. Explora mi portafolio y déjate inspirar por la fusión de arte y tatuaje.'
		]
	];

	$translations = [];

	foreach ($texts as $text) {
		$clean_text = trim($text);
		// Normalizza il testo rimuovendo escape di slash
		$normalized_text = wp_unslash($clean_text);

		$translation = isset($simple_translations[$normalized_text][$target_lang])
			? $simple_translations[$normalized_text][$target_lang]
			: (isset($simple_translations[$clean_text][$target_lang])
				? $simple_translations[$clean_text][$target_lang]
				: $clean_text);

		// FALLBACK per traduzioni mancanti specifiche
		if ($translation === $clean_text) {
			$missing_translations = [
				'L\'arte di Marcello: un viaggio' => [
					'en' => 'Marcello\'s art: a journey',
					'es' => 'El arte de Marcello: un viaje'
				],
				'Marcello è un artista che unisce passione e talento in ogni opera d\'arte e tatuaggio. Fin da giovane, ha coltivato il suo amore per l\'arte, esplorando tecniche diverse e affascinandosi sempre della vita che lo circonda. La sua evoluzione è evidente, non solo nei suoi lavori, ma anche nella continua ricerca di innovazione e stile. Ogni tatuaggio racconta una storia, un pezzo della personalità di chi lo indossa. Con la sua capacità di ascoltare e interpretare i desideri dei clienti, Marcello trasforma ogni foglio bianco in un\'opera unica, creando un legame speciale con ognuno di loro.' => [
					'en' => 'Marcello is an artist who combines passion and talent in every artwork and tattoo. Since he was young, he has cultivated his love for art, exploring different techniques and always being fascinated by the life around him. His evolution is evident, not only in his works, but also in the continuous search for innovation and style. Every tattoo tells a story, a piece of the personality of the one who wears it. With his ability to listen and interpret clients\' desires, Marcello transforms every blank sheet into a unique work, creating a special bond with each of them.',
					'es' => 'Marcello es un artista que combina pasión y talento en cada obra de arte y tatuaje. Desde joven, ha cultivado su amor por el arte, explorando técnicas diferentes y siempre fascinándose por la vida que lo rodea. Su evolución es evidente, no solo en sus trabajos, sino también en la búsqueda continua de innovación y estilo. Cada tatuaje cuenta una historia, una pieza de la personalidad de quien lo lleva. Con su capacidad de escuchar e interpretar los deseos de los clientes, Marcello transforma cada hoja en blanco en una obra única, creando un vínculo especial con cada uno de ellos.'
				],
				'La filosofia di Marcello si basa sull\'ascolto attento delle esigenze del cliente e sulla traduzione delle loro idee in opere d\'arte durature. Ogni sessione è un momento di creazione condivisa, dove l\'esperienza e la creatività si fondono per dare vita a qualcosa di veramente speciale.' => [
					'en' => 'Marcello\'s philosophy is based on careful listening to customer needs and translating their ideas into lasting works of art. Each session is a moment of shared creation, where experience and creativity merge to give life to something truly special.',
					'es' => 'La filosofía de Marcello se basa en escuchar atentamente las necesidades del cliente y traducir sus ideas en obras de arte duraderas. Cada sesión es un momento de creación compartida, donde la experiencia y la creatividad se fusionan para dar vida a algo verdaderamente especial.'
				],
				// Traduzioni aggiuntive per elementi che potrebbero essere già tradotti
				'Hi, I\'m Marcello Scavo' => [
					'en' => 'Hi, I\'m Marcello Scavo',
					'es' => 'Hola, soy Marcello Scavo'
				],
				'Explore Now' => [
					'en' => 'Explore Now',
					'es' => 'Explorar Ahora'
				],
				'THE ART OF TATTOO' => [
					'en' => 'THE ART OF TATTOO',
					'es' => 'EL ARTE DEL TATUAJE'
				],
				'Discover the essence of my tattoos and works of art.' => [
					'en' => 'Discover the essence of my tattoos and works of art.',
					'es' => 'Descubre la esencia de mis tatuajes y obras de arte.'
				],
				'About Me' => [
					'en' => 'About Me',
					'es' => 'Sobre Mí'
				],
				'Services' => [
					'en' => 'Services',
					'es' => 'Servicios'
				],
				'Contacts' => [
					'en' => 'Contacts',
					'es' => 'Contactos'
				],
				// === SEZIONE SERVIZI ===
				'I nostri servizi' => [
					'en' => 'Our Services',
					'es' => 'Nuestros Servicios'
				],
				'Scopri le nostre offerte principali, pensate per soddisfare le tue passioni.' => [
					'en' => 'Discover our main offers, designed to satisfy your passions.',
					'es' => 'Descubre nuestras ofertas principales, diseñadas para satisfacer tus pasiones.'
				],
				'Vendita opere d\'arte' => [
					'en' => 'Art Sales',
					'es' => 'Venta de Obras de Arte'
				],
				'Esplora la nostra collezione di opere d\'arte uniche e porta a casa un pezzo di creatività.' => [
					'en' => 'Explore our collection of unique artworks and take home a piece of creativity.',
					'es' => 'Explora nuestra colección de obras de arte únicas y llévate a casa una pieza de creatividad.'
				],
				'Mostra di tatuaggi' => [
					'en' => 'Tattoo Shows',
					'es' => 'Exposiciones de Tatuajes'
				],
				'Assisti ai nostri eventi di tatuaggio, dove talenti internazionali mostrano le loro creazioni dal vivo.' => [
					'en' => 'Attend our tattoo events, where international talents showcase their creations live.',
					'es' => 'Asiste a nuestros eventos de tatuaje, donde talentos internacionales muestran sus creaciones en vivo.'
				],
				'Prenotazione tatuaggi' => [
					'en' => 'Tattoo Booking',
					'es' => 'Reserva de Tatuajes'
				],
				'Prenota il tuo tatuaggio direttamente online e realizza il tuo design personalizzato con noi.' => [
					'en' => 'Book your tattoo directly online and create your personalized design with us.',
					'es' => 'Reserva tu tatuaje directamente en línea y crea tu diseño personalizado con nosotros.'
				],
				// === SEZIONE GALLERIA ===
				'Scopri le mie creazioni artistiche' => [
					'en' => 'Discover My Artistic Creations',
					'es' => 'Descubre Mis Creaciones Artísticas'
				],
				'Esplora una collezione di opere ispirate ai tatuaggi unici, che raccontano storie e emozioni.' => [
					'en' => 'Explore a collection of works inspired by unique tattoos, telling stories and emotions.',
					'es' => 'Explora una colección de obras inspiradas en tatuajes únicos, que cuentan historias y emociones.'
				]
			];

			$fallback_key = wp_unslash($clean_text);
			if (isset($missing_translations[$fallback_key][$target_lang])) {
				$translation = $missing_translations[$fallback_key][$target_lang];
			}
		}

		$translations[$clean_text] = $translation;
	}

	error_log("✅ BATCH Translation result: " . print_r($translations, true));

	wp_send_json_success([
		'translations' => $translations,
		'target_lang' => $target_lang,
		'count' => count($translations)
	]);
}

// Registra AJAX handlers
add_action('wp_ajax_translate_text', 'handle_translation_ajax');
add_action('wp_ajax_nopriv_translate_text', 'handle_translation_ajax');
add_action('wp_ajax_batch_translate', 'handle_batch_translation_ajax');
add_action('wp_ajax_nopriv_batch_translate', 'handle_batch_translation_ajax');

// === REMINDER SISTEMA DI TRADUZIONE ===
// Aggiunge notifica nell'admin per ricordare che il sistema di traduzione è in sviluppo
function marcello_scavo_translation_reminder()
{
	$screen = get_current_screen();

	// Mostra solo nelle pagine principali dell'admin
	if (!$screen || !in_array($screen->base, ['dashboard', 'themes', 'appearance_page_theme-customizer'])) {
		return;
	}

	echo '<div class="notice notice-info is-dismissible" style="border-left-color: #d4a574;">
        <p><strong>🌐 Sistema di Traduzione Multilingua</strong></p>
        <p>Il sistema di traduzione automatica con Google Translate è temporaneamente disabilitato durante lo sviluppo.</p>
        <p><strong>Prossimi sviluppi:</strong></p>
        <ul style="margin-left: 20px;">
            <li>✅ Traduzione automatica IT/EN/ES</li>
            <li>✅ Cache delle traduzioni</li>
            <li>✅ Interfaccia utente completata</li>
            <li>🔄 Test e ottimizzazioni in corso</li>
            <li>🔜 Attivazione sistema completo</li>
        </ul>
        <p><em>Per ora il sito rimane in italiano. Il sistema multilingua sarà riattivato una volta completati i test.</em></p>
    </div>';
}
add_action('admin_notices', 'marcello_scavo_translation_reminder');

// Aggiunge anche una voce nel menu Aspetto
function marcello_scavo_translation_menu()
{
	add_theme_page(
		'Sistema Traduzione',
		'🌐 Traduzioni',
		'manage_options',
		'marcello-translation-status',
		'marcello_scavo_translation_status_page'
	);
}
add_action('admin_menu', 'marcello_scavo_translation_menu');

function marcello_scavo_translation_status_page()
{
?>
	<div class="wrap">
		<h1>🌐 Sistema di Traduzione Multilingua</h1>

		<div class="notice notice-info">
			<h2>Stato Attuale: In Sviluppo</h2>
			<p>Il sistema di traduzione automatica è temporaneamente disabilitato per permettere lo sviluppo delle altre funzionalità del sito.</p>
		</div>

		<div class="card">
			<h2>Funzionalità Implementate</h2>
			<ul>
				<li>✅ <strong>Traduzione Automatica:</strong> Integrazione con Google Translate API</li>
				<li>✅ <strong>Lingue Supportate:</strong> Italiano, Inglese, Spagnolo</li>
				<li>✅ <strong>Cache Sistema:</strong> Traduzioni memorizzate per performance</li>
				<li>✅ <strong>Interfaccia Utente:</strong> Selettore lingua nel header</li>
				<li>✅ <strong>Batch Translation:</strong> Traduzione multipla per efficienza</li>
				<li>✅ <strong>Normalizzazione Testi:</strong> Gestione caratteri speciali</li>
			</ul>
		</div>

		<div class="card">
			<h2>Sezioni Tradotte</h2>
			<ul>
				<li>🌐 <strong>Navigazione:</strong> Menu principale</li>
				<li>🌐 <strong>Hero Section:</strong> Titolo, sottotitolo, pulsanti</li>
				<li>🌐 <strong>About Section:</strong> Biografia e filosofia di Marcello</li>
				<li>🌐 <strong>Servizi:</strong> I nostri servizi, descrizioni</li>
				<li>🌐 <strong>Galleria:</strong> Titoli e descrizioni</li>
			</ul>
		</div>

		<div class="card">
			<h2>Riattivazione Sistema</h2>
			<p>Per riattivare il sistema di traduzione:</p>
			<ol>
				<li>Andare in <code>assets/js/main.js</code></li>
				<li>Nella funzione <code>applyLanguageChanges()</code></li>
				<li>Rimuovere/commentare la riga: <code>return; // Esci dalla funzione</code></li>
				<li>Il sistema tornerà automaticamente operativo</li>
			</ol>

			<p><strong>File coinvolti:</strong></p>
			<ul>
				<li><code>functions.php</code> - API e traduzioni</li>
				<li><code>assets/js/main.js</code> - Frontend JavaScript</li>
				<li><code>header.php</code> - Selettore lingua</li>
				<li><code>index.php</code> - Elementi traducibili</li>
			</ul>
		</div>
	</div>
	<?php
}

// Aggiungi ajaxurl per il frontend
function add_ajax_url_to_frontend()
{
	if (!is_admin()) {
		echo '<script type="text/javascript">var ajaxurl = "' . admin_url('admin-ajax.php') . '";</script>';
	}
}
add_action('wp_head', 'add_ajax_url_to_frontend');

/**
 * Sanitize Footer Layout Type
 */
function marcello_scavo_sanitize_footer_layout($input)
{
	$valid = array('one_column', 'two_columns', 'three_columns', 'four_columns');
	return in_array($input, $valid) ? $input : 'three_columns';
}

/**
 * Sanitize Social Style
 */
function marcello_scavo_sanitize_social_style($input)
{
	$valid = array('modern', 'minimal', 'buttons', 'cards');
	return in_array($input, $valid) ? $input : 'modern';
}

/**
 * Register Additional Footer Widget Areas Based on Layout
 */
function marcello_scavo_register_additional_footer_widgets()
{
	$footer_layout = get_theme_mod('footer_layout_type', 'three_columns');

	// Se il layout è quattro colonne, registra la quarta area
	if ($footer_layout === 'four_columns') {
		register_sidebar(array(
			'name'          => __('Footer Colonna 4', 'marcello-scavo-tattoo'),
			'id'            => 'footer-4',
			'description'   => __('Widget per la quarta colonna del footer (solo con layout 4 colonne).', 'marcello-scavo-tattoo'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		));
	}
}
add_action('widgets_init', 'marcello_scavo_register_additional_footer_widgets', 20);

/**
 * Add admin styles
 */
function marcello_scavo_admin_styles()
{
	echo '<style>
        .form-table th { width: 150px; }
        .widget-title { color: var(--primary-gold); }
        .booking-status-pending { color: #f59e0b; }
        .booking-status-confirmed { color: #10b981; }
        .booking-status-completed { color: #6b7280; }
        .booking-status-cancelled { color: #ef4444; }
    </style>';
}
add_action('admin_head', 'marcello_scavo_admin_styles');

/**
 * Theme support for multilingual
 */
function marcello_scavo_languages_setup()
{
	$languages_dir = get_template_directory() . '/languages';
	if (!file_exists($languages_dir)) {
		wp_mkdir_p($languages_dir);
	}
}
add_action('after_setup_theme', 'marcello_scavo_languages_setup');

/**
 * Gallery Management Admin Enhancements
 */
function marcello_scavo_gallery_admin_enhancements()
{
	// Add custom columns to gallery list
	add_filter('manage_gallery_posts_columns', 'marcello_scavo_gallery_custom_columns');
	add_action('manage_gallery_posts_custom_column', 'marcello_scavo_gallery_custom_column_content', 10, 2);

	// Add quick stats widget to gallery dashboard
	add_action('admin_notices', 'marcello_scavo_gallery_admin_notice');
}
add_action('admin_init', 'marcello_scavo_gallery_admin_enhancements');

/**
 * Custom columns for gallery post type
 */
function marcello_scavo_gallery_custom_columns($columns)
{
	$new_columns = array();
	$new_columns['cb'] = $columns['cb'];
	$new_columns['image'] = __('Immagine', 'marcello-scavo-tattoo');
	$new_columns['title'] = $columns['title'];
	$new_columns['gallery_category'] = __('Categoria', 'marcello-scavo-tattoo');
	$new_columns['technique'] = __('Tecnica', 'marcello-scavo-tattoo');
	$new_columns['featured'] = __('In Evidenza', 'marcello-scavo-tattoo');
	$new_columns['date'] = $columns['date'];

	return $new_columns;
}

/**
 * Custom column content for gallery post type
 */
function marcello_scavo_gallery_custom_column_content($column, $post_id)
{
	switch ($column) {
		case 'image':
			if (has_post_thumbnail($post_id)) {
				echo '<img src="' . get_the_post_thumbnail_url($post_id, 'thumbnail') . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 3px;">';
			} else {
				echo '<div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 3px; display: flex; align-items: center; justify-content: center;"><span style="color: #999;">📷</span></div>';
			}
			break;

		case 'gallery_category':
			$terms = get_the_terms($post_id, 'gallery_category');
			if (!empty($terms)) {
				$term_names = array();
				foreach ($terms as $term) {
					$term_names[] = $term->name;
				}
				echo implode(', ', $term_names);
			} else {
				echo '<span style="color: #999;">' . __('Nessuna categoria', 'marcello-scavo-tattoo') . '</span>';
			}
			break;

		case 'technique':
			$technique = get_post_meta($post_id, '_gallery_image_technique', true);
			if ($technique) {
				echo esc_html(str_replace('_', ' ', ucfirst($technique)));
			} else {
				echo '<span style="color: #999;">-</span>';
			}
			break;

		case 'featured':
			$is_featured = get_post_meta($post_id, '_gallery_is_featured', true);
			if ($is_featured) {
				echo '<span style="color: #c9b05f; font-weight: bold;">★ ' . __('Sì', 'marcello-scavo-tattoo') . '</span>';
			} else {
				echo '<span style="color: #999;">' . __('No', 'marcello-scavo-tattoo') . '</span>';
			}
			break;
	}
}

/**
 * Gallery admin notice with stats
 */
function marcello_scavo_gallery_admin_notice()
{
	$screen = get_current_screen();

	if ($screen->post_type === 'gallery' && $screen->base === 'edit') {
		$total_items = wp_count_posts('gallery')->publish;
		$featured_items = get_posts(array(
			'post_type' => 'gallery',
			'meta_key' => '_gallery_is_featured',
			'meta_value' => '1',
			'posts_per_page' => -1,
			'fields' => 'ids'
		));
		$featured_count = count($featured_items);

		echo '<div class="notice notice-info" style="padding: 15px; display: flex; gap: 30px; align-items: center;">';
		echo '<div><strong>' . __('Statistiche Galleria:', 'marcello-scavo-tattoo') . '</strong></div>';
		echo '<div>📷 ' . $total_items . ' ' . __('immagini totali', 'marcello-scavo-tattoo') . '</div>';
		echo '<div>⭐ ' . $featured_count . ' ' . __('in evidenza', 'marcello-scavo-tattoo') . '</div>';
		echo '<div><a href="' . home_url('/#gallery-btn') . '" target="_blank" class="button button-small">' . __('Visualizza Galleria Frontend', 'marcello-scavo-tattoo') . '</a></div>';
		echo '</div>';
	}
}

/**
 * Custom Widget: Portfolio Recent Works
 */
class Marcello_Scavo_Portfolio_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_portfolio_widget',
			__('Portfolio Recenti', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Mostra i lavori del portfolio più recenti.', 'marcello-scavo-tattoo'),
				'classname' => 'widget_marcello_portfolio',
			)
		);
	}

	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);
		$number = isset($instance['number']) ? absint($instance['number']) : 3;

		echo $args['before_widget'];

		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$portfolio_posts = get_posts(array(
			'post_type' => 'portfolio',
			'posts_per_page' => $number,
			'post_status' => 'publish'
		));

		if ($portfolio_posts) {
			echo '<div class="portfolio-widget-list">';
			foreach ($portfolio_posts as $post) {
				setup_postdata($post);
				echo '<div class="portfolio-widget-item">';

				if (has_post_thumbnail($post->ID)) {
					echo '<div class="portfolio-widget-thumb">';
					echo '<a href="' . get_permalink($post->ID) . '">';
					echo get_the_post_thumbnail($post->ID, 'thumbnail');
					echo '</a>';
					echo '</div>';
				}

				echo '<div class="portfolio-widget-content">';
				echo '<h4 class="portfolio-widget-title">';
				echo '<a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a>';
				echo '</h4>';
				echo '<span class="portfolio-widget-date">' . get_the_date('', $post->ID) . '</span>';
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<p>' . __('Nessun lavoro nel portfolio al momento.', 'marcello-scavo-tattoo') . '</p>';
		}

		echo $args['after_widget'];
	}

	public function form($instance)
	{
		$title = isset($instance['title']) ? $instance['title'] : __('Portfolio Recenti', 'marcello-scavo-tattoo');
		$number = isset($instance['number']) ? absint($instance['number']) : 3;
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Numero di lavori da mostrare:', 'marcello-scavo-tattoo'); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3">
		</p>
	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 3;
		return $instance;
	}
}

/**
 * Custom Widget: Contact Info
 */
class Marcello_Scavo_Contact_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_contact_widget',
			__('Info Contatto', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Mostra le informazioni di contatto dello studio.', 'marcello-scavo-tattoo'),
				'classname' => 'widget_marcello_contact',
			)
		);
	}

	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);

		echo $args['before_widget'];

		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo '<div class="contact-widget-info">';

		// Milano
		echo '<div class="contact-location">';
		echo '<h4><i class="fas fa-map-marker-alt"></i> ' . __('Milano', 'marcello-scavo-tattoo') . '</h4>';
		echo '<p>' . nl2br(esc_html(get_theme_mod('contact_milano_address', 'Via Example, 123\n20121 Milano (MI)'))) . '</p>';
		echo '</div>';

		// Messina
		echo '<div class="contact-location">';
		echo '<h4><i class="fas fa-map-marker-alt"></i> ' . __('Messina', 'marcello-scavo-tattoo') . '</h4>';
		echo '<p>' . nl2br(esc_html(get_theme_mod('contact_messina_address', 'Via Example, 456\n98121 Messina (ME)'))) . '</p>';
		echo '</div>';

		// Phone and Email
		echo '<div class="contact-details">';
		$phone = get_theme_mod('contact_phone', '+39 123 456 7890');
		$email = get_theme_mod('contact_email', 'info@marcelloscavo.com');

		echo '<p><i class="fas fa-phone"></i> <a href="tel:' . esc_attr(str_replace(' ', '', $phone)) . '">' . esc_html($phone) . '</a></p>';
		echo '<p><i class="fas fa-envelope"></i> <a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></p>';
		echo '</div>';

		echo '</div>';

		echo $args['after_widget'];
	}

	public function form($instance)
	{
		$title = isset($instance['title']) ? $instance['title'] : __('Contatti Studio', 'marcello-scavo-tattoo');
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>
		<p><em><?php _e('Le informazioni di contatto vengono prese dalle opzioni del tema nel Customizer.', 'marcello-scavo-tattoo'); ?></em></p>
	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		return $instance;
	}
}

/**
 * Enqueue Media Library scripts for Instagram widget uploader
 */
function marcello_scavo_admin_scripts($hook)
{
	// Load on widgets page, customizer and block editor
	if ('widgets.php' == $hook || 'customize.php' == $hook || $hook == 'post.php' || $hook == 'post-new.php') {
		wp_enqueue_media();
		wp_enqueue_script('jquery');

		// Add debug info
		add_action('admin_footer', 'marcello_scavo_debug_media_scripts');
	}
}
add_action('admin_enqueue_scripts', 'marcello_scavo_admin_scripts');

// Debug function to check if scripts are loaded
function marcello_scavo_debug_media_scripts()
{
	?>
	<script>
		console.log('🔍 Debug Media Library:', {
			wp: typeof wp,
			wpMedia: typeof wp !== 'undefined' ? typeof wp.media : 'wp not found',
			jQuery: typeof jQuery
		});
	</script>
	<?php
}

// Also enqueue for customizer
function marcello_scavo_customize_controls_enqueue()
{
	wp_enqueue_media();
	wp_enqueue_script('jquery');
}
add_action('customize_controls_enqueue_scripts', 'marcello_scavo_customize_controls_enqueue');

// Force load media scripts on widget admin
function marcello_scavo_widget_admin_scripts()
{
	global $pagenow;
	if ($pagenow == 'widgets.php') {
		wp_enqueue_media();
		wp_enqueue_script('jquery');
	}
}
add_action('admin_init', 'marcello_scavo_widget_admin_scripts');

// AJAX handler for getting attachment data
function marcello_scavo_get_attachment_data()
{
	// Verify nonce
	if (!wp_verify_nonce($_POST['nonce'], 'get_attachment_data')) {
		wp_die('Nonce verification failed');
	}

	$attachment_id = intval($_POST['attachment_id']);

	if (!$attachment_id) {
		wp_send_json_error('ID allegato non valido');
	}

	// Check if attachment exists and is an image
	if (!wp_attachment_is_image($attachment_id)) {
		wp_send_json_error('Allegato non è un\'immagine valida');
	}

	// Get attachment data
	$attachment = get_post($attachment_id);
	if (!$attachment) {
		wp_send_json_error('Allegato non trovato');
	}

	// Get image data
	$image_url = wp_get_attachment_image_url($attachment_id, 'medium');
	if (!$image_url) {
		$image_url = wp_get_attachment_image_url($attachment_id, 'full');
	}

	$data = array(
		'id' => $attachment_id,
		'url' => $image_url,
		'filename' => basename(get_attached_file($attachment_id)),
		'mime' => get_post_mime_type($attachment_id),
		'sizes' => array(
			'medium' => array('url' => wp_get_attachment_image_url($attachment_id, 'medium')),
			'full' => array('url' => wp_get_attachment_image_url($attachment_id, 'full'))
		)
	);

	wp_send_json_success($data);
}
add_action('wp_ajax_get_attachment_data', 'marcello_scavo_get_attachment_data');

/**
 * Custom Widget: Contact Info Card
 */
class Marcello_Scavo_Contact_Card_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_scavo_contact_card',
			__('📧 Carta Contatto Personalizzata', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Widget per creare carte contatto personalizzate con icone, titoli e azioni nella sezione contatti.', 'marcello-scavo-tattoo'),
				'classname' => 'marcello-scavo-contact-card-widget'
			)
		);
	}

	public function widget($args, $instance)
	{
		$title = !empty($instance['title']) ? $instance['title'] : '';
		$description = !empty($instance['description']) ? $instance['description'] : '';
		$icon_class = !empty($instance['icon_class']) ? $instance['icon_class'] : 'fas fa-envelope';
		$button_text = !empty($instance['button_text']) ? $instance['button_text'] : '';
		$button_link = !empty($instance['button_link']) ? $instance['button_link'] : '';
		$contact_info = !empty($instance['contact_info']) ? $instance['contact_info'] : '';
		$card_style = !empty($instance['card_style']) ? $instance['card_style'] : 'default';

		// Non usiamo before_widget/after_widget per integrarci nel layout a 3 colonne
	?>

		<div class="contact-method contact-card-<?php echo esc_attr($card_style); ?>">
			<div class="contact-icon">
				<i class="<?php echo esc_attr($icon_class); ?>"></i>
			</div>

			<?php if ($title) : ?>
				<h3><?php echo esc_html($title); ?></h3>
			<?php endif; ?>

			<?php if ($description) : ?>
				<p><?php echo wp_kses_post($description); ?></p>
			<?php endif; ?>

			<?php if ($contact_info) : ?>
				<div class="contact-info">
					<?php
					// Processare ogni riga delle info di contatto
					$info_lines = explode("\n", $contact_info);
					foreach ($info_lines as $line) {
						$line = trim($line);
						if (!empty($line)) {
							echo '<p>' . wp_kses_post($line) . '</p>';
						}
					}
					?>
				</div>
			<?php endif; ?>

			<?php if ($button_text && $button_link) : ?>
				<div class="contact-action">
					<a href="<?php echo esc_url($button_link); ?>" class="btn-contact"
						<?php echo (strpos($button_link, 'http') === 0) ? 'target="_blank" rel="noopener"' : ''; ?>>
						<i class="fas fa-arrow-right"></i>
						<?php echo esc_html($button_text); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>

	<?php
	}

	public function form($instance)
	{
		$title = isset($instance['title']) ? $instance['title'] : '';
		$description = isset($instance['description']) ? $instance['description'] : '';
		$icon_class = isset($instance['icon_class']) ? $instance['icon_class'] : 'fas fa-envelope';
		$button_text = isset($instance['button_text']) ? $instance['button_text'] : '';
		$button_link = isset($instance['button_link']) ? $instance['button_link'] : '';
		$contact_info = isset($instance['contact_info']) ? $instance['contact_info'] : '';
		$card_style = isset($instance['card_style']) ? $instance['card_style'] : 'default';
	?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" placeholder="es. Scrivici un messaggio">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Descrizione:', 'marcello-scavo-tattoo'); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" rows="3" placeholder="Descrizione della carta contatto..."><?php echo esc_textarea($description); ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('icon_class'); ?>"><?php _e('Classe Icona FontAwesome:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('icon_class'); ?>" name="<?php echo $this->get_field_name('icon_class'); ?>" type="text" value="<?php echo esc_attr($icon_class); ?>" placeholder="fas fa-envelope">
			<small>Esempi: fas fa-envelope, fas fa-phone, fas fa-map-marker-alt, fas fa-share-alt</small>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('contact_info'); ?>"><?php _e('Informazioni di Contatto (opzionale):', 'marcello-scavo-tattoo'); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('contact_info'); ?>" name="<?php echo $this->get_field_name('contact_info'); ?>" rows="4" placeholder="📞 +39 123 456 7890&#10;📧 info@example.com&#10;📍 Via Example 123, Milano"><?php echo esc_textarea($contact_info); ?></textarea>
			<small>Usa emoji o codici HTML per icone. Ogni riga = una informazione.</small>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('button_text'); ?>"><?php _e('Testo Pulsante (opzionale):', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo esc_attr($button_text); ?>" placeholder="Invia Messaggio">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('button_link'); ?>"><?php _e('Link Pulsante (opzionale):', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('button_link'); ?>" name="<?php echo $this->get_field_name('button_link'); ?>" type="url" value="<?php echo esc_attr($button_link); ?>" placeholder="mailto:info@example.com o #contact-form">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('card_style'); ?>"><?php _e('Stile Carta:', 'marcello-scavo-tattoo'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('card_style'); ?>" name="<?php echo $this->get_field_name('card_style'); ?>">
				<option value="default" <?php selected($card_style, 'default'); ?>><?php _e('Default', 'marcello-scavo-tattoo'); ?></option>
				<option value="highlight" <?php selected($card_style, 'highlight'); ?>><?php _e('Evidenziato (Oro)', 'marcello-scavo-tattoo'); ?></option>
				<option value="primary" <?php selected($card_style, 'primary'); ?>><?php _e('Primario (Navy)', 'marcello-scavo-tattoo'); ?></option>
			</select>
		</p>

		<div style="background: #f0f0f0; padding: 10px; border-radius: 5px; margin-top: 15px;">
			<h4 style="margin: 0 0 10px 0;">💡 Suggerimenti:</h4>
			<ul style="margin: 0; padding-left: 20px; font-size: 12px;">
				<li><strong>Icone:</strong> Usa FontAwesome (fas fa-envelope, fas fa-phone, etc.)</li>
				<li><strong>Links:</strong> mailto:, tel:, #id, URLs complete</li>
				<li><strong>Info Contatto:</strong> Una per riga, usa emoji per le icone</li>
				<li><strong>Stili:</strong> Default (bianco), Highlight (oro), Primary (navy)</li>
			</ul>
		</div>

	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
		$instance['description'] = (!empty($new_instance['description'])) ? wp_kses_post($new_instance['description']) : '';
		$instance['icon_class'] = (!empty($new_instance['icon_class'])) ? sanitize_text_field($new_instance['icon_class']) : 'fas fa-envelope';
		$instance['button_text'] = (!empty($new_instance['button_text'])) ? sanitize_text_field($new_instance['button_text']) : '';
		$instance['button_link'] = (!empty($new_instance['button_link'])) ? esc_url_raw($new_instance['button_link']) : '';
		$instance['contact_info'] = (!empty($new_instance['contact_info'])) ? wp_kses_post($new_instance['contact_info']) : '';
		$instance['card_style'] = (!empty($new_instance['card_style'])) ? sanitize_text_field($new_instance['card_style']) : 'default';

		return $instance;
	}
}

/**
 * Custom Widget: Location Map
 */
class Marcello_Scavo_Location_Map_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_scavo_location_map',
			__('🗺️ Mappa Personalizzata', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Widget per creare mappe di localizzazione personalizzate con Google Maps, OpenStreetMap o iframe personalizzati.', 'marcello-scavo-tattoo'),
				'classname' => 'marcello-scavo-location-map-widget'
			)
		);
	}

	public function widget($args, $instance)
	{
		$map_type = !empty($instance['map_type']) ? $instance['map_type'] : 'google';
		$address = !empty($instance['address']) ? $instance['address'] : '';
		$latitude = !empty($instance['latitude']) ? $instance['latitude'] : '';
		$longitude = !empty($instance['longitude']) ? $instance['longitude'] : '';
		$zoom_level = !empty($instance['zoom_level']) ? absint($instance['zoom_level']) : 15;
		$map_height = !empty($instance['map_height']) ? absint($instance['map_height']) : 400;
		$custom_iframe = !empty($instance['custom_iframe']) ? $instance['custom_iframe'] : '';
		$api_key = !empty($instance['api_key']) ? $instance['api_key'] : '';

		// Non usiamo before_widget/after_widget per occupare tutta la sezione
	?>

		<div class="location-map-container" style="height: <?php echo esc_attr($map_height); ?>px;">
			<?php if ($map_type === 'custom' && !empty($custom_iframe)) : ?>
				<!-- Iframe personalizzato -->
				<div class="custom-map-iframe">
					<?php echo wp_kses($custom_iframe, array(
						'iframe' => array(
							'src' => array(),
							'width' => array(),
							'height' => array(),
							'frameborder' => array(),
							'style' => array(),
							'allowfullscreen' => array(),
							'loading' => array(),
							'referrerpolicy' => array()
						)
					)); ?>
				</div>

			<?php elseif ($map_type === 'openstreetmap' && (!empty($address) || (!empty($latitude) && !empty($longitude)))) : ?>
				<!-- OpenStreetMap -->
				<?php
				$map_id = 'osm-map-' . uniqid();

				// Se abbiamo solo l'indirizzo, proviamo a geocodificarlo con miglioramenti
				if (!empty($address) && (empty($latitude) || empty($longitude))) {

					// Migliora la query di ricerca per l'Italia
					$enhanced_address = $address;

					// Aggiungi "Italia" se non è presente
					if (stripos($enhanced_address, 'italia') === false && stripos($enhanced_address, 'italy') === false) {
						$enhanced_address .= ', Italia';
					}

					// Prova multiple strategie di geocoding
					$geocoding_attempts = array(
						// Tentativo 1: Indirizzo completo con Italia
						'https://nominatim.openstreetmap.org/search?format=json&countrycodes=it&addressdetails=1&limit=1&q=' . urlencode($enhanced_address),
						// Tentativo 2: Solo città se la ricerca precedente fallisce
						'https://nominatim.openstreetmap.org/search?format=json&countrycodes=it&addressdetails=1&limit=1&q=' . urlencode($this->extract_city($address) . ', Italia'),
						// Tentativo 3: Ricerca base
						'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($address) . '&limit=1'
					);

					$geocoding_success = false;

					foreach ($geocoding_attempts as $geocode_url) {
						$response = wp_remote_get($geocode_url, array(
							'timeout' => 15,
							'user-agent' => 'MarcelloScavoTattoo/1.0 (WordPress)',
							'headers' => array(
								'Accept' => 'application/json'
							)
						));

						if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
							$body = wp_remote_retrieve_body($response);
							$data = json_decode($body, true);

							if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
								$latitude = $data[0]['lat'];
								$longitude = $data[0]['lon'];
								$geocoding_success = true;
								break;
							}
						}

						// Pausa tra i tentativi per rispettare i rate limits
						if (!$geocoding_success) {
							usleep(500000); // 0.5 secondi
						}
					}

					// Fallback con coordinate predefinite per città italiane principali
					if (!$geocoding_success) {
						$coordinates = $this->get_italian_city_coordinates($address);
						$latitude = $coordinates['lat'];
						$longitude = $coordinates['lng'];
					}
				}
				?>

				<div id="<?php echo esc_attr($map_id); ?>" class="osm-map"
					data-lat="<?php echo esc_attr($latitude); ?>"
					data-lng="<?php echo esc_attr($longitude); ?>"
					data-zoom="<?php echo esc_attr($zoom_level); ?>"
					data-address="<?php echo esc_attr($address); ?>">
				</div>

				<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
					integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
					crossorigin="" />
				<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
					integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
					crossorigin=""></script>

				<script>
					document.addEventListener('DOMContentLoaded', function() {
						const mapElement = document.getElementById('<?php echo esc_js($map_id); ?>');
						if (mapElement && typeof L !== 'undefined') {
							const lat = parseFloat(mapElement.dataset.lat) || 45.4642;
							const lng = parseFloat(mapElement.dataset.lng) || 9.1900;
							const zoom = parseInt(mapElement.dataset.zoom) || 15;
							const address = mapElement.dataset.address || '';

							try {
								const map = L.map('<?php echo esc_js($map_id); ?>').setView([lat, lng], zoom);

								L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
									attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
									maxZoom: 19
								}).addTo(map);

								const popupContent = address || '<?php echo esc_js(get_bloginfo('name')); ?>';
								L.marker([lat, lng]).addTo(map)
									.bindPopup(popupContent)
									.openPopup();

								// Assicurati che la mappa sia completamente caricata
								setTimeout(function() {
									map.invalidateSize();
								}, 250);

							} catch (error) {
								console.error('Errore nell\'inizializzazione della mappa:', error);
								mapElement.innerHTML = '<div class="map-error"><p>Errore nel caricamento della mappa</p></div>';
							}
						} else {
							console.error('Elemento mappa non trovato o Leaflet non caricato');
						}
					});
				</script>

			<?php else : ?>
				<!-- Fallback placeholder -->
				<div class="map-placeholder">
					<div class="map-content">
						<i class="fas fa-map-marked-alt"></i>
						<h4>Configura la Mappa</h4>
						<p>Inserisci l'indirizzo o le coordinate nel widget per visualizzare la mappa.</p>
					</div>
				</div>
			<?php endif; ?>
		</div>

	<?php
	}

	public function form($instance)
	{
		$map_type = isset($instance['map_type']) ? $instance['map_type'] : 'openstreetmap';
		$address = isset($instance['address']) ? $instance['address'] : '';
		$latitude = isset($instance['latitude']) ? $instance['latitude'] : '';
		$longitude = isset($instance['longitude']) ? $instance['longitude'] : '';
		$zoom_level = isset($instance['zoom_level']) ? $instance['zoom_level'] : 15;
		$map_height = isset($instance['map_height']) ? $instance['map_height'] : 400;
		$custom_iframe = isset($instance['custom_iframe']) ? $instance['custom_iframe'] : '';
		$api_key = isset($instance['api_key']) ? $instance['api_key'] : '';
	?>

		<p>
			<label for="<?php echo $this->get_field_id('map_type'); ?>"><?php _e('Tipo di Mappa:', 'marcello-scavo-tattoo'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('map_type'); ?>" name="<?php echo $this->get_field_name('map_type'); ?>">
				<option value="openstreetmap" <?php selected($map_type, 'openstreetmap'); ?>><?php _e('OpenStreetMap (Gratis)', 'marcello-scavo-tattoo'); ?></option>
				<option value="custom" <?php selected($map_type, 'custom'); ?>><?php _e('Iframe Personalizzato', 'marcello-scavo-tattoo'); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('address'); ?>"><?php _e('Indirizzo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('address'); ?>" name="<?php echo $this->get_field_name('address'); ?>" type="text" value="<?php echo esc_attr($address); ?>" placeholder="Via Example 123, Milano, Italia">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('latitude'); ?>"><?php _e('Latitudine:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('latitude'); ?>" name="<?php echo $this->get_field_name('latitude'); ?>" type="text" value="<?php echo esc_attr($latitude); ?>" placeholder="45.4642">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('longitude'); ?>"><?php _e('Longitudine:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('longitude'); ?>" name="<?php echo $this->get_field_name('longitude'); ?>" type="text" value="<?php echo esc_attr($longitude); ?>" placeholder="9.1900">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('zoom_level'); ?>"><?php _e('Livello Zoom (1-20):', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('zoom_level'); ?>" name="<?php echo $this->get_field_name('zoom_level'); ?>" type="number" min="1" max="20" value="<?php echo esc_attr($zoom_level); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('map_height'); ?>"><?php _e('Altezza Mappa (px):', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('map_height'); ?>" name="<?php echo $this->get_field_name('map_height'); ?>" type="number" min="200" max="800" value="<?php echo esc_attr($map_height); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('custom_iframe'); ?>"><?php _e('Iframe Personalizzato:', 'marcello-scavo-tattoo'); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('custom_iframe'); ?>" name="<?php echo $this->get_field_name('custom_iframe'); ?>" rows="4" placeholder='<iframe src="..." width="100%" height="400" frameborder="0"></iframe>'><?php echo esc_textarea($custom_iframe); ?></textarea>
			<small><?php _e('Solo per "Iframe Personalizzato". Incolla qui il codice iframe da Google Maps, Bing o altri servizi.', 'marcello-scavo-tattoo'); ?></small>
		</p>

		<div style="background: #f0f0f0; padding: 10px; border-radius: 5px; margin-top: 15px;">
			<h4 style="margin: 0 0 10px 0;">💡 Suggerimenti:</h4>
			<ul style="margin: 0; padding-left: 20px; font-size: 12px;">
				<li><strong>OpenStreetMap:</strong> Gratuito, nessuna API key richiesta</li>
				<li><strong>Coordinate:</strong> Usa Google Maps per trovarle (click destro → "Cosa c'è qui?")</li>
				<li><strong>Iframe:</strong> Copia da Google Maps → Condividi → Incorpora mappa</li>
				<li><strong>Altezza:</strong> Consigliata 400-500px per una buona visibilità</li>
			</ul>
		</div>

	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['map_type'] = (!empty($new_instance['map_type'])) ? sanitize_text_field($new_instance['map_type']) : 'openstreetmap';
		$instance['address'] = (!empty($new_instance['address'])) ? sanitize_text_field($new_instance['address']) : '';
		$instance['latitude'] = (!empty($new_instance['latitude'])) ? sanitize_text_field($new_instance['latitude']) : '';
		$instance['longitude'] = (!empty($new_instance['longitude'])) ? sanitize_text_field($new_instance['longitude']) : '';
		$instance['zoom_level'] = (!empty($new_instance['zoom_level'])) ? absint($new_instance['zoom_level']) : 15;
		$instance['map_height'] = (!empty($new_instance['map_height'])) ? absint($new_instance['map_height']) : 400;
		$instance['custom_iframe'] = (!empty($new_instance['custom_iframe'])) ? wp_kses($new_instance['custom_iframe'], array(
			'iframe' => array(
				'src' => array(),
				'width' => array(),
				'height' => array(),
				'frameborder' => array(),
				'style' => array(),
				'allowfullscreen' => array(),
				'loading' => array(),
				'referrerpolicy' => array()
			)
		)) : '';
		$instance['api_key'] = (!empty($new_instance['api_key'])) ? sanitize_text_field($new_instance['api_key']) : '';

		return $instance;
	}

	/**
	 * Estrae il nome della città da un indirizzo
	 */
	private function extract_city($address)
	{
		$address_parts = explode(',', $address);

		foreach ($address_parts as $part) {
			$part = trim($part);

			// Lista di città italiane comuni
			$italian_cities = array('milano', 'roma', 'napoli', 'torino', 'palermo', 'genova', 'bologna', 'firenze', 'bari', 'catania', 'venezia', 'verona', 'messina', 'padova', 'trieste', 'brescia', 'prato', 'taranto', 'modena', 'reggio calabria', 'reggio emilia', 'perugia', 'livorno', 'ravenna', 'cagliari', 'foggia', 'rimini', 'salerno', 'ferrara', 'sassari', 'latina', 'giugliano in campania', 'monza', 'siracusa', 'pescara', 'bergamo', 'forlì', 'trento', 'vicenza', 'terni', 'bolzano', 'novara', 'piacenza', 'ancona', 'andria', 'arezzo', 'udine', 'cesena', 'lecce', 'pesaro', 'barletta', 'alessandria', 'la spezia', 'pistoia', 'como', 'pisa', 'cremona', 'parma');

			foreach ($italian_cities as $city) {
				if (stripos($part, $city) !== false) {
					return $city;
				}
			}
		}

		// Se non trova una città riconosciuta, ritorna la prima parte non numerica
		foreach ($address_parts as $part) {
			$part = trim($part);
			if (!is_numeric($part) && strlen($part) > 3) {
				return $part;
			}
		}

		return $address;
	}

	/**
	 * Ottiene coordinate predefinite per le principali città italiane
	 */
	private function get_italian_city_coordinates($address)
	{
		$default_coordinates = array('lat' => '45.4642', 'lng' => '9.1900'); // Milano default

		$city_coordinates = array(
			'milano' => array('lat' => '45.4642', 'lng' => '9.1900'),
			'roma' => array('lat' => '41.9028', 'lng' => '12.4964'),
			'napoli' => array('lat' => '40.8518', 'lng' => '14.2681'),
			'torino' => array('lat' => '45.0703', 'lng' => '7.6869'),
			'palermo' => array('lat' => '38.1157', 'lng' => '13.3613'),
			'genova' => array('lat' => '44.4056', 'lng' => '8.9463'),
			'bologna' => array('lat' => '44.4949', 'lng' => '11.3426'),
			'firenze' => array('lat' => '43.7696', 'lng' => '11.2558'),
			'bari' => array('lat' => '41.1171', 'lng' => '16.8719'),
			'catania' => array('lat' => '37.5079', 'lng' => '15.0830'),
			'venezia' => array('lat' => '45.4408', 'lng' => '12.3155'),
			'verona' => array('lat' => '45.4384', 'lng' => '10.9916'),
			'messina' => array('lat' => '38.1938', 'lng' => '15.5540'),
			'padova' => array('lat' => '45.4064', 'lng' => '11.8768'),
			'trieste' => array('lat' => '45.6495', 'lng' => '13.7768'),
			'brescia' => array('lat' => '45.5416', 'lng' => '10.2118'),
			'prato' => array('lat' => '43.8777', 'lng' => '11.1020'),
			'taranto' => array('lat' => '40.4668', 'lng' => '17.2725'),
			'modena' => array('lat' => '44.6471', 'lng' => '10.9252'),
			'reggio calabria' => array('lat' => '38.1102', 'lng' => '15.6616'),
			'reggio emilia' => array('lat' => '44.6989', 'lng' => '10.6298'),
			'perugia' => array('lat' => '43.1122', 'lng' => '12.3888'),
			'livorno' => array('lat' => '43.5423', 'lng' => '10.3106'),
			'ravenna' => array('lat' => '44.4173', 'lng' => '12.1992'),
			'cagliari' => array('lat' => '39.2238', 'lng' => '9.1217'),
			'foggia' => array('lat' => '41.4621', 'lng' => '15.5444'),
			'rimini' => array('lat' => '44.0678', 'lng' => '12.5695'),
			'salerno' => array('lat' => '40.6824', 'lng' => '14.7681'),
			'ferrara' => array('lat' => '44.8381', 'lng' => '11.6198'),
			'sassari' => array('lat' => '40.7259', 'lng' => '8.5594'),
			'latina' => array('lat' => '41.4677', 'lng' => '12.9037'),
			'monza' => array('lat' => '45.5845', 'lng' => '9.2744'),
			'siracusa' => array('lat' => '37.0755', 'lng' => '15.2866'),
			'pescara' => array('lat' => '42.4584', 'lng' => '14.2048'),
			'bergamo' => array('lat' => '45.6983', 'lng' => '9.6773'),
			'forlì' => array('lat' => '44.2226', 'lng' => '12.0401'),
			'trento' => array('lat' => '46.0748', 'lng' => '11.1217'),
			'vicenza' => array('lat' => '45.5455', 'lng' => '11.5354'),
			'terni' => array('lat' => '42.5633', 'lng' => '12.6433'),
			'bolzano' => array('lat' => '46.4983', 'lng' => '11.3548'),
			'novara' => array('lat' => '45.4469', 'lng' => '8.6218'),
			'piacenza' => array('lat' => '45.0526', 'lng' => '9.6931'),
			'ancona' => array('lat' => '43.6158', 'lng' => '13.5189'),
			'andria' => array('lat' => '41.2284', 'lng' => '16.2983'),
			'arezzo' => array('lat' => '43.4633', 'lng' => '11.8796'),
			'udine' => array('lat' => '46.0748', 'lng' => '13.2335'),
			'cesena' => array('lat' => '44.1391', 'lng' => '12.2431'),
			'lecce' => array('lat' => '40.3515', 'lng' => '18.1750'),
			'pesaro' => array('lat' => '43.9102', 'lng' => '12.9130'),
			'barletta' => array('lat' => '41.3207', 'lng' => '16.2816'),
			'alessandria' => array('lat' => '44.9133', 'lng' => '8.6167'),
			'la spezia' => array('lat' => '44.1024', 'lng' => '9.8233'),
			'pistoia' => array('lat' => '43.9330', 'lng' => '10.9177'),
			'como' => array('lat' => '45.8081', 'lng' => '9.0852'),
			'pisa' => array('lat' => '43.7228', 'lng' => '10.4017'),
			'cremona' => array('lat' => '45.1335', 'lng' => '10.0422'),
			'parma' => array('lat' => '44.8015', 'lng' => '10.3279')
		);

		$address_lower = strtolower($address);

		foreach ($city_coordinates as $city => $coords) {
			if (stripos($address_lower, $city) !== false) {
				return $coords;
			}
		}

		return $default_coordinates;
	}
}

/**
 * Custom Widget: Instagram Feed with instafeed.js
 */
class Marcello_Scavo_Instagram_Feed_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_instagram_feed_widget',
			__('Instagram Feed (instafeed.js)', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Widget Instagram personalizzato che utilizza instafeed.js per mostrare le foto.', 'marcello-scavo-tattoo'),
				'classname' => 'widget_marcello_instagram_feed',
			)
		);
	}

	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);
		$access_token = isset($instance['access_token']) ? $instance['access_token'] : '';
		$num_photos = isset($instance['num_photos']) ? absint($instance['num_photos']) : 6;
		$show_profile = isset($instance['show_profile']) ? (bool) $instance['show_profile'] : true;
		$profile_username = isset($instance['profile_username']) ? $instance['profile_username'] : 'marcelloscavo_art';
		$profile_link = isset($instance['profile_link']) ? $instance['profile_link'] : 'https://instagram.com/marcelloscavo_art';
		$use_simple_mode = isset($instance['use_simple_mode']) ? (bool) $instance['use_simple_mode'] : false;

		echo $args['before_widget'];

		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo '<div class="instagram-feed-container">';

		// Profile Info Section
		if ($show_profile) {
			echo '<div class="instagram-profile-info">';
			echo '<div class="instagram-profile-details">';
			echo '<h4>@' . esc_html($profile_username) . '</h4>';
			echo '<p>' . __('Seguimi su Instagram per i miei ultimi lavori', 'marcello-scavo-tattoo') . '</p>';
			echo '</div>';
			echo '</div>';
		}

		// Instagram Feed Container
		echo '<div id="instagram-feed" class="instagram-grid"></div>';

		// Loading message
		echo '<div class="instagram-loading" id="instagram-loading">';
		echo '<i class="fas fa-spinner fa-spin"></i> ' . __('Caricamento feed Instagram...', 'marcello-scavo-tattoo');
		echo '</div>';

		// Error message container
		echo '<div class="instagram-error" id="instagram-error" style="display: none;"></div>';

		// Follow button
		if ($show_profile && !empty($profile_link)) {
			echo '<a href="' . esc_url($profile_link) . '" target="_blank" rel="noopener" class="instagram-follow-btn">';
			echo '<i class="fab fa-instagram"></i> ' . __('Seguimi su Instagram', 'marcello-scavo-tattoo');
			echo '</a>';
		}

		echo '</div>';

		// Choose loading method based on mode
		if ($use_simple_mode) {
			// Simple mode: show placeholder images with Instagram link
			$this->render_simple_instagram_feed($profile_username, $num_photos, $instance);
		} elseif (!empty($access_token)) {
			// API mode: use instafeed.js with access token
			$this->render_api_instagram_feed($access_token, $num_photos);
		} else {
			echo '<div class="instagram-error">' . __('Modalità API: Token di accesso Instagram non configurato. Modalità Semplice: Attiva l\'opzione nel widget.', 'marcello-scavo-tattoo') . '</div>';
		}

		echo $args['after_widget'];
	}

	private function render_simple_instagram_feed($username, $num_photos, $instance)
	{
		// Simple mode: create attractive placeholder grid that links to Instagram
		// Enhanced with error handling for production safety
	?>
		<script>
			jQuery(document).ready(function($) {
				try {
					// Hide loading message for simple mode
					$('#instagram-loading').hide();

					// Create simple Instagram grid
					var instagramGrid = $('#instagram-feed');
					instagramGrid.addClass('instagram-simple-grid');

					// Custom or default placeholder data with security
					var placeholderPosts = [
						<?php
						$default_posts = array(
							array('image' => 'https://picsum.photos/300/300?random=1', 'caption' => '🎨 Nuovo tatuaggio completato!'),
							array('image' => 'https://picsum.photos/300/300?random=2', 'caption' => '✨ Work in progress'),
							array('image' => 'https://picsum.photos/300/300?random=3', 'caption' => '🖋️ Sketch del giorno'),
							array('image' => 'https://picsum.photos/300/300?random=4', 'caption' => '🌟 Dettagli artistici'),
							array('image' => 'https://picsum.photos/300/300?random=5', 'caption' => '🎭 Arte e passione'),
							array('image' => 'https://picsum.photos/300/300?random=6', 'caption' => '💫 Creatività infinita')
						);

						for ($i = 1; $i <= 6; $i++) {
							$validated_image = '';
							$caption = '';
							$is_secure = false;

							try {
								// Safely get instance data
								$custom_image_id = isset($instance["placeholder_image_id_$i"]) ? absint($instance["placeholder_image_id_$i"]) : 0;
								$custom_image_url = isset($instance["placeholder_image_$i"]) ? $instance["placeholder_image_$i"] : '';
								$custom_caption = isset($instance["placeholder_caption_$i"]) ? $instance["placeholder_caption_$i"] : '';

								// Security validation for custom images
								if ($custom_image_id > 0 && function_exists('wp_attachment_is_image') && wp_attachment_is_image($custom_image_id)) {
									// Get secure URL from attachment ID
									if (function_exists('wp_get_attachment_image_url')) {
										$secure_url = wp_get_attachment_image_url($custom_image_id, 'medium');
										if (!$secure_url && function_exists('wp_get_attachment_image_url')) {
											$secure_url = wp_get_attachment_image_url($custom_image_id, 'full');
										}

										// Verify URL is from this site
										if ($secure_url && $this->is_local_attachment($secure_url)) {
											$validated_image = $secure_url;
											$is_secure = true;
										}
									}
								} else if (!empty($custom_image_url) && $this->is_local_attachment($custom_image_url)) {
									// Fallback to URL validation for existing data
									$validated_image = $custom_image_url;
									$is_secure = false;
								}

								if (!empty($validated_image)) {
									$caption = !empty($custom_caption) ? $custom_caption : $default_posts[$i - 1]['caption'];
								} else {
									$validated_image = $default_posts[$i - 1]['image'];
									$caption = $default_posts[$i - 1]['caption'];
									$is_secure = false;
								}
							} catch (Exception $e) {
								// Fallback to default on any error
								$validated_image = $default_posts[$i - 1]['image'];
								$caption = $default_posts[$i - 1]['caption'];
								$is_secure = false;
							}

							echo '{';
							echo 'image: "' . esc_js($validated_image) . '",';
							echo 'caption: "' . esc_js($caption) . '",';
							echo 'isSecure: ' . ($is_secure ? 'true' : 'false');
							echo '}';
							if ($i < 6) echo ',';
							echo "\n";
						}
						?>
					];

					// Limit to requested number
					var posts = placeholderPosts.slice(0, <?php echo intval($num_photos); ?>);

					posts.forEach(function(post, index) {
						var securityIcon = post.isSecure ? '<i class="fas fa-lock" style="font-size: 10px; color: #28a745;" title="Immagine sicura"></i> ' : '';

						var item = $('<a href="https://instagram.com/<?php echo esc_js($username); ?>" target="_blank" rel="noopener" class="instagram-item">' +
							'<div class="instagram-image">' +
							'<img src="' + post.image + '" alt="' + post.caption + '" loading="lazy" onerror="this.src=\'https://picsum.photos/300/300?random=' + (index + 1) + '\'">' +
							'<div class="instagram-overlay">' +
							'<div class="instagram-overlay-content">' +
							'<i class="fab fa-instagram"></i>' +
							'<span>' + securityIcon + post.caption + '</span>' +
							'</div>' +
							'</div>' +
							'</div>' +
							'</a>');

						instagramGrid.append(item);
					});

					// Add message about simple mode with security status
					var hasCustomImages = <?php
											$has_custom = false;
											$has_secure = false;
											try {
												for ($i = 1; $i <= 6; $i++) {
													if (!empty($instance["placeholder_image_$i"])) {
														$has_custom = true;
														$image_id = isset($instance["placeholder_image_id_$i"]) ? absint($instance["placeholder_image_id_$i"]) : 0;
														if ($image_id > 0 && function_exists('wp_attachment_is_image') && wp_attachment_is_image($image_id)) {
															$has_secure = true;
														}
													}
												}
											} catch (Exception $e) {
												$has_custom = false;
												$has_secure = false;
											}
											echo json_encode(array('custom' => $has_custom, 'secure' => $has_secure));
											?>;

					var noteText = '';
					if (hasCustomImages.secure) {
						noteText = '<?php echo esc_js(__('🔒 Immagini sicure caricate. Clicca per visitare Instagram.', 'marcello-scavo-tattoo')); ?>';
					} else if (hasCustomImages.custom) {
						noteText = '<?php echo esc_js(__('⚠️ Immagini personalizzate. Clicca per visitare Instagram.', 'marcello-scavo-tattoo')); ?>';
					} else {
						noteText = '<?php echo esc_js(__('📱 Modalità semplice attiva. Clicca sulle immagini per visitare Instagram.', 'marcello-scavo-tattoo')); ?>';
					}

					instagramGrid.after('<div class="instagram-simple-note"><small>' + noteText + '</small></div>');

				} catch (e) {
					console.error('Instagram widget error:', e);
					// Fallback: show basic message
					$('#instagram-feed').html('<div class="instagram-error">Widget Instagram in modalità di emergenza. <a href="https://instagram.com/<?php echo esc_js($username); ?>" target="_blank">Visita il profilo Instagram</a></div>');
				}
			});
		</script>
	<?php
	}

	private function render_api_instagram_feed($access_token, $num_photos)
	{
		// API mode: use instafeed.js with access token
	?>
		<script>
			jQuery(document).ready(function($) {
				// Wait for DOM and check if Instafeed is available
				function initInstagramFeed() {
					if (typeof Instafeed !== 'undefined') {
						console.log('Instafeed.js loaded successfully');
						try {
							var feed = new Instafeed({
								accessToken: '<?php echo esc_js($access_token); ?>',
								target: 'instagram-feed',
								limit: <?php echo intval($num_photos); ?>,
								template: '<a href="{{link}}" target="_blank" rel="noopener"><img src="{{image}}" alt="{{caption}}" /></a>',
								success: function() {
									console.log('Instagram feed loaded successfully');
									$('#instagram-loading').hide();
								},
								error: function(message) {
									console.error('Instagram Feed Error:', message);
									$('#instagram-loading').hide();
									$('#instagram-error').show().html('<?php echo esc_js(__('Errore nel caricamento del feed Instagram. Verifica il token di accesso.', 'marcello-scavo-tattoo')); ?>');
								}
							});

							feed.run();
						} catch (e) {
							console.error('Instagram Feed Exception:', e);
							$('#instagram-loading').hide();
							$('#instagram-error').show().html('<?php echo esc_js(__('Errore nell\'inizializzazione del feed Instagram.', 'marcello-scavo-tattoo')); ?>');
						}
					} else {
						console.error('Instafeed.js not loaded - trying alternative approach');
						// Fallback: load script manually
						if (!window.instafeedScriptLoaded) {
							var script = document.createElement('script');
							script.src = 'https://unpkg.com/instafeed.js@2.0.0-rc2/dist/instafeed.min.js';
							script.onload = function() {
								console.log('Instafeed.js loaded via fallback');
								window.instafeedScriptLoaded = true;
								setTimeout(initInstagramFeed, 100);
							};
							script.onerror = function() {
								console.error('Failed to load Instafeed.js from fallback CDN');
								$('#instagram-loading').hide();
								$('#instagram-error').show().html('<?php echo esc_js(__('Instafeed.js non è caricato. Verifica che lo script sia incluso correttamente.', 'marcello-scavo-tattoo')); ?>');
							};
							document.head.appendChild(script);
						}
					}
				}

				// Try to initialize after a short delay to allow scripts to load
				setTimeout(initInstagramFeed, 500);
			});
		</script>
	<?php
	}

	public function form($instance)
	{
		$title = isset($instance['title']) ? $instance['title'] : __('I miei ultimi post Instagram', 'marcello-scavo-tattoo');
		$access_token = isset($instance['access_token']) ? $instance['access_token'] : '';
		$num_photos = isset($instance['num_photos']) ? absint($instance['num_photos']) : 6;
		$show_profile = isset($instance['show_profile']) ? (bool) $instance['show_profile'] : true;
		$profile_username = isset($instance['profile_username']) ? $instance['profile_username'] : 'marcelloscavo_art';
		$profile_link = isset($instance['profile_link']) ? $instance['profile_link'] : 'https://instagram.com/marcelloscavo_art';
		$use_simple_mode = isset($instance['use_simple_mode']) ? (bool) $instance['use_simple_mode'] : false;

		// Placeholder images (6 slots)
		$placeholder_images = array();
		$placeholder_captions = array();
		for ($i = 1; $i <= 6; $i++) {
			$placeholder_images[$i] = isset($instance["placeholder_image_$i"]) ? $instance["placeholder_image_$i"] : '';
			$placeholder_captions[$i] = isset($instance["placeholder_caption_$i"]) ? $instance["placeholder_caption_$i"] : '';
		}
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('access_token'); ?>"><?php _e('Instagram Access Token:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('access_token'); ?>" name="<?php echo $this->get_field_name('access_token'); ?>" type="text" value="<?php echo esc_attr($access_token); ?>">
			<small><?php _e('Inserisci il tuo Instagram Access Token. Vedi la documentazione per maggiori dettagli.', 'marcello-scavo-tattoo'); ?></small>
			<?php if (!empty($access_token)): ?>
				<br><small style="color: green;">✓ <?php _e('Token configurato', 'marcello-scavo-tattoo'); ?></small>
			<?php else: ?>
				<br><small style="color: red;">✗ <?php _e('Token mancante', 'marcello-scavo-tattoo'); ?></small>
			<?php endif; ?>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('num_photos'); ?>"><?php _e('Numero di foto:', 'marcello-scavo-tattoo'); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id('num_photos'); ?>" name="<?php echo $this->get_field_name('num_photos'); ?>" type="number" step="1" min="1" max="20" value="<?php echo $num_photos; ?>" size="3">
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($show_profile); ?> id="<?php echo $this->get_field_id('show_profile'); ?>" name="<?php echo $this->get_field_name('show_profile'); ?>" />
			<label for="<?php echo $this->get_field_id('show_profile'); ?>"><?php _e('Mostra informazioni profilo', 'marcello-scavo-tattoo'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('profile_username'); ?>"><?php _e('Username Instagram:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('profile_username'); ?>" name="<?php echo $this->get_field_name('profile_username'); ?>" type="text" value="<?php echo esc_attr($profile_username); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('profile_link'); ?>"><?php _e('Link profilo Instagram:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('profile_link'); ?>" name="<?php echo $this->get_field_name('profile_link'); ?>" type="url" value="<?php echo esc_attr($profile_link); ?>">
		</p>

		<hr>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($use_simple_mode); ?> id="<?php echo $this->get_field_id('use_simple_mode'); ?>" name="<?php echo $this->get_field_name('use_simple_mode'); ?>" />
			<label for="<?php echo $this->get_field_id('use_simple_mode'); ?>"><?php _e('🔒 Usa modalità semplice con uploader sicuro', 'marcello-scavo-tattoo'); ?></label>
			<br><small><?php _e('Attiva questa opzione per caricare le tue immagini direttamente nella Media Library di WordPress. Include uploader sicuro integrato.', 'marcello-scavo-tattoo'); ?></small>
		</p>

		<p><strong><?php _e('Modalità di utilizzo:', 'marcello-scavo-tattoo'); ?></strong></p>
		<ul style="margin-left: 20px;">
			<li><strong><?php _e('Modalità API:', 'marcello-scavo-tattoo'); ?></strong> <?php _e('Inserisci il token di accesso Instagram per mostrare le foto reali del feed.', 'marcello-scavo-tattoo'); ?></li>
			<li><strong><?php _e('🔒 Modalità Semplice:', 'marcello-scavo-tattoo'); ?></strong> <?php _e('Carica le tue immagini personali con l\'uploader sicuro integrato. Le immagini vengono salvate nella Media Library di WordPress.', 'marcello-scavo-tattoo'); ?></li>
		</ul>

		<div id="placeholder-images-section" style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 20px;">
			<h4><?php _e('🎨 Immagini Personalizzate (Modalità Semplice)', 'marcello-scavo-tattoo'); ?></h4>
			<p><small><?php _e('Carica le tue immagini personalizzate per la modalità semplice. File sicuri salvati nella Media Library. Formati: JPG, PNG, WebP (max 2MB)', 'marcello-scavo-tattoo'); ?></small></p>

			<!-- Debug status -->
			<div id="media-status-debug" style="background: #f0f0f0; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 12px;">
				<strong>📊 Status Media Library:</strong> <span id="media-status-text">Controllando...</span>
				<button type="button" id="test-media-library" class="button button-small" style="margin-left: 10px;">🧪 Test Media Library</button>
			</div>

			<?php
			// Security nonce for image operations
			wp_nonce_field('instagram_widget_secure_images', 'instagram_widget_nonce');
			?>

			<?php for ($i = 1; $i <= 6; $i++):
				$image_id = isset($instance["placeholder_image_id_$i"]) ? absint($instance["placeholder_image_id_$i"]) : 0;
				$image_url = isset($instance["placeholder_image_$i"]) ? $instance["placeholder_image_$i"] : '';
				$caption = isset($instance["placeholder_caption_$i"]) ? $instance["placeholder_caption_$i"] : '';

				// Security check: verify image still exists and is accessible
				if ($image_id > 0) {
					if (!wp_attachment_is_image($image_id) || !current_user_can('edit_post', $image_id)) {
						$image_id = 0;
						$image_url = '';
					} else {
						// Get fresh URL from ID for security
						$fresh_url = wp_get_attachment_image_url($image_id, 'medium');
						if ($fresh_url) {
							$image_url = $fresh_url;
						}
					}
				}
			?>
				<div class="placeholder-image-row" style="border: 1px solid #e0e0e0; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
					<p style="margin: 0 0 10px 0;"><strong><?php printf(__('Immagine %d:', 'marcello-scavo-tattoo'), $i); ?></strong></p>

					<!-- Hidden fields for secure storage -->
					<input type="hidden"
						id="<?php echo $this->get_field_id("placeholder_image_id_$i"); ?>"
						name="<?php echo $this->get_field_name("placeholder_image_id_$i"); ?>"
						value="<?php echo esc_attr($image_id); ?>">

					<input type="hidden"
						id="<?php echo $this->get_field_id("placeholder_image_$i"); ?>"
						name="<?php echo $this->get_field_name("placeholder_image_$i"); ?>"
						value="<?php echo esc_attr($image_url); ?>">

					<div style="display: flex; gap: 10px; align-items: flex-start;">
						<div style="flex: 1;">
							<input class="widefat placeholder-image-display"
								id="<?php echo $this->get_field_id("placeholder_image_$i"); ?>_display"
								type="text"
								value="<?php echo $image_id ? sprintf(__('Immagine ID: %d (%s)', 'marcello-scavo-tattoo'), $image_id, basename($image_url)) : __('Nessuna immagine selezionata', 'marcello-scavo-tattoo'); ?>"
								readonly
								style="background: #f9f9f9; min-width: 250px;">
						</div>
						<button type="button" class="button upload-secure-image"
							data-target-id="<?php echo $this->get_field_id("placeholder_image_id_$i"); ?>"
							data-target-url="<?php echo $this->get_field_id("placeholder_image_$i"); ?>"
							data-target-display="<?php echo $this->get_field_id("placeholder_image_$i"); ?>_display"
							data-preview="<?php echo $this->get_field_id("placeholder_image_$i"); ?>_preview">
							<?php _e('🔒 Carica Sicuro', 'marcello-scavo-tattoo'); ?>
						</button>

						<button type="button" class="button upload-classic-image"
							data-target-id="<?php echo $this->get_field_id("placeholder_image_id_$i"); ?>"
							data-target-url="<?php echo $this->get_field_id("placeholder_image_$i"); ?>"
							data-target-display="<?php echo $this->get_field_id("placeholder_image_$i"); ?>_display"
							data-preview="<?php echo $this->get_field_id("placeholder_image_$i"); ?>_preview"
							style="margin-left: 5px; background: #28a745;">
							📁 Media Library
						</button>
					</div>

					<div id="<?php echo $this->get_field_id("placeholder_image_$i"); ?>_preview" style="margin-top: 10px;">
						<?php if (!empty($image_url) && $image_id > 0): ?>
							<div style="display: flex; align-items: center; gap: 10px;">
								<img src="<?php echo esc_url($image_url); ?>"
									style="max-width: 80px; height: auto; border-radius: 4px; border: 2px solid #ddd;"
									alt="Preview">
								<div>
									<p style="margin: 0; font-size: 11px; color: #666;">
										<strong><?php _e('Sicuro:', 'marcello-scavo-tattoo'); ?></strong> ID <?php echo $image_id; ?><br>
										<strong><?php _e('File:', 'marcello-scavo-tattoo'); ?></strong> <?php echo basename($image_url); ?>
									</p>
									<button type="button" class="button remove-secure-image"
										data-target-id="<?php echo $this->get_field_id("placeholder_image_id_$i"); ?>"
										data-target-url="<?php echo $this->get_field_id("placeholder_image_$i"); ?>"
										data-target-display="<?php echo $this->get_field_id("placeholder_image_$i"); ?>_display"
										data-preview="<?php echo $this->get_field_id("placeholder_image_$i"); ?>_preview"
										style="margin-top: 5px; color: #dc3545;">
										<?php _e('🗑️ Rimuovi', 'marcello-scavo-tattoo'); ?>
									</button>
								</div>
							</div>
						<?php endif; ?>
					</div>

					<p style="margin: 10px 0 0 0;">
						<label for="<?php echo $this->get_field_id("placeholder_caption_$i"); ?>"><?php _e('Didascalia:', 'marcello-scavo-tattoo'); ?></label>
						<input class="widefat"
							id="<?php echo $this->get_field_id("placeholder_caption_$i"); ?>"
							name="<?php echo $this->get_field_name("placeholder_caption_$i"); ?>"
							type="text"
							value="<?php echo esc_attr($caption); ?>"
							placeholder="<?php _e('es. 🎨 Nuovo tatuaggio completato!', 'marcello-scavo-tattoo'); ?>"
							maxlength="200">
					</p>
				</div>
			<?php endfor; ?>

			<div style="background: #e7f3ff; padding: 10px; border-radius: 5px; margin-top: 15px;">
				<p style="margin: 0; font-size: 12px;">
					<strong>🔒 <?php _e('Sicurezza:', 'marcello-scavo-tattoo'); ?></strong><br>
					• <?php _e('Le immagini sono salvate nella Media Library di WordPress', 'marcello-scavo-tattoo'); ?><br>
					• <?php _e('Solo immagini dal tuo sito sono accettate', 'marcello-scavo-tattoo'); ?><br>
					• <?php _e('Validazione automatica del tipo file (JPG, PNG, WebP)', 'marcello-scavo-tattoo'); ?><br>
					• <?php _e('Limite dimensione: 2MB per file', 'marcello-scavo-tattoo'); ?>
				</p>
			</div>
		</div>

		<script>
			var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

			jQuery(document).ready(function($) {
				console.log('🎨 Instagram Widget Uploader inizializzato');

				// Force load media scripts if not available
				function forceLoadMediaScripts() {
					if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
						console.log('🔄 Forzando il caricamento degli script media...');

						// Try to load media scripts dynamically
						var mediaScript = document.createElement('script');
						mediaScript.src = '<?php echo admin_url('js/media-upload.js'); ?>';
						document.head.appendChild(mediaScript);

						// Also try to initialize wp.media manually
						if (typeof wp !== 'undefined' && !wp.media) {
							$.getScript('<?php echo includes_url('js/media-views.min.js'); ?>', function() {
								console.log('📦 Media views script caricato');
							});
						}
					}
				}

				// Check initial status
				setTimeout(function() {
					if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
						$('#media-status-text').html('❌ Non trovata - Caricamento forzato...').css('color', 'red');
						forceLoadMediaScripts();
					}
				}, 1000);

				// Wait for wp.media to be available
				function waitForMedia(callback) {
					if (typeof wp !== 'undefined' && typeof wp.media !== 'undefined') {
						$('#media-status-text').html('✅ Pronta').css('color', 'green');
						callback();
					} else {
						$('#media-status-text').html('⏳ Caricamento...').css('color', 'orange');
						console.log('⏳ Aspettando che wp.media sia disponibile...');
						setTimeout(function() {
							waitForMedia(callback);
						}, 500);
					}
				}

				// Test button for Media Library
				$(document).on('click', '#test-media-library', function(e) {
					e.preventDefault();

					if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
						alert('❌ Media Library non disponibile!\n\nProva a:\n1. Ricaricare la pagina\n2. Salvare il widget e riaprirlo\n3. Controllare la console per errori');
						$('#media-status-text').html('❌ Non disponibile').css('color', 'red');
						return;
					}

					// Test opening media library
					var test_uploader = wp.media({
						title: '🧪 Test Media Library',
						button: {
							text: 'Chiudi Test'
						},
						multiple: false,
						library: {
							type: 'image'
						}
					});

					test_uploader.on('open', function() {
						alert('✅ Media Library funziona correttamente!');
					});

					test_uploader.open();
				});

				// Initialize uploader when media is ready
				waitForMedia(function() {
					console.log('✅ Media Library pronta, inizializzo uploader');
					initializeSecureUploader();
				});

				function initializeSecureUploader() {
					// Secure media uploader functionality
					$(document).off('click', '.upload-secure-image').on('click', '.upload-secure-image', function(e) {
						e.preventDefault();

						console.log('🔒 Pulsante carica sicuro cliccato');

						var button = $(this);
						var targetIdField = $('#' + button.data('target-id'));
						var targetUrlField = $('#' + button.data('target-url'));
						var targetDisplay = $('#' + button.data('target-display'));
						var previewContainer = $('#' + button.data('preview'));

						console.log('📋 Dati target:', {
							id: button.data('target-id'),
							url: button.data('target-url'),
							display: button.data('target-display'),
							preview: button.data('preview')
						});

						// Security check
						if (!targetIdField.length || !targetUrlField.length || !targetDisplay.length) {
							alert('<?php _e('Errore: campi target non trovati', 'marcello-scavo-tattoo'); ?>');
							console.error('❌ Campi non trovati:', {
								idField: targetIdField.length,
								urlField: targetUrlField.length,
								displayField: targetDisplay.length
							});
							return;
						}

						// Check if wp.media is available
						if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
							alert('<?php _e('Media Library non disponibile. Ricarica la pagina.', 'marcello-scavo-tattoo'); ?>');
							console.error('❌ wp.media non disponibile');
							return;
						}

						console.log('🚀 Aprendo Media Library...');

						var media_uploader = wp.media({
							title: '<?php _e('🔒 Seleziona Immagine Sicura', 'marcello-scavo-tattoo'); ?>',
							button: {
								text: '<?php _e('Usa questa immagine', 'marcello-scavo-tattoo'); ?>'
							},
							multiple: false,
							library: {
								type: 'image'
							}
						});

						media_uploader.on('select', function() {
							var attachment = media_uploader.state().get('selection').first().toJSON();
							console.log('📸 Immagine selezionata:', attachment);

							// Security validation
							if (!attachment.id || !attachment.url) {
								alert('<?php _e('Errore: dati immagine non validi', 'marcello-scavo-tattoo'); ?>');
								return;
							}

							// File type validation
							var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
							if (allowedTypes.indexOf(attachment.mime) === -1) {
								alert('<?php _e('Formato non supportato. Usa JPG, PNG o WebP', 'marcello-scavo-tattoo'); ?>');
								return;
							}

							// Size validation (2MB max)
							if (attachment.filesizeInBytes && attachment.filesizeInBytes > 2097152) {
								alert('<?php _e('File troppo grande. Massimo 2MB', 'marcello-scavo-tattoo'); ?>');
								return;
							}

							// Get appropriate image size
							var imageUrl = attachment.sizes && attachment.sizes.medium ?
								attachment.sizes.medium.url : attachment.url;

							console.log('✅ Immagine validata, aggiornando campi...');

							// Update secure fields
							targetIdField.val(attachment.id);
							targetUrlField.val(imageUrl);

							// Update display field
							targetDisplay.val('<?php _e('Immagine ID:', 'marcello-scavo-tattoo'); ?> ' + attachment.id + ' (' + attachment.filename + ')');

							// Update preview
							updateSecureImagePreview(previewContainer, {
								id: attachment.id,
								url: imageUrl,
								filename: attachment.filename
							}, button);

							console.log('🎉 Immagine caricata con successo!');
						});

						media_uploader.open();
					});
				}

				// Alternative classic media library approach
				$(document).on('click', '.upload-classic-image', function(e) {
					e.preventDefault();

					console.log('📁 Pulsante Media Library classico cliccato');

					var button = $(this);
					var targetIdField = $('#' + button.data('target-id'));
					var targetUrlField = $('#' + button.data('target-url'));
					var targetDisplay = $('#' + button.data('target-display'));
					var previewContainer = $('#' + button.data('preview'));

					// Store current button reference for callback
					window.currentImageButton = {
						idField: targetIdField,
						urlField: targetUrlField,
						displayField: targetDisplay,
						previewContainer: previewContainer
					};

					// Try multiple approaches to open media library
					if (typeof wp !== 'undefined' && typeof wp.media !== 'undefined') {
						// Modern approach
						var frame = wp.media({
							title: '📁 Seleziona Immagine dalla Media Library',
							button: {
								text: 'Seleziona Immagine'
							},
							multiple: false
						});

						frame.on('select', function() {
							var attachment = frame.state().get('selection').first().toJSON();
							processSelectedImage(attachment);
						});

						frame.open();
					} else {
						// Fallback to classic uploader
						alert('⚠️ Media Library moderna non disponibile.\n\nUsa il gestore media di WordPress dal menu Media > Libreria per caricare le immagini, poi inserisci l\'ID dell\'immagine manualmente.');

						var imageId = prompt('Inserisci l\'ID dell\'immagine dalla Media Library:');
						if (imageId && !isNaN(imageId)) {
							// Validate and process image ID
							$.ajax({
								url: ajaxurl,
								type: 'POST',
								data: {
									action: 'get_attachment_data',
									attachment_id: imageId,
									nonce: '<?php echo wp_create_nonce('get_attachment_data'); ?>'
								},
								success: function(response) {
									if (response.success) {
										processSelectedImage(response.data);
									} else {
										alert('❌ Immagine non trovata o non valida');
									}
								}
							});
						}
					}
				});

				// Process selected image (common function)
				function processSelectedImage(attachment) {
					console.log('🖼️ Elaborando immagine selezionata:', attachment);

					if (!window.currentImageButton) {
						alert('Errore: riferimento pulsante non trovato');
						return;
					}

					var btn = window.currentImageButton;

					// Security validation
					if (!attachment.id || !attachment.url) {
						alert('Errore: dati immagine non validi');
						return;
					}

					// File type validation
					var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
					if (attachment.mime && allowedTypes.indexOf(attachment.mime) === -1) {
						alert('Formato non supportato. Usa JPG, PNG o WebP');
						return;
					}

					// Get appropriate image size
					var imageUrl = attachment.sizes && attachment.sizes.medium ?
						attachment.sizes.medium.url : attachment.url;

					// Update fields
					btn.idField.val(attachment.id);
					btn.urlField.val(imageUrl);
					btn.displayField.val('Immagine ID: ' + attachment.id + ' (' + (attachment.filename || 'immagine.jpg') + ')');

					// Update preview
					var previewHtml = '<div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">' +
						'<img src="' + imageUrl + '" style="max-width: 80px; height: auto; border-radius: 4px; border: 2px solid #28a745;" alt="Preview">' +
						'<div>' +
						'<p style="margin: 0; font-size: 11px; color: #666;">' +
						'<strong>✅ Caricato:</strong> ID ' + attachment.id + '<br>' +
						'<strong>File:</strong> ' + (attachment.filename || 'immagine.jpg') +
						'</p>' +
						'<button type="button" onclick="$(\'#' + btn.idField.attr('id') + '\').val(\'\'); $(\'#' + btn.urlField.attr('id') + '\').val(\'\'); $(\'#' + btn.displayField.attr('id') + '\').val(\'Nessuna immagine selezionata\'); $(\'#' + btn.previewContainer.attr('id') + '\').html(\'\');" style="margin-top: 5px; color: #dc3545;">' +
						'🗑️ Rimuovi' +
						'</button>' +
						'</div>' +
						'</div>';

					btn.previewContainer.html(previewHtml);

					console.log('✅ Immagine processata con successo!');
				}

				// Remove secure image functionality
				$(document).on('click', '.remove-secure-image', function(e) {
					e.preventDefault();

					if (!confirm('<?php _e('Sei sicuro di voler rimuovere questa immagine?', 'marcello-scavo-tattoo'); ?>')) {
						return;
					}

					var button = $(this);
					var targetIdField = $('#' + button.data('target-id'));
					var targetUrlField = $('#' + button.data('target-url'));
					var targetDisplay = $('#' + button.data('target-display'));
					var previewContainer = $('#' + button.data('preview'));

					// Clear all fields
					targetIdField.val('');
					targetUrlField.val('');
					targetDisplay.val('<?php _e('Nessuna immagine selezionata', 'marcello-scavo-tattoo'); ?>');
					previewContainer.html('');
				});

				// Legacy uploader compatibility (for old URLs)
				$('.upload-placeholder-image').on('click', function(e) {
					e.preventDefault();
					alert('<?php _e('Usa il nuovo sistema sicuro "🔒 Carica Sicuro" per maggiore sicurezza.', 'marcello-scavo-tattoo'); ?>');
				});
			});

			function updateSecureImagePreview(container, attachment, uploadButton) {
				var previewHtml = '<div style="display: flex; align-items: center; gap: 10px;">' +
					'<img src="' + attachment.url + '" style="max-width: 80px; height: auto; border-radius: 4px; border: 2px solid #ddd;" alt="Preview">' +
					'<div>' +
					'<p style="margin: 0; font-size: 11px; color: #666;">' +
					'<strong><?php _e('Sicuro:', 'marcello-scavo-tattoo'); ?></strong> ID ' + attachment.id + '<br>' +
					'<strong><?php _e('File:', 'marcello-scavo-tattoo'); ?></strong> ' + attachment.filename +
					'</p>' +
					'<button type="button" class="button remove-secure-image" ' +
					'data-target-id="' + uploadButton.data('target-id') + '" ' +
					'data-target-url="' + uploadButton.data('target-url') + '" ' +
					'data-target-display="' + uploadButton.data('target-display') + '" ' +
					'data-preview="' + uploadButton.data('preview') + '" ' +
					'style="margin-top: 5px; color: #dc3545;">' +
					'<?php _e('🗑️ Rimuovi', 'marcello-scavo-tattoo'); ?>' +
					'</button>' +
					'</div>' +
					'</div>';

				container.html(previewHtml);
			}
		</script>

		<p><strong><?php _e('Nota:', 'marcello-scavo-tattoo'); ?></strong> <?php _e('La modalità semplice è utile se hai difficoltà con la configurazione dell\'API di Instagram.', 'marcello-scavo-tattoo'); ?></p>
	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['access_token'] = (!empty($new_instance['access_token'])) ? sanitize_text_field($new_instance['access_token']) : '';
		$instance['num_photos'] = (!empty($new_instance['num_photos'])) ? absint($new_instance['num_photos']) : 6;
		$instance['show_profile'] = !empty($new_instance['show_profile']) ? 1 : 0;
		$instance['profile_username'] = (!empty($new_instance['profile_username'])) ? sanitize_text_field($new_instance['profile_username']) : 'marcelloscavo_art';
		$instance['profile_link'] = (!empty($new_instance['profile_link'])) ? esc_url_raw($new_instance['profile_link']) : 'https://instagram.com/marcelloscavo_art';
		$instance['use_simple_mode'] = !empty($new_instance['use_simple_mode']) ? 1 : 0;

		// Save placeholder images with security validation
		for ($i = 1; $i <= 6; $i++) {
			// Initialize variables
			$image_id = 0;
			$image_url = '';
			$caption = '';

			// Get and validate image ID if provided
			if (!empty($new_instance["placeholder_image_id_$i"])) {
				$provided_id = absint($new_instance["placeholder_image_id_$i"]);

				// Verify this is a valid attachment and user has permission
				if ($provided_id > 0 && wp_attachment_is_image($provided_id)) {
					// Check if user can edit this attachment
					if (current_user_can('edit_post', $provided_id)) {
						$image_id = $provided_id;
						// Get secure URL from attachment ID
						$image_url = wp_get_attachment_image_url($image_id, 'medium');

						// Fallback to full size if medium doesn't exist
						if (!$image_url) {
							$image_url = wp_get_attachment_image_url($image_id, 'full');
						}

						// Additional security: verify URL belongs to this site
						if (!$image_url || !$this->is_local_attachment($image_url)) {
							$image_id = 0;
							$image_url = '';
						}
					}
				}
			}

			// Fallback: if no valid ID but URL is provided, validate URL
			if (!$image_id && !empty($new_instance["placeholder_image_$i"])) {
				$provided_url = esc_url_raw($new_instance["placeholder_image_$i"]);

				// Only allow URLs from this site for security
				if ($this->is_local_attachment($provided_url)) {
					$image_url = $provided_url;
					// Try to get attachment ID from URL
					$image_id = attachment_url_to_postid($provided_url);
				}
			}

			// Validate and sanitize caption
			if (!empty($new_instance["placeholder_caption_$i"])) {
				$caption = sanitize_text_field($new_instance["placeholder_caption_$i"]);
				// Limit length for security
				$caption = substr($caption, 0, 200);
			}

			// Save validated data
			$instance["placeholder_image_id_$i"] = $image_id;
			$instance["placeholder_image_$i"] = $image_url;
			$instance["placeholder_caption_$i"] = $caption;
		}

		return $instance;
	}

	/**
	 * Security helper: Check if URL belongs to this WordPress site
	 * Enhanced with error handling for production
	 */
	private function is_local_attachment($url)
	{
		try {
			if (empty($url) || !is_string($url)) {
				return false;
			}

			// Basic URL validation
			if (!filter_var($url, FILTER_VALIDATE_URL)) {
				return false;
			}

			$site_url = home_url();
			$uploads_dir = wp_upload_dir();

			// Fallback if wp_upload_dir fails
			if (is_wp_error($uploads_dir) || !isset($uploads_dir['baseurl'])) {
				// Only allow site URL based URLs as fallback
				return strpos($url, $site_url) === 0;
			}

			// Check if URL starts with site URL or uploads URL
			return (
				strpos($url, $site_url) === 0 ||
				strpos($url, $uploads_dir['baseurl']) === 0
			);
		} catch (Exception $e) {
			// Log error and return false for safety
			error_log('Instagram widget URL validation error: ' . $e->getMessage());
			return false;
		}
	}
}

/**
 * Custom Widget: Google Reviews
 */
class Marcello_Scavo_Google_Reviews_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_google_reviews_widget',
			__('Recensioni Google Studio', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Mostra le recensioni Google dello studio con animazioni innovative.', 'marcello-scavo-tattoo'),
				'classname' => 'widget_marcello_google_reviews',
			)
		);
	}

	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);
		$show_google_stats = isset($instance['show_google_stats']) ? (bool) $instance['show_google_stats'] : true;
		$animation_type = isset($instance['animation_type']) ? $instance['animation_type'] : 'slide';
		$auto_rotate = isset($instance['auto_rotate']) ? (bool) $instance['auto_rotate'] : true;
		$rotation_speed = isset($instance['rotation_speed']) ? absint($instance['rotation_speed']) : 5;

		echo $args['before_widget'];

		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// Recensioni reali dello studio (basate sull'immagine fornita)
		$reviews = array(
			array(
				'name' => 'Federica Mazzu',
				'rating' => 5,
				'text' => 'Esperienza fantastica! Marcello è un vero artista, ogni dettaglio è curato con passione. Il mio tatuaggio è diventato un\'opera d\'arte che porto con orgoglio sulla pelle.',
				'date' => '2 settimane fa',
				'initial' => 'F',
				'color' => '#e91e63'
			),
			array(
				'name' => 'Alessandro Romano',
				'rating' => 5,
				'text' => 'Professionalità e creatività al top! Studio impeccabile, strumenti sterili e un\'atmosfera che ti mette subito a tuo agio. Consigliatissimo!',
				'date' => '1 mese fa',
				'initial' => 'A',
				'color' => '#2196f3'
			),
			array(
				'name' => 'Sofia Martini',
				'rating' => 5,
				'text' => 'Il mio primo tatuaggio non poteva essere in mani migliori. Marcello ha saputo interpretare perfettamente la mia idea e realizzarla oltre le mie aspettative.',
				'date' => '3 settimane fa',
				'initial' => 'S',
				'color' => '#ff9800'
			),
			array(
				'name' => 'Marco Bianchi',
				'rating' => 5,
				'text' => 'Arte pura! Il tatuaggio che ho fatto da Marcello è un capolavoro. Ogni volta che lo guardo mi emoziono. Grazie per aver dato vita alla mia idea!',
				'date' => '1 settimana fa',
				'initial' => 'M',
				'color' => '#4caf50'
			),
			array(
				'name' => 'Elena Rossi',
				'rating' => 5,
				'text' => 'Esperienza incredibile dall\'inizio alla fine. Studio pulitissimo, Marcello super disponibile e il risultato... semplicemente perfetto!',
				'date' => '2 mesi fa',
				'initial' => 'E',
				'color' => '#9c27b0'
			)
		);

		echo '<div class="google-reviews-container">';

		// Google Business Info
		if ($show_google_stats) {
			echo '<div class="google-business-info">';
			echo '<div class="google-logo">';
			echo '<img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTIyLjU2IDEyLjI1YzAtLjc4LS4wNy0xLjUzLS4yLTIuMjVIMTJ2NC4yNmg1LjkyYy0uMjYgMS4zNy0xLjA0IDIuNTMtMi4yMSAzLjMxdjIuNzdoMy41N2MyLjA4LTEuOTIgMy4yOC00Ljc0IDMuMjgtOC4wOXoiIGZpbGw9IiM0Mjg1RjQiLz4KPHBhdGggZD0iTTEyIDIzYzIuOTcgMCA1LjQ2LS45OCA3LjI4LTIuNjZsLTMuNTctMi43N2MtLjk4LjY5LTIuMjMgMS4wNi0zLjcxIDEuMDYtMi44NiAwLTUuMjktMS45My02LjE2LTQuNTNIMi4xOHYyLjg0QzMuOTkgMjAuNTMgNy43IDIzIDEyIDIzeiIgZmlsbD0iIzM0QTg1MyIvPgo8cGF0aCBkPSJNNS44NCAxNC4wOWMtLjIyLS42OS0uMzUtMS40My0uMzUtMi4wOXMuMTMtMS40LjM1LTIuMDlWNy4wN0gyLjE4QzEuNDMgOC41NSAxIDEwLjIyIDEgMTJzLjQzIDMuNDUgMS4xOCA0LjkzbDIuODUtMi4yMXYtLjYzeiIgZmlsbD0iI0ZCQkMwNCIvPgo8cGF0aCBkPSJNMTIgNS4zOGMyLjYyIDAgNC45NCAxLjEyIDYuNzggMi44TDE1LjkgNi4yOUMxNC40NyA0Ljk3IDEyLjQ3IDQgMTIgNGMtNC4zIDAtOC4wMSAyLjQ3LTkuODIgNi4wN2wyLjgzIDIuMjJjLjg3LTIuNiAzLjMtNC41MyA2LjE2LTQuNTN6IiBmaWxsPSIjRUE0MzM1Ii8+Cjwvc3ZnPgo=" alt="Google" />';
			echo '</div>';
			echo '<div class="google-business-details">';
			echo '<h4>Marcello Scavo Tattoos</h4>';
			echo '<div class="google-rating">';
			echo '<span class="rating-score">5,0</span>';
			echo '<div class="stars-google">';
			for ($i = 0; $i < 5; $i++) {
				echo '<span class="star">★</span>';
			}
			echo '</div>';
			echo '<span class="review-count">Basato su ' . count($reviews) . '+ recensioni</span>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}

		// Reviews Carousel
		echo '<div class="reviews-carousel" data-animation="' . esc_attr($animation_type) . '" data-auto-rotate="' . ($auto_rotate ? 'true' : 'false') . '" data-speed="' . esc_attr($rotation_speed) . '">';

		foreach ($reviews as $index => $review) {
			$active_class = $index === 0 ? ' active' : '';
			echo '<div class="review-card' . $active_class . '" data-index="' . $index . '">';

			echo '<div class="review-header">';
			echo '<div class="reviewer-avatar" style="background-color: ' . esc_attr($review['color']) . '">';
			echo '<span>' . esc_html($review['initial']) . '</span>';
			echo '</div>';
			echo '<div class="reviewer-info">';
			echo '<h5 class="reviewer-name">' . esc_html($review['name']) . '</h5>';
			echo '<div class="review-rating">';
			for ($i = 0; $i < $review['rating']; $i++) {
				echo '<span class="star">★</span>';
			}
			echo '</div>';
			echo '<span class="review-date">' . esc_html($review['date']) . '</span>';
			echo '</div>';
			echo '</div>';

			echo '<div class="review-content">';
			echo '<p>"' . esc_html($review['text']) . '"</p>';
			echo '</div>';

			echo '</div>';
		}

		echo '</div>';

		// Navigation dots
		echo '<div class="reviews-navigation">';
		for ($i = 0; $i < count($reviews); $i++) {
			$active_class = $i === 0 ? ' active' : '';
			echo '<button class="nav-dot' . $active_class . '" data-slide="' . $i . '"></button>';
		}
		echo '</div>';

		// Call to action
		echo '<div class="reviews-cta">';
		echo '<a href="https://g.page/r/CRvQ8z8z8z8EBAM/review" target="_blank" rel="noopener" class="review-us-btn">';
		echo '<i class="fab fa-google"></i> Lascia una recensione su Google';
		echo '</a>';
		echo '</div>';

		echo '</div>';

		// JavaScript for carousel functionality
	?>
		<script>
			jQuery(document).ready(function($) {
				var currentSlide = 0;
				var totalSlides = $('.review-card').length;
				var autoRotate = $('.reviews-carousel').data('auto-rotate');
				var speed = $('.reviews-carousel').data('speed') * 1000;
				var intervalId;

				function showSlide(index) {
					$('.review-card').removeClass('active');
					$('.nav-dot').removeClass('active');
					$('.review-card[data-index="' + index + '"]').addClass('active');
					$('.nav-dot[data-slide="' + index + '"]').addClass('active');
					currentSlide = index;
				}

				function nextSlide() {
					var next = (currentSlide + 1) % totalSlides;
					showSlide(next);
				}

				// Navigation dots click
				$('.nav-dot').on('click', function() {
					var slide = $(this).data('slide');
					showSlide(slide);

					if (autoRotate) {
						clearInterval(intervalId);
						startAutoRotation();
					}
				});

				// Auto rotation
				function startAutoRotation() {
					if (autoRotate && totalSlides > 1) {
						intervalId = setInterval(nextSlide, speed);
					}
				}

				// Pause on hover
				$('.reviews-carousel').on('mouseenter', function() {
					if (autoRotate) clearInterval(intervalId);
				}).on('mouseleave', function() {
					if (autoRotate) startAutoRotation();
				});

				// Start auto rotation
				startAutoRotation();
			});
		</script>
	<?php

		echo $args['after_widget'];
	}

	public function form($instance)
	{
		$title = isset($instance['title']) ? $instance['title'] : __('Cosa dicono i nostri clienti', 'marcello-scavo-tattoo');
		$show_google_stats = isset($instance['show_google_stats']) ? (bool) $instance['show_google_stats'] : true;
		$animation_type = isset($instance['animation_type']) ? $instance['animation_type'] : 'slide';
		$auto_rotate = isset($instance['auto_rotate']) ? (bool) $instance['auto_rotate'] : true;
		$rotation_speed = isset($instance['rotation_speed']) ? absint($instance['rotation_speed']) : 5;
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($show_google_stats); ?> id="<?php echo $this->get_field_id('show_google_stats'); ?>" name="<?php echo $this->get_field_name('show_google_stats'); ?>" />
			<label for="<?php echo $this->get_field_id('show_google_stats'); ?>"><?php _e('Mostra statistiche Google', 'marcello-scavo-tattoo'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('animation_type'); ?>"><?php _e('Tipo animazione:', 'marcello-scavo-tattoo'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('animation_type'); ?>" name="<?php echo $this->get_field_name('animation_type'); ?>">
				<option value="slide" <?php selected($animation_type, 'slide'); ?>><?php _e('Slide', 'marcello-scavo-tattoo'); ?></option>
				<option value="fade" <?php selected($animation_type, 'fade'); ?>><?php _e('Fade', 'marcello-scavo-tattoo'); ?></option>
				<option value="scale" <?php selected($animation_type, 'scale'); ?>><?php _e('Scale', 'marcello-scavo-tattoo'); ?></option>
			</select>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($auto_rotate); ?> id="<?php echo $this->get_field_id('auto_rotate'); ?>" name="<?php echo $this->get_field_name('auto_rotate'); ?>" />
			<label for="<?php echo $this->get_field_id('auto_rotate'); ?>"><?php _e('Rotazione automatica', 'marcello-scavo-tattoo'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('rotation_speed'); ?>"><?php _e('Velocità rotazione (secondi):', 'marcello-scavo-tattoo'); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id('rotation_speed'); ?>" name="<?php echo $this->get_field_name('rotation_speed'); ?>" type="number" step="1" min="2" max="10" value="<?php echo $rotation_speed; ?>" size="3">
		</p>
	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['show_google_stats'] = !empty($new_instance['show_google_stats']) ? 1 : 0;
		$instance['animation_type'] = (!empty($new_instance['animation_type'])) ? sanitize_text_field($new_instance['animation_type']) : 'slide';
		$instance['auto_rotate'] = !empty($new_instance['auto_rotate']) ? 1 : 0;
		$instance['rotation_speed'] = (!empty($new_instance['rotation_speed'])) ? absint($new_instance['rotation_speed']) : 5;
		return $instance;
	}
}

/**
 * Custom Widget: Bookly Integration
 */
class Marcello_Scavo_Bookly_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_bookly_widget',
			__('Bookly Prenotazioni', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Widget per integrare Bookly o altri sistemi di prenotazione.', 'marcello-scavo-tattoo'),
				'classname' => 'widget_marcello_bookly',
			)
		);
	}

	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);
		$subtitle = isset($instance['subtitle']) ? $instance['subtitle'] : '';
		$bookly_shortcode = isset($instance['bookly_shortcode']) ? $instance['bookly_shortcode'] : '';
		$show_features = isset($instance['show_features']) ? (bool) $instance['show_features'] : true;
		$cta_text = isset($instance['cta_text']) ? $instance['cta_text'] : '';

		echo $args['before_widget'];

		echo '<div class="bookly-integration-container">';

		// Header Section
		echo '<div class="booking-header">';
		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if (!empty($subtitle)) {
			echo '<p class="booking-subtitle">' . esc_html($subtitle) . '</p>';
		}
		echo '</div>';

		// Features List (if enabled)
		if ($show_features) {
			echo '<div class="booking-features">';
			echo '<div class="features-grid">';

			echo '<div class="feature-item">';
			echo '<div class="feature-icon"><i class="fas fa-calendar-check"></i></div>';
			echo '<h4>' . __('Prenotazione Online', 'marcello-scavo-tattoo') . '</h4>';
			echo '<p>' . __('Sistema sicuro e veloce', 'marcello-scavo-tattoo') . '</p>';
			echo '</div>';

			echo '<div class="feature-item">';
			echo '<div class="feature-icon"><i class="fas fa-clock"></i></div>';
			echo '<h4>' . __('Orari Flessibili', 'marcello-scavo-tattoo') . '</h4>';
			echo '<p>' . __('Milano e Messina', 'marcello-scavo-tattoo') . '</p>';
			echo '</div>';

			echo '<div class="feature-item">';
			echo '<div class="feature-icon"><i class="fas fa-paint-brush"></i></div>';
			echo '<h4>' . __('Consulenza Gratuita', 'marcello-scavo-tattoo') . '</h4>';
			echo '<p>' . __('Design personalizzato', 'marcello-scavo-tattoo') . '</p>';
			echo '</div>';

			echo '</div>';
			echo '</div>';
		}

		// Bookly Shortcode Section
		if (!empty($bookly_shortcode)) {
			echo '<div class="bookly-form-container">';
			echo do_shortcode($bookly_shortcode);
			echo '</div>';
		} else {
			// Fallback if no Bookly shortcode is provided
			echo '<div class="booking-fallback">';
			echo '<div class="booking-fallback-content">';
			echo '<h3>' . __('Sistema di Prenotazione', 'marcello-scavo-tattoo') . '</h3>';
			echo '<p>' . __('Per configurare il sistema di prenotazione, inserisci lo shortcode di Bookly nel widget.', 'marcello-scavo-tattoo') . '</p>';
			echo '<div class="booking-info">';
			echo '<p><strong>' . __('Esempio shortcode Bookly:', 'marcello-scavo-tattoo') . '</strong></p>';
			echo '<code>[bookly-form]</code>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}

		// Additional CTA (if provided)
		if (!empty($cta_text)) {
			echo '<div class="booking-additional-cta">';
			echo '<p class="cta-text">' . wp_kses_post($cta_text) . '</p>';
			echo '</div>';
		}

		// Contact Alternative
		echo '<div class="booking-alternative">';
		echo '<p class="alternative-text">' . __('Preferisci chiamare?', 'marcello-scavo-tattoo') . '</p>';
		echo '<div class="contact-buttons">';

		$phone = get_theme_mod('contact_phone', '+39 347 627 0570');
		$email = get_theme_mod('contact_email', 'info@marcelloscavo.com');

		echo '<a href="tel:' . esc_attr(str_replace(' ', '', $phone)) . '" class="contact-btn phone-btn">';
		echo '<i class="fas fa-phone"></i> ' . esc_html($phone);
		echo '</a>';

		echo '<a href="mailto:' . esc_attr($email) . '" class="contact-btn email-btn">';
		echo '<i class="fas fa-envelope"></i> Email';
		echo '</a>';

		echo '</div>';
		echo '</div>';

		echo '</div>';

		echo $args['after_widget'];
	}

	public function form($instance)
	{
		$title = isset($instance['title']) ? $instance['title'] : __('Prenota il tuo tatuaggio oggi!', 'marcello-scavo-tattoo');
		$subtitle = isset($instance['subtitle']) ? $instance['subtitle'] : __('Scopri l\'arte del tatuaggio e prenota il tuo appuntamento per un\'esperienza unica.', 'marcello-scavo-tattoo');
		$bookly_shortcode = isset($instance['bookly_shortcode']) ? $instance['bookly_shortcode'] : '[bookly-form]';
		$show_features = isset($instance['show_features']) ? (bool) $instance['show_features'] : true;
		$cta_text = isset($instance['cta_text']) ? $instance['cta_text'] : '';
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('Sottotitolo:', 'marcello-scavo-tattoo'); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" rows="3"><?php echo esc_textarea($subtitle); ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('bookly_shortcode'); ?>"><?php _e('Shortcode Bookly:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('bookly_shortcode'); ?>" name="<?php echo $this->get_field_name('bookly_shortcode'); ?>" type="text" value="<?php echo esc_attr($bookly_shortcode); ?>">
			<small><?php _e('Es: [bookly-form] o [bookly-form category_id="1"]', 'marcello-scavo-tattoo'); ?></small>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($show_features); ?> id="<?php echo $this->get_field_id('show_features'); ?>" name="<?php echo $this->get_field_name('show_features'); ?>" />
			<label for="<?php echo $this->get_field_id('show_features'); ?>"><?php _e('Mostra caratteristiche del servizio', 'marcello-scavo-tattoo'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('cta_text'); ?>"><?php _e('Testo aggiuntivo (opzionale):', 'marcello-scavo-tattoo'); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('cta_text'); ?>" name="<?php echo $this->get_field_name('cta_text'); ?>" rows="2"><?php echo esc_textarea($cta_text); ?></textarea>
			<small><?php _e('Testo che apparirà sotto il form di prenotazione.', 'marcello-scavo-tattoo'); ?></small>
		</p>

		<hr>
		<p><strong><?php _e('Istruzioni:', 'marcello-scavo-tattoo'); ?></strong></p>
		<ol>
			<li><?php _e('Installa e configura il plugin Bookly', 'marcello-scavo-tattoo'); ?></li>
			<li><?php _e('Crea un servizio "Tatuaggio" in Bookly', 'marcello-scavo-tattoo'); ?></li>
			<li><?php _e('Copia lo shortcode generato da Bookly', 'marcello-scavo-tattoo'); ?></li>
			<li><?php _e('Incollalo nel campo "Shortcode Bookly"', 'marcello-scavo-tattoo'); ?></li>
		</ol>
	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['subtitle'] = (!empty($new_instance['subtitle'])) ? sanitize_textarea_field($new_instance['subtitle']) : '';
		$instance['bookly_shortcode'] = (!empty($new_instance['bookly_shortcode'])) ? sanitize_text_field($new_instance['bookly_shortcode']) : '[bookly-form]';
		$instance['show_features'] = !empty($new_instance['show_features']) ? 1 : 0;
		$instance['cta_text'] = (!empty($new_instance['cta_text'])) ? wp_kses_post($new_instance['cta_text']) : '';
		return $instance;
	}
}

/**
 * Custom Widget: Artistic Gallery Showcase
 */
class Marcello_Scavo_Gallery_Showcase_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_gallery_showcase_widget',
			__('Galleria Creazioni Artistiche', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Mostra la galleria dei lavori artistici con slider categorizzato e filtri.', 'marcello-scavo-tattoo'),
				'classname' => 'widget_marcello_gallery_showcase',
			)
		);
	}

	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);
		$subtitle = isset($instance['subtitle']) ? $instance['subtitle'] : '';
		$display_mode = isset($instance['display_mode']) ? $instance['display_mode'] : 'categorized_slider';
		$items_per_category = isset($instance['items_per_category']) ? absint($instance['items_per_category']) : 6;
		$show_category_filter = isset($instance['show_category_filter']) ? (bool) $instance['show_category_filter'] : true;
		$auto_rotate = isset($instance['auto_rotate']) ? (bool) $instance['auto_rotate'] : true;
		$rotation_speed = isset($instance['rotation_speed']) ? absint($instance['rotation_speed']) : 4;

		echo $args['before_widget'];

		echo '<div class="gallery-showcase-container">';

		// Header Section
		echo '<div class="gallery-header">';
		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if (!empty($subtitle)) {
			echo '<p class="gallery-subtitle">' . esc_html($subtitle) . '</p>';
		}
		echo '</div>';

		// Get gallery categories
		$categories = get_terms(array(
			'taxonomy' => 'gallery_category',
			'hide_empty' => true,
		));

		if (!empty($categories) && !is_wp_error($categories)) {

			// Category Filter (if enabled)
			if ($show_category_filter && $display_mode === 'categorized_slider') {
				echo '<div class="gallery-filter-tabs">';
				echo '<button class="filter-tab active" data-category="all">' . __('Tutte le Opere', 'marcello-scavo-tattoo') . '</button>';
				foreach ($categories as $category) {
					echo '<button class="filter-tab" data-category="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</button>';
				}
				echo '</div>';
			}

			// Gallery Content
			if ($display_mode === 'categorized_slider') {
				echo '<div class="gallery-categorized-slider" data-auto-rotate="' . ($auto_rotate ? 'true' : 'false') . '" data-speed="' . esc_attr($rotation_speed) . '">';

				// All works section
				echo '<div class="category-section active" data-category="all">';
				$this->render_gallery_grid('all', $items_per_category);
				echo '</div>';

				// Category sections
				foreach ($categories as $category) {
					echo '<div class="category-section" data-category="' . esc_attr($category->slug) . '">';
					$this->render_gallery_grid($category->slug, $items_per_category);
					echo '</div>';
				}

				echo '</div>';
			} elseif ($display_mode === 'masonry_grid') {
				echo '<div class="gallery-masonry-grid">';
				$this->render_gallery_grid('all', $items_per_category * count($categories));
				echo '</div>';
			} elseif ($display_mode === 'featured_carousel') {
				echo '<div class="gallery-featured-carousel">';
				$this->render_featured_carousel($items_per_category);
				echo '</div>';
			}

			// Call to Action
			echo '<div class="gallery-cta">';
			echo '<button class="gallery-view-all-btn" data-toggle="gallery-modal">';
			echo '<i class="fas fa-images"></i> ' . __('Visualizza Tutta la Galleria', 'marcello-scavo-tattoo');
			echo '</button>';
			echo '</div>';
		} else {
			// No gallery items
			echo '<div class="gallery-empty">';
			echo '<div class="empty-state">';
			echo '<i class="fas fa-palette"></i>';
			echo '<h3>' . __('Galleria in Costruzione', 'marcello-scavo-tattoo') . '</h3>';
			echo '<p>' . __('Presto vedrai qui le mie creazioni artistiche più belle!', 'marcello-scavo-tattoo') . '</p>';
			echo '</div>';
			echo '</div>';
		}

		echo '</div>';

		// JavaScript for interactivity
	?>
		<script>
			jQuery(document).ready(function($) {
				console.log('Gallery widget script initialized');

				// Global event delegation for modal images (always active)
				$('body').off('click.globalGalleryModal').on('click.globalGalleryModal', '.gallery-modal-item img, .gallery-modal-item', function(e) {
					if ($(this).closest('.gallery-modal-overlay').length > 0) {
						e.preventDefault();
						e.stopPropagation();

						console.log('GLOBAL: Click detected on modal image');

						var $img = $(this).is('img') ? $(this) : $(this).find('img');
						var fullSrc = $img.data('full') || $img.attr('src');
						var alt = $img.attr('alt') || 'Immagine Galleria';

						console.log('GLOBAL: Opening lightbox with:', fullSrc, alt);

						if (fullSrc) {
							openLightbox(fullSrc, alt);
						}
					}
				});

				// Category filter functionality
				$('.filter-tab').on('click', function() {
					var category = $(this).data('category');

					// Update active tab
					$('.filter-tab').removeClass('active');
					$(this).addClass('active');

					// Show/hide category sections
					$('.category-section').removeClass('active');
					$('.category-section[data-category="' + category + '"]').addClass('active');
				});

				// Auto-rotation for featured carousel
				var autoRotate = $('.gallery-categorized-slider').data('auto-rotate');
				var speed = $('.gallery-categorized-slider').data('speed') * 1000;

				if (autoRotate && $('.gallery-featured-carousel').length) {
					setInterval(function() {
						var current = $('.gallery-featured-carousel .featured-item.active');
						var next = current.next('.featured-item');

						if (next.length === 0) {
							next = $('.gallery-featured-carousel .featured-item').first();
						}

						current.removeClass('active');
						next.addClass('active');
					}, speed);
				}

				// Gallery modal functionality
				$('.gallery-view-all-btn[data-toggle="gallery-modal"]').on('click', function(e) {
					e.preventDefault();
					openGalleryModal();
				});

				function openGalleryModal() {
					console.log('Opening gallery modal...');

					var modalHtml = '<div class="gallery-modal-overlay" id="gallery-full-modal">' +
						'<div class="gallery-modal-content">' +
						'<div class="gallery-modal-header">' +
						'<h3>Galleria Completa</h3>' +
						'<button class="gallery-modal-close">&times;</button>' +
						'</div>' +
						'<div class="gallery-modal-body">' +
						'<div class="gallery-modal-grid">' +
						loadAllGalleryItems() +
						'</div>' +
						'</div>' +
						'</div>' +
						'</div>';

					console.log('Modal HTML created');
					$('body').append(modalHtml);
					$('#gallery-full-modal').addClass('active');
					$('body').addClass('gallery-modal-open');
					console.log('Modal added to DOM and activated');

					// Debug: log all images in modal
					setTimeout(function() {
						$('.gallery-modal-item img').each(function(i, img) {
							console.log('Image', i, ':', $(img).attr('alt'), 'data-full:', $(img).data('full'));
						});
					}, 200);

					initModalHandlers();
				}

				function loadAllGalleryItems() {
					var items = '';
					<?php
					$all_posts = get_posts(array(
						'post_type' => 'gallery',
						'posts_per_page' => -1,
						'post_status' => 'publish'
					));
					foreach ($all_posts as $post) {
						if (has_post_thumbnail($post->ID)) {
							$image_url = get_the_post_thumbnail_url($post->ID, 'medium');
							$image_full = get_the_post_thumbnail_url($post->ID, 'full');
							echo "items += '<div class=\"gallery-modal-item\"><img src=\"" . esc_js($image_url) . "\" alt=\"" . esc_js($post->post_title) . "\" data-full=\"" . esc_js($image_full) . "\"><div class=\"gallery-item-overlay\"><h4>" . esc_js($post->post_title) . "</h4></div></div>';";
						}
					}
					?>
					return items;
				}

				function initModalHandlers() {
					console.log('Initializing modal handlers...');

					$('.gallery-modal-close').on('click', closeGalleryModal);
					$('.gallery-modal-overlay').on('click', function(e) {
						if (e.target === this) closeGalleryModal();
					});

					// Clean and simple image click handler for modal
					$(document).off('click.galleryModalImages');

					// Try multiple selectors to catch the click
					$(document).on('click.galleryModalImages', '.gallery-modal-item img, .gallery-modal-item', function(e) {
						e.preventDefault();
						e.stopPropagation();

						console.log('Click detected on:', this.tagName, this.className);

						var $img = $(this).is('img') ? $(this) : $(this).find('img');
						var fullSrc = $img.data('full') || $img.attr('src');
						var alt = $img.attr('alt') || 'Immagine Galleria';

						console.log('Modal image clicked:', alt, 'Full URL:', fullSrc);

						if (fullSrc) {
							// Use the same lightbox function as the widget
							openLightbox(fullSrc, alt);
						}
					});

					console.log('Modal handlers initialized');
				}

				function closeGalleryModal() {
					console.log('Closing gallery modal...');
					$('#gallery-full-modal').removeClass('active');
					$('body').removeClass('gallery-modal-open');

					// Clean up event listeners
					$(document).off('click.galleryModal');

					setTimeout(function() {
						$('#gallery-full-modal').remove();
						console.log('Modal removed from DOM');
					}, 300);
				}

				function openLightbox(src, alt) {
					console.log('Opening lightbox with:', src, alt);

					// Simple lightbox with box container
					var lightbox = $('<div class="gallery-lightbox">' +
						'<div class="lightbox-content">' +
						'<img src="' + src + '" alt="' + alt + '">' +
						'<button class="lightbox-close">&times;</button>' +
						'</div>' +
						'</div>');

					$('body').append(lightbox);

					// Add admin bar class if present
					if ($('#wpadminbar').length > 0 && $('#wpadminbar').is(':visible')) {
						$('body').addClass('admin-bar');
					}

					lightbox.fadeIn(300);

					lightbox.on('click', function(e) {
						if (e.target === this || $(e.target).hasClass('lightbox-close')) {
							lightbox.fadeOut(300, function() {
								lightbox.remove();
							});
						}
					});

					// ESC key to close
					$(document).on('keyup.lightbox', function(e) {
						if (e.keyCode === 27) {
							$(document).off('keyup.lightbox');
							lightbox.fadeOut(300, function() {
								lightbox.remove();
							});
						}
					});
				}

				// Gallery zoom button functionality
				$('.gallery-zoom-btn').on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();

					var $galleryItem = $(this).closest('.gallery-item');
					var imgSrc = $galleryItem.find('img').attr('src');
					var imgAlt = $galleryItem.find('img').attr('alt');

					// Get full size image if available
					var fullSrc = imgSrc.replace('-large', '').replace('-medium', '');
					openLightbox(fullSrc, imgAlt);
				});

				// Lightbox functionality for gallery images
				$('.gallery-item img').on('click', function() {
					var src = $(this).attr('src');
					var alt = $(this).attr('alt');

					// Create lightbox
					var lightbox = $('<div class="gallery-lightbox">' +
						'<div class="lightbox-content">' +
						'<img src="' + src + '" alt="' + alt + '">' +
						'<button class="lightbox-close">&times;</button>' +
						'</div>' +
						'</div>');

					$('body').append(lightbox);
					lightbox.fadeIn(300);

					// Close lightbox
					lightbox.on('click', function(e) {
						if (e.target === this || $(e.target).hasClass('lightbox-close')) {
							lightbox.fadeOut(300, function() {
								lightbox.remove();
							});
						}
					});
				});
			});
		</script>
	<?php

		echo $args['after_widget'];
	}

	private function render_gallery_grid($category_slug, $count)
	{
		$args = array(
			'post_type' => 'gallery',
			'posts_per_page' => $count,
			'post_status' => 'publish'
		);

		if ($category_slug !== 'all') {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'gallery_category',
					'field' => 'slug',
					'terms' => $category_slug,
				)
			);
		}

		$gallery_posts = get_posts($args);

		// Debug temporaneo
		error_log('Gallery widget - Category: ' . $category_slug . ', Count: ' . $count);
		error_log('Gallery posts found: ' . count($gallery_posts));
		if ($gallery_posts) {
			foreach ($gallery_posts as $debug_post) {
				error_log('Found gallery post: ' . $debug_post->post_title . ' (ID: ' . $debug_post->ID . ')');
			}
		}

		if ($gallery_posts) {
			echo '<div class="gallery-grid">';
			foreach ($gallery_posts as $post) {
				setup_postdata($post);
				$this->render_gallery_item($post);
			}
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<div class="gallery-no-items">';
			echo '<p>' . __('Nessun elemento in questa categoria al momento.', 'marcello-scavo-tattoo') . '</p>';
			echo '</div>';
		}
	}

	private function render_gallery_item($post)
	{
		$categories = get_the_terms($post->ID, 'gallery_category');
		$category_names = array();
		if ($categories && !is_wp_error($categories)) {
			foreach ($categories as $category) {
				$category_names[] = $category->name;
			}
		}

		echo '<div class="gallery-item">';

		if (has_post_thumbnail($post->ID)) {
			echo '<div class="gallery-item-image">';
			echo '<img src="' . get_the_post_thumbnail_url($post->ID, 'large') . '" alt="' . get_the_title($post->ID) . '" loading="lazy">';
			echo '<div class="gallery-item-overlay">';
			echo '<div class="gallery-item-info">';
			echo '<h4 class="gallery-item-title">' . get_the_title($post->ID) . '</h4>';
			if (!empty($category_names)) {
				echo '<span class="gallery-item-category">' . implode(', ', $category_names) . '</span>';
			}
			echo '</div>';
			echo '<div class="gallery-item-actions">';
			echo '<button class="gallery-zoom-btn" title="' . __('Visualizza', 'marcello-scavo-tattoo') . '">';
			echo '<i class="fas fa-search-plus"></i>';
			echo '</button>';
			echo '</div>';
			echo '</div>';
			echo '</div>';

			// Aggiungiamo sempre lo spazio contenuto sotto l'immagine (spazio dorato)
			echo '<div class="gallery-item-content">';

			// Proviamo diversi modi per ottenere il contenuto
			$content = '';

			// Metodo 1: post_content diretto
			if (!empty($post->post_content)) {
				$content = $post->post_content;
			}
			// Metodo 2: get_post_field
			elseif (!empty(get_post_field('post_content', $post->ID))) {
				$content = get_post_field('post_content', $post->ID);
			}
			// Metodo 3: excerpt
			elseif (!empty($post->post_excerpt)) {
				$content = $post->post_excerpt;
			}
			// Metodo 4: controlla meta fields comuni
			else {
				$possible_meta_keys = array('description', 'gallery_description', '_gallery_description', 'content', 'text');
				foreach ($possible_meta_keys as $meta_key) {
					$meta_content = get_post_meta($post->ID, $meta_key, true);
					if (!empty($meta_content)) {
						$content = $meta_content;
						break;
					}
				}
			}

			if (!empty($content)) {
				$description = wp_strip_all_tags($content);
				$description = trim($description);

				if (!empty($description)) {
					// Limitiamo a circa 100 caratteri per evitare testi troppo lunghi
					if (strlen($description) > 100) {
						$description = substr($description, 0, 100) . '...';
					}
					echo '<p class="gallery-item-description">' . esc_html($description) . '</p>';
				} else {
					echo '<p class="gallery-item-description" style="font-style: italic; opacity: 0.7;">Contenuto trovato ma vuoto dopo la pulizia</p>';
				}
			} else {
				// Debug: mostra tutti i meta fields disponibili
				$all_meta = get_post_meta($post->ID);
				$meta_keys = array_keys($all_meta);
				echo '<p class="gallery-item-description" style="font-style: italic; opacity: 0.7; font-size: 0.7rem;">Debug: Meta keys disponibili: ' . implode(', ', array_slice($meta_keys, 0, 5)) . '</p>';
			}
			echo '</div>';
		}

		echo '</div>';
	}

	private function render_featured_carousel($count)
	{
		$featured_posts = get_posts(array(
			'post_type' => 'gallery',
			'posts_per_page' => $count,
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => '_gallery_is_featured',
					'value' => '1',
					'compare' => '='
				)
			)
		));

		if ($featured_posts) {
			foreach ($featured_posts as $index => $post) {
				$active_class = $index === 0 ? ' active' : '';
				echo '<div class="featured-item' . $active_class . '">';

				if (has_post_thumbnail($post->ID)) {
					echo '<img src="' . get_the_post_thumbnail_url($post->ID, 'large') . '" alt="' . get_the_title($post->ID) . '">';
				}

				echo '<div class="featured-content">';
				echo '<h3>' . get_the_title($post->ID) . '</h3>';
				echo '<p>' . wp_trim_words(get_the_content(null, false, $post), 20) . '</p>';
				echo '</div>';

				echo '</div>';
			}
		}
	}

	public function form($instance)
	{
		$title = isset($instance['title']) ? $instance['title'] : __('Scopri le mie creazioni artistiche', 'marcello-scavo-tattoo');
		$subtitle = isset($instance['subtitle']) ? $instance['subtitle'] : __('Esplora una collezione di opere ispirate ai tatuaggi unici, che raccontano storie e emozioni.', 'marcello-scavo-tattoo');
		$display_mode = isset($instance['display_mode']) ? $instance['display_mode'] : 'categorized_slider';
		$items_per_category = isset($instance['items_per_category']) ? absint($instance['items_per_category']) : 6;
		$show_category_filter = isset($instance['show_category_filter']) ? (bool) $instance['show_category_filter'] : true;
		$auto_rotate = isset($instance['auto_rotate']) ? (bool) $instance['auto_rotate'] : true;
		$rotation_speed = isset($instance['rotation_speed']) ? absint($instance['rotation_speed']) : 4;
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('Sottotitolo:', 'marcello-scavo-tattoo'); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" rows="3"><?php echo esc_textarea($subtitle); ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('display_mode'); ?>"><?php _e('Modalità di visualizzazione:', 'marcello-scavo-tattoo'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('display_mode'); ?>" name="<?php echo $this->get_field_name('display_mode'); ?>">
				<option value="categorized_slider" <?php selected($display_mode, 'categorized_slider'); ?>><?php _e('Slider Categorizzato', 'marcello-scavo-tattoo'); ?></option>
				<option value="masonry_grid" <?php selected($display_mode, 'masonry_grid'); ?>><?php _e('Griglia Masonry', 'marcello-scavo-tattoo'); ?></option>
				<option value="featured_carousel" <?php selected($display_mode, 'featured_carousel'); ?>><?php _e('Carousel In Evidenza', 'marcello-scavo-tattoo'); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('items_per_category'); ?>"><?php _e('Elementi per categoria:', 'marcello-scavo-tattoo'); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id('items_per_category'); ?>" name="<?php echo $this->get_field_name('items_per_category'); ?>" type="number" step="1" min="3" max="12" value="<?php echo $items_per_category; ?>" size="3">
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($show_category_filter); ?> id="<?php echo $this->get_field_id('show_category_filter'); ?>" name="<?php echo $this->get_field_name('show_category_filter'); ?>" />
			<label for="<?php echo $this->get_field_id('show_category_filter'); ?>"><?php _e('Mostra filtri categoria', 'marcello-scavo-tattoo'); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($auto_rotate); ?> id="<?php echo $this->get_field_id('auto_rotate'); ?>" name="<?php echo $this->get_field_name('auto_rotate'); ?>" />
			<label for="<?php echo $this->get_field_id('auto_rotate'); ?>"><?php _e('Rotazione automatica (carousel)', 'marcello-scavo-tattoo'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('rotation_speed'); ?>"><?php _e('Velocità rotazione (secondi):', 'marcello-scavo-tattoo'); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id('rotation_speed'); ?>" name="<?php echo $this->get_field_name('rotation_speed'); ?>" type="number" step="1" min="2" max="10" value="<?php echo $rotation_speed; ?>" size="3">
		</p>

		<hr>
		<p><strong><?php _e('Categorie disponibili:', 'marcello-scavo-tattoo'); ?></strong></p>
		<?php
		$categories = get_terms(array(
			'taxonomy' => 'gallery_category',
			'hide_empty' => false,
		));

		if (!empty($categories) && !is_wp_error($categories)) {
			echo '<ul>';
			foreach ($categories as $category) {
				echo '<li>• ' . esc_html($category->name) . ' (' . $category->count . ' elementi)</li>';
			}
			echo '</ul>';
		} else {
			echo '<p><em>' . __('Nessuna categoria trovata. Crea categorie in Galleria > Categorie.', 'marcello-scavo-tattoo') . '</em></p>';
		}
		?>
	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['subtitle'] = (!empty($new_instance['subtitle'])) ? sanitize_textarea_field($new_instance['subtitle']) : '';
		$instance['display_mode'] = (!empty($new_instance['display_mode'])) ? sanitize_text_field($new_instance['display_mode']) : 'categorized_slider';
		$instance['items_per_category'] = (!empty($new_instance['items_per_category'])) ? absint($new_instance['items_per_category']) : 6;
		$instance['show_category_filter'] = !empty($new_instance['show_category_filter']) ? 1 : 0;
		$instance['auto_rotate'] = !empty($new_instance['auto_rotate']) ? 1 : 0;
		$instance['rotation_speed'] = (!empty($new_instance['rotation_speed'])) ? absint($new_instance['rotation_speed']) : 4;
		return $instance;
	}
}

// Register AJAX handlers for secure image management
add_action('wp_ajax_cleanup_instagram_image', 'handle_instagram_image_cleanup');
add_action('wp_ajax_nopriv_cleanup_instagram_image', 'handle_instagram_image_cleanup_nopriv');

/**
 * Handle secure cleanup of Instagram widget images (admin only)
 * Enhanced with error handling for production
 */
function handle_instagram_image_cleanup()
{
	try {
		// Security checks
		if (!current_user_can('edit_theme_options')) {
			wp_die(__('Permessi insufficienti', 'marcello-scavo-tattoo'), 403);
		}

		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cleanup_instagram_image')) {
			wp_die(__('Nonce non valido', 'marcello-scavo-tattoo'), 403);
		}

		$image_id = isset($_POST['image_id']) ? absint($_POST['image_id']) : 0;

		if ($image_id > 0) {
			// Log the cleanup request (optional)
			if (function_exists('error_log')) {
				error_log("Instagram widget: cleanup requested for image ID $image_id");
			}

			wp_send_json_success(array('message' => __('Immagine rimossa dal widget', 'marcello-scavo-tattoo')));
		} else {
			wp_send_json_error(array('message' => __('ID immagine non valido', 'marcello-scavo-tattoo')));
		}
	} catch (Exception $e) {
		wp_send_json_error(array('message' => __('Errore durante la rimozione', 'marcello-scavo-tattoo')));
	}
}

/**
 * Deny cleanup for non-privileged users
 */
function handle_instagram_image_cleanup_nopriv()
{
	wp_die(__('Accesso negato', 'marcello-scavo-tattoo'), 403);
}

/**
 * Security function: Validate uploaded file for Instagram widget
 * Enhanced with production safety
 */
function validate_instagram_upload($file)
{
	try {
		// Check file type
		$allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/webp');
		if (!in_array($file['type'], $allowed_types)) {
			return new WP_Error('invalid_type', __('Tipo file non consentito per widget Instagram', 'marcello-scavo-tattoo'));
		}

		// Check file size (2MB max)
		if ($file['size'] > 2097152) {
			return new WP_Error('file_too_large', __('File troppo grande per widget Instagram (max 2MB)', 'marcello-scavo-tattoo'));
		}

		return $file;
	} catch (Exception $e) {
		return new WP_Error('validation_error', __('Errore durante la validazione del file', 'marcello-scavo-tattoo'));
	}
}

/**
 * Register custom widgets
 */
function marcello_scavo_register_widgets()
{
	register_widget('Marcello_Scavo_Portfolio_Widget');
	register_widget('Marcello_Scavo_Contact_Widget');
	register_widget('Marcello_Scavo_Contact_Card_Widget');
	register_widget('Marcello_Scavo_Location_Map_Widget');
	register_widget('Marcello_Scavo_Instagram_Feed_Widget');
	register_widget('Marcello_Scavo_Google_Reviews_Widget');
	register_widget('Marcello_Scavo_Bookly_Widget');
	register_widget('Marcello_Scavo_Gallery_Showcase_Widget');
	register_widget('Marcello_Scavo_Secure_Uploader_Widget');
}
add_action('widgets_init', 'marcello_scavo_register_widgets');

/**
 * Widget Uploader Sicuro per Media WordPress
 */
class Marcello_Scavo_Secure_Uploader_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_scavo_secure_uploader',
			'MS - Uploader Sicuro',
			array('description' => 'Widget per caricamento sicuro di immagini nei media di WordPress')
		);
	}

	public function widget($args, $instance)
	{
		echo $args['before_widget'];

		$title = !empty($instance['title']) ? $instance['title'] : 'Carica Immagini';
		$allowed_types = !empty($instance['allowed_types']) ? $instance['allowed_types'] : 'jpg,jpeg,png,webp';
		$max_size = !empty($instance['max_size']) ? intval($instance['max_size']) : 5;
		$max_files = !empty($instance['max_files']) ? intval($instance['max_files']) : 10;

		// Genera ID unici per evitare conflitti
		$widget_id = uniqid('uploader_');

		echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
	?>

		<div class="secure-uploader-widget">
			<div class="uploader-container">
				<div class="upload-area" id="uploadArea_<?php echo $widget_id; ?>">
					<div class="upload-icon">
						<i class="fas fa-cloud-upload-alt"></i>
					</div>
					<div class="upload-text">
						<strong>Clicca per caricare</strong> o trascina le immagini qui
					</div>
					<div class="upload-restrictions">
						Formati: <?php echo strtoupper($allowed_types); ?> | Max <?php echo $max_size; ?>MB per file | Max <?php echo $max_files; ?> file
					</div>
					<input type="file" id="fileInput_<?php echo $widget_id; ?>" multiple accept=".<?php echo str_replace(',', ',.', $allowed_types); ?>" style="display: none;">
				</div>

				<div class="upload-progress" id="uploadProgress_<?php echo $widget_id; ?>" style="display: none;">
					<div class="progress-bar">
						<div class="progress-fill" id="progressFill_<?php echo $widget_id; ?>"></div>
					</div>
					<div class="progress-text" id="progressText_<?php echo $widget_id; ?>">Caricamento...</div>
				</div>

				<div class="upload-results" id="uploadResults_<?php echo $widget_id; ?>"></div>
			</div>
		</div>

		<script>
			jQuery(document).ready(function($) {
				const uploadArea = $('#uploadArea_<?php echo $widget_id; ?>');
				const fileInput = $('#fileInput_<?php echo $widget_id; ?>');
				const uploadProgress = $('#uploadProgress_<?php echo $widget_id; ?>');
				const progressFill = $('#progressFill_<?php echo $widget_id; ?>');
				const progressText = $('#progressText_<?php echo $widget_id; ?>');
				const uploadResults = $('#uploadResults_<?php echo $widget_id; ?>');

				console.log('Uploader widget inizializzato:', '<?php echo $widget_id; ?>');

				// Security settings
				const allowedTypes = '<?php echo $allowed_types; ?>'.split(',');
				const maxSize = <?php echo $max_size * 1024 * 1024; ?>; // Convert to bytes
				const maxFiles = <?php echo $max_files; ?>;

				// Click to upload
				uploadArea.on('click', function() {
					console.log('Upload area clicked');
					fileInput.click();
				});

				// Drag and drop events
				uploadArea.on('dragover', function(e) {
					e.preventDefault();
					$(this).addClass('drag-over');
				});

				uploadArea.on('dragleave', function(e) {
					e.preventDefault();
					$(this).removeClass('drag-over');
				});

				uploadArea.on('drop', function(e) {
					e.preventDefault();
					$(this).removeClass('drag-over');
					const files = e.originalEvent.dataTransfer.files;
					console.log('Files dropped:', files.length);
					handleFiles(files);
				});

				// File input change
				fileInput.on('change', function() {
					console.log('File input changed:', this.files.length);
					handleFiles(this.files);
				});

				function handleFiles(files) {
					console.log('Handling files:', files.length);

					// Validate file count
					if (files.length > maxFiles) {
						showError('Troppi file selezionati. Massimo ' + maxFiles + ' file consentiti.');
						return;
					}

					// Validate each file
					const validFiles = [];
					for (let i = 0; i < files.length; i++) {
						const file = files[i];

						// Check file type
						const extension = file.name.split('.').pop().toLowerCase();
						if (!allowedTypes.includes(extension)) {
							showError('Tipo file non consentito: ' + file.name);
							continue;
						}

						// Check file size
						if (file.size > maxSize) {
							showError('File troppo grande: ' + file.name + ' (max <?php echo $max_size; ?>MB)');
							continue;
						}

						// Check if it's actually an image
						if (!file.type.startsWith('image/')) {
							showError('Il file non è un\'immagine valida: ' + file.name);
							continue;
						}

						validFiles.push(file);
					}

					console.log('Valid files:', validFiles.length);

					if (validFiles.length > 0) {
						uploadFiles(validFiles);
					} else {
						showError('Nessun file valido selezionato.');
					}
				}

				function uploadFiles(files) {
					console.log('Starting upload for', files.length, 'files');
					uploadProgress.show();
					uploadResults.empty();

					let completed = 0;
					const total = files.length;

					files.forEach(function(file, index) {
						console.log('Uploading file:', file.name);

						const formData = new FormData();
						formData.append('file', file);
						formData.append('action', 'secure_upload_image');
						formData.append('nonce', '<?php echo wp_create_nonce('secure_upload_nonce'); ?>');

						$.ajax({
							url: '<?php echo admin_url('admin-ajax.php'); ?>',
							type: 'POST',
							data: formData,
							processData: false,
							contentType: false,
							xhr: function() {
								const xhr = new window.XMLHttpRequest();
								xhr.upload.addEventListener("progress", function(evt) {
									if (evt.lengthComputable) {
										const percentComplete = (evt.loaded / evt.total) * 100;
										const overallProgress = ((completed * 100) + percentComplete) / total;
										updateProgress(overallProgress, 'Caricamento ' + file.name + '...');
									}
								}, false);
								return xhr;
							},
							success: function(response) {
								console.log('Upload response:', response);
								completed++;
								if (response.success) {
									showSuccess('✓ ' + file.name + ' caricato con successo');
								} else {
									showError('✗ Errore caricamento ' + file.name + ': ' + response.data);
								}

								if (completed === total) {
									updateProgress(100, 'Caricamento completato!');
									setTimeout(function() {
										uploadProgress.hide();
										fileInput.val(''); // Reset input
									}, 2000);
								}
							},
							error: function(xhr, status, error) {
								console.error('Upload error:', error);
								completed++;
								showError('✗ Errore di rete per ' + file.name + ': ' + error);

								if (completed === total) {
									uploadProgress.hide();
								}
							}
						});
					});
				}

				function updateProgress(percent, text) {
					progressFill.css('width', percent + '%');
					progressText.text(text);
				}

				function showError(message) {
					console.log('Error:', message);
					uploadResults.append('<div class="upload-message error">' + message + '</div>');
				}

				function showSuccess(message) {
					console.log('Success:', message);
					uploadResults.append('<div class="upload-message success">' + message + '</div>');
				}
			});
		</script>

	<?php
		echo $args['after_widget'];
	}

	public function form($instance)
	{
		$title = !empty($instance['title']) ? $instance['title'] : 'Carica Immagini';
		$allowed_types = !empty($instance['allowed_types']) ? $instance['allowed_types'] : 'jpg,jpeg,png,webp';
		$max_size = !empty($instance['max_size']) ? $instance['max_size'] : '5';
		$max_files = !empty($instance['max_files']) ? $instance['max_files'] : '10';
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Titolo:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('allowed_types'); ?>">Tipi file consentiti (separati da virgola):</label>
			<input class="widefat" id="<?php echo $this->get_field_id('allowed_types'); ?>" name="<?php echo $this->get_field_name('allowed_types'); ?>" type="text" value="<?php echo esc_attr($allowed_types); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('max_size'); ?>">Dimensione massima file (MB):</label>
			<input class="widefat" id="<?php echo $this->get_field_id('max_size'); ?>" name="<?php echo $this->get_field_name('max_size'); ?>" type="number" value="<?php echo esc_attr($max_size); ?>" min="1" max="50">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('max_files'); ?>">Numero massimo file per upload:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('max_files'); ?>" name="<?php echo $this->get_field_name('max_files'); ?>" type="number" value="<?php echo esc_attr($max_files); ?>" min="1" max="50">
		</p>
	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
		$instance['allowed_types'] = (!empty($new_instance['allowed_types'])) ? sanitize_text_field($new_instance['allowed_types']) : 'jpg,jpeg,png,webp';
		$instance['max_size'] = (!empty($new_instance['max_size'])) ? intval($new_instance['max_size']) : 5;
		$instance['max_files'] = (!empty($new_instance['max_files'])) ? intval($new_instance['max_files']) : 10;
		return $instance;
	}
}

// AJAX handler per upload sicuro
add_action('wp_ajax_secure_upload_image', 'handle_secure_upload');
add_action('wp_ajax_nopriv_secure_upload_image', 'handle_secure_upload');

function handle_secure_upload()
{
	// Verifica nonce per sicurezza
	if (!wp_verify_nonce($_POST['nonce'], 'secure_upload_nonce')) {
		wp_die('Accesso negato: nonce non valido');
	}

	// Verifica capacità utente
	if (!current_user_can('upload_files')) {
		wp_send_json_error('Non hai i permessi per caricare file.');
		return;
	}

	// Verifica che sia stato caricato un file
	if (empty($_FILES['file'])) {
		wp_send_json_error('Nessun file ricevuto.');
		return;
	}

	$file = $_FILES['file'];

	// Validazioni di sicurezza avanzate
	try {
		// 1. Verifica estensione file
		$allowed_types = array('jpg', 'jpeg', 'png', 'webp');
		$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

		if (!in_array($file_extension, $allowed_types)) {
			wp_send_json_error('Tipo di file non consentito: ' . $file_extension);
			return;
		}

		// 2. Verifica MIME type reale
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$real_mime = finfo_file($finfo, $file['tmp_name']);
		finfo_close($finfo);

		$allowed_mimes = array(
			'image/jpeg',
			'image/jpg',
			'image/png',
			'image/webp'
		);

		if (!in_array($real_mime, $allowed_mimes)) {
			wp_send_json_error('MIME type non valido: ' . $real_mime);
			return;
		}

		// 3. Verifica che sia effettivamente un'immagine
		$image_info = getimagesize($file['tmp_name']);
		if ($image_info === false) {
			wp_send_json_error('Il file non è un\'immagine valida.');
			return;
		}

		// 4. Verifica dimensioni file
		$max_size = 5 * 1024 * 1024; // 5MB
		if ($file['size'] > $max_size) {
			wp_send_json_error('File troppo grande (max 5MB).');
			return;
		}

		// 5. Sanitizza nome file
		$safe_filename = sanitize_file_name($file['name']);
		$file['name'] = $safe_filename;

		// 6. Upload usando WordPress media handler
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');

		// Override per upload file
		$upload_overrides = array(
			'test_form' => false,
			'mimes' => array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'png' => 'image/png',
				'webp' => 'image/webp'
			)
		);

		$uploaded_file = wp_handle_upload($file, $upload_overrides);

		if (isset($uploaded_file['error'])) {
			wp_send_json_error('Errore upload: ' . $uploaded_file['error']);
			return;
		}

		// 7. Crea attachment nel database
		$attachment = array(
			'guid' => $uploaded_file['url'],
			'post_mime_type' => $uploaded_file['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($safe_filename)),
			'post_content' => '',
			'post_status' => 'inherit'
		);

		$attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);

		if (is_wp_error($attachment_id)) {
			wp_send_json_error('Errore creazione attachment: ' . $attachment_id->get_error_message());
			return;
		}

		// 8. Genera metadata e thumbnails
		$attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
		wp_update_attachment_metadata($attachment_id, $attachment_data);

		// 9. Log di sicurezza
		error_log(sprintf(
			'Secure Upload: User %d uploaded file %s (ID: %d, MIME: %s)',
			get_current_user_id(),
			$safe_filename,
			$attachment_id,
			$real_mime
		));

		wp_send_json_success(array(
			'message' => 'File caricato con successo',
			'attachment_id' => $attachment_id,
			'url' => $uploaded_file['url'],
			'filename' => $safe_filename
		));
	} catch (Exception $e) {
		error_log('Secure Upload Error: ' . $e->getMessage());
		wp_send_json_error('Errore durante l\'upload: ' . $e->getMessage());
	}
}

add_action('after_setup_theme', 'marcello_scavo_languages_setup');

/**
 * Generate Social Links HTML
 */
function marcello_scavo_get_social_links($style = 'modern')
{
	$social_networks = array(
		'instagram' => array('icon' => 'fab fa-instagram', 'color' => '#E4405F', 'name' => 'Instagram'),
		'facebook' => array('icon' => 'fab fa-facebook', 'color' => '#1877F2', 'name' => 'Facebook'),
		'tiktok' => array('icon' => 'fab fa-tiktok', 'color' => '#000000', 'name' => 'TikTok'),
		'youtube' => array('icon' => 'fab fa-youtube', 'color' => '#FF0000', 'name' => 'YouTube'),
		'twitter' => array('icon' => 'fab fa-twitter', 'color' => '#1DA1F2', 'name' => 'Twitter'),
		'linkedin' => array('icon' => 'fab fa-linkedin', 'color' => '#0A66C2', 'name' => 'LinkedIn'),
	);

	$output = '';
	$has_links = false;

	foreach ($social_networks as $network => $data) {
		$url = get_theme_mod("social_link_{$network}", '');
		if (!empty($url)) {
			$has_links = true;
			break;
		}
	}

	if (!$has_links) {
		// Se non ci sono link configurati, mostra link di default per il fallback
		$default_links = array(
			'instagram' => 'https://instagram.com/marcelloscavo_art',
			'facebook' => 'https://facebook.com/marcelloscavo',
			'tiktok' => 'https://tiktok.com/@marcello.scavo',
			'youtube' => 'https://youtube.com/@MarcelloScavo'
		);

		$container_class = "footer-social footer-social-{$style}";
		$output .= '<div class="' . $container_class . '">';

		foreach ($default_links as $network => $url) {
			$data = $social_networks[$network];
			$link_class = "social-link social-link-{$network}";

			switch ($style) {
				case 'minimal':
					$output .= sprintf(
						'<a href="%s" class="%s" target="_blank" rel="noopener" aria-label="%s">
                            <i class="%s"></i>
                        </a>',
						esc_url($url),
						esc_attr($link_class),
						esc_attr($data['name']),
						esc_attr($data['icon'])
					);
					break;

				case 'buttons':
					$output .= sprintf(
						'<a href="%s" class="%s social-button" target="_blank" rel="noopener" aria-label="%s">
                            <i class="%s"></i>
                            <span>%s</span>
                        </a>',
						esc_url($url),
						esc_attr($link_class),
						esc_attr($data['name']),
						esc_attr($data['icon']),
						esc_html($data['name'])
					);
					break;

				case 'cards':
					$output .= sprintf(
						'<div class="social-card social-card-%s">
                            <a href="%s" target="_blank" rel="noopener" aria-label="%s">
                                <i class="%s"></i>
                                <span>%s</span>
                            </a>
                        </div>',
						esc_attr($network),
						esc_url($url),
						esc_attr($data['name']),
						esc_attr($data['icon']),
						esc_html($data['name'])
					);
					break;

				default: // modern
					$output .= sprintf(
						'<a href="%s" class="%s" target="_blank" rel="noopener" aria-label="%s" data-color="%s">
                            <i class="%s"></i>
                        </a>',
						esc_url($url),
						esc_attr($link_class),
						esc_attr($data['name']),
						esc_attr($data['color']),
						esc_attr($data['icon'])
					);
					break;
			}
		}

		$output .= '</div>';
		return $output;
	}

	$container_class = "footer-social footer-social-{$style}";
	$output .= '<div class="' . $container_class . '">';

	foreach ($social_networks as $network => $data) {
		$url = get_theme_mod("social_link_{$network}", '');
		if (!empty($url)) {
			$link_class = "social-link social-link-{$network}";

			switch ($style) {
				case 'minimal':
					$output .= sprintf(
						'<a href="%s" class="%s" target="_blank" rel="noopener" aria-label="%s">
                            <i class="%s"></i>
                        </a>',
						esc_url($url),
						esc_attr($link_class),
						esc_attr($data['name']),
						esc_attr($data['icon'])
					);
					break;

				case 'buttons':
					$output .= sprintf(
						'<a href="%s" class="%s social-button" target="_blank" rel="noopener" aria-label="%s">
                            <i class="%s"></i>
                            <span>%s</span>
                        </a>',
						esc_url($url),
						esc_attr($link_class),
						esc_attr($data['name']),
						esc_attr($data['icon']),
						esc_html($data['name'])
					);
					break;

				case 'cards':
					$output .= sprintf(
						'<div class="social-card social-card-%s">
                            <a href="%s" target="_blank" rel="noopener" aria-label="%s">
                                <i class="%s"></i>
                                <span>%s</span>
                            </a>
                        </div>',
						esc_attr($network),
						esc_url($url),
						esc_attr($data['name']),
						esc_attr($data['icon']),
						esc_html($data['name'])
					);
					break;

				default: // modern
					$output .= sprintf(
						'<a href="%s" class="%s" target="_blank" rel="noopener" aria-label="%s" data-color="%s">
                            <i class="%s"></i>
                        </a>',
						esc_url($url),
						esc_attr($link_class),
						esc_attr($data['name']),
						esc_attr($data['color']),
						esc_attr($data['icon'])
					);
					break;
			}
		}
	}

	$output .= '</div>';

	return $output;
}

/**
 * Custom Footer Content Widget
 * Widget personalizzato per contenuto generico del footer
 */
class Marcello_Scavo_Footer_Content_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_scavo_footer_content',
			__('Footer Content', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Widget personalizzato per aggiungere contenuto generico alle colonne del footer.', 'marcello-scavo-tattoo'),
				'classname' => 'footer-content-widget'
			)
		);
	}

	public function widget($args, $instance)
	{
		echo $args['before_widget'];

		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}

		if (!empty($instance['content'])) {
			echo '<div class="footer-content-text">' . wpautop($instance['content']) . '</div>';
		}

		// Icona opzionale
		if (!empty($instance['icon'])) {
			echo '<div class="footer-content-icon"><i class="' . esc_attr($instance['icon']) . '"></i></div>';
		}

		// Link opzionale
		if (!empty($instance['link_url']) && !empty($instance['link_text'])) {
			echo '<div class="footer-content-link"><a href="' . esc_url($instance['link_url']) . '" target="' . ($instance['link_target'] ? '_blank' : '_self') . '">' . esc_html($instance['link_text']) . '</a></div>';
		}

		echo $args['after_widget'];
	}

	public function form($instance)
	{
		$title = !empty($instance['title']) ? $instance['title'] : '';
		$content = !empty($instance['content']) ? $instance['content'] : '';
		$icon = !empty($instance['icon']) ? $instance['icon'] : '';
		$link_url = !empty($instance['link_url']) ? $instance['link_url'] : '';
		$link_text = !empty($instance['link_text']) ? $instance['link_text'] : '';
		$link_target = !empty($instance['link_target']) ? $instance['link_target'] : false;
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('content'); ?>"><?php _e('Contenuto:', 'marcello-scavo-tattoo'); ?></label>
			<textarea class="widefat" rows="5" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>"><?php echo esc_textarea($content); ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('icon'); ?>"><?php _e('Icona FontAwesome (es: fas fa-phone):', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('icon'); ?>" name="<?php echo $this->get_field_name('icon'); ?>" type="text" value="<?php echo esc_attr($icon); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('link_url'); ?>"><?php _e('URL Link (opzionale):', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('link_url'); ?>" name="<?php echo $this->get_field_name('link_url'); ?>" type="url" value="<?php echo esc_attr($link_url); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('link_text'); ?>"><?php _e('Testo Link:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" name="<?php echo $this->get_field_name('link_text'); ?>" type="text" value="<?php echo esc_attr($link_text); ?>">
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($link_target); ?> id="<?php echo $this->get_field_id('link_target'); ?>" name="<?php echo $this->get_field_name('link_target'); ?>" />
			<label for="<?php echo $this->get_field_id('link_target'); ?>"><?php _e('Apri link in nuova finestra', 'marcello-scavo-tattoo'); ?></label>
		</p>
	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
		$instance['content'] = !empty($new_instance['content']) ? wp_kses_post($new_instance['content']) : '';
		$instance['icon'] = !empty($new_instance['icon']) ? sanitize_text_field($new_instance['icon']) : '';
		$instance['link_url'] = !empty($new_instance['link_url']) ? esc_url_raw($new_instance['link_url']) : '';
		$instance['link_text'] = !empty($new_instance['link_text']) ? sanitize_text_field($new_instance['link_text']) : '';
		$instance['link_target'] = !empty($new_instance['link_target']) ? 1 : 0;

		return $instance;
	}
}

/**
 * Custom Footer Contact Widget
 * Widget personalizzato per informazioni di contatto nel footer
 */
class Marcello_Scavo_Footer_Contact_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_scavo_footer_contact',
			__('Footer Contact Info', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Widget per visualizzare informazioni di contatto nel footer.', 'marcello-scavo-tattoo'),
				'classname' => 'footer-contact-widget'
			)
		);
	}

	public function widget($args, $instance)
	{
		echo $args['before_widget'];

		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}

		echo '<div class="footer-contact-info">';

		if (!empty($instance['address'])) {
			echo '<div class="contact-item address"><i class="fas fa-map-marker-alt"></i> ' . wpautop($instance['address']) . '</div>';
		}

		if (!empty($instance['phone'])) {
			echo '<div class="contact-item phone"><i class="fas fa-phone"></i> <a href="tel:' . esc_attr(str_replace(' ', '', $instance['phone'])) . '">' . esc_html($instance['phone']) . '</a></div>';
		}

		if (!empty($instance['email'])) {
			echo '<div class="contact-item email"><i class="fas fa-envelope"></i> <a href="mailto:' . esc_attr($instance['email']) . '">' . esc_html($instance['email']) . '</a></div>';
		}

		if (!empty($instance['hours'])) {
			echo '<div class="contact-item hours"><i class="fas fa-clock"></i> ' . wpautop($instance['hours']) . '</div>';
		}

		echo '</div>';

		echo $args['after_widget'];
	}

	public function form($instance)
	{
		$title = !empty($instance['title']) ? $instance['title'] : __('Contatti', 'marcello-scavo-tattoo');
		$address = !empty($instance['address']) ? $instance['address'] : '';
		$phone = !empty($instance['phone']) ? $instance['phone'] : '';
		$email = !empty($instance['email']) ? $instance['email'] : '';
		$hours = !empty($instance['hours']) ? $instance['hours'] : '';
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('address'); ?>"><?php _e('Indirizzo:', 'marcello-scavo-tattoo'); ?></label>
			<textarea class="widefat" rows="3" id="<?php echo $this->get_field_id('address'); ?>" name="<?php echo $this->get_field_name('address'); ?>"><?php echo esc_textarea($address); ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('phone'); ?>"><?php _e('Telefono:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('phone'); ?>" name="<?php echo $this->get_field_name('phone'); ?>" type="tel" value="<?php echo esc_attr($phone); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('email'); ?>"><?php _e('Email:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="email" value="<?php echo esc_attr($email); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('hours'); ?>"><?php _e('Orari (opzionale):', 'marcello-scavo-tattoo'); ?></label>
			<textarea class="widefat" rows="3" id="<?php echo $this->get_field_id('hours'); ?>" name="<?php echo $this->get_field_name('hours'); ?>"><?php echo esc_textarea($hours); ?></textarea>
		</p>
	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
		$instance['address'] = !empty($new_instance['address']) ? wp_kses_post($new_instance['address']) : '';
		$instance['phone'] = !empty($new_instance['phone']) ? sanitize_text_field($new_instance['phone']) : '';
		$instance['email'] = !empty($new_instance['email']) ? sanitize_email($new_instance['email']) : '';
		$instance['hours'] = !empty($new_instance['hours']) ? wp_kses_post($new_instance['hours']) : '';

		return $instance;
	}
}

/**
 * Custom Footer Social Widget
 * Widget personalizzato per link social nel footer
 */
class Marcello_Scavo_Footer_Social_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_scavo_footer_social',
			__('Footer Social Links', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Widget per visualizzare link social nel footer con stile personalizzabile.', 'marcello-scavo-tattoo'),
				'classname' => 'footer-social-widget'
			)
		);
	}

	public function widget($args, $instance)
	{
		echo $args['before_widget'];

		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}

		$style = !empty($instance['style']) ? $instance['style'] : 'modern';

		// Usa la funzione esistente per i social links
		echo marcello_scavo_get_social_links($style);

		echo $args['after_widget'];
	}

	public function form($instance)
	{
		$title = !empty($instance['title']) ? $instance['title'] : __('Seguici', 'marcello-scavo-tattoo');
		$style = !empty($instance['style']) ? $instance['style'] : 'modern';
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Stile Social Icons:', 'marcello-scavo-tattoo'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
				<option value="modern" <?php selected($style, 'modern'); ?>><?php _e('Moderno (Circoli colorati)', 'marcello-scavo-tattoo'); ?></option>
				<option value="minimal" <?php selected($style, 'minimal'); ?>><?php _e('Minimale (Solo icone)', 'marcello-scavo-tattoo'); ?></option>
				<option value="buttons" <?php selected($style, 'buttons'); ?>><?php _e('Pulsanti (Con testo)', 'marcello-scavo-tattoo'); ?></option>
				<option value="cards" <?php selected($style, 'cards'); ?>><?php _e('Card (Stile card)', 'marcello-scavo-tattoo'); ?></option>
			</select>
		</p>

		<p><small><?php _e('I link social si configurano nel Personalizza > Layout Footer > Social Media.', 'marcello-scavo-tattoo'); ?></small></p>
	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
		$instance['style'] = !empty($new_instance['style']) ? sanitize_text_field($new_instance['style']) : 'modern';

		return $instance;
	}
}

/**
 * Register Custom Widgets
 */
function marcello_scavo_register_custom_widgets()
{
	register_widget('Marcello_Scavo_Footer_Content_Widget');
	register_widget('Marcello_Scavo_Footer_Contact_Widget');
	register_widget('Marcello_Scavo_Footer_Social_Widget');
}
add_action('widgets_init', 'marcello_scavo_register_custom_widgets');

/**
 * Manual Language Switching Support
 * Handles language switching when no multilingual plugin is available
 */
function marcello_scavo_handle_language_switch()
{
	// Check if a multilingual plugin is active
	if (function_exists('pll_the_languages') || function_exists('icl_get_languages')) {
		return; // Let the multilingual plugin handle language switching
	}

	// Check for language preference in cookie or URL
	$lang_code = '';

	if (isset($_COOKIE['marcello_scavo_language'])) {
		$lang_code = sanitize_text_field($_COOKIE['marcello_scavo_language']);
	}

	if (isset($_GET['lang'])) {
		$lang_code = sanitize_text_field($_GET['lang']);
		// Set cookie for future visits
		setcookie('marcello_scavo_language', $lang_code, time() + (365 * 24 * 60 * 60), '/');
	}

	// Apply language if valid
	if (in_array($lang_code, array('it', 'en', 'es'))) {
		marcello_scavo_set_language($lang_code);
	}
}

/**
 * Set Language for Manual Language Switching
 */
function marcello_scavo_set_language($lang_code)
{
	$locale_map = array(
		'it' => 'it_IT',
		'en' => 'en_US',
		'es' => 'es_ES'
	);

	if (isset($locale_map[$lang_code])) {
		$locale = $locale_map[$lang_code];

		// Add filter to change locale
		add_filter('locale', function () use ($locale) {
			return $locale;
		});

		// Reload text domain with new locale
		unload_textdomain('marcello-scavo-tattoo');
		load_theme_textdomain('marcello-scavo-tattoo', get_template_directory() . '/languages');
	}
}

/**
 * Get Current Language Code
 */
function marcello_scavo_get_current_language()
{
	// Check for Polylang
	if (function_exists('pll_current_language')) {
		$current = pll_current_language();
		return $current ? $current : 'it';
	}

	// Check for WPML
	if (function_exists('icl_get_current_language')) {
		return icl_get_current_language();
	}

	// Check manual language setting
	if (isset($_COOKIE['marcello_scavo_language'])) {
		return sanitize_text_field($_COOKIE['marcello_scavo_language']);
	}

	// Get from WordPress locale
	$locale = get_locale();
	$locale_map = array(
		'it_IT' => 'it',
		'en_US' => 'en',
		'es_ES' => 'es'
	);

	return isset($locale_map[$locale]) ? $locale_map[$locale] : 'it';
}

/**
 * Language Switcher Shortcode
 * Usage: [language_switcher]
 */
function marcello_scavo_language_switcher_shortcode($atts)
{
	$atts = shortcode_atts(array(
		'style' => 'dropdown', // dropdown, links, flags
		'show_flags' => true,
		'show_names' => true
	), $atts);

	// If multilingual plugin is active, let it handle
	if (function_exists('pll_the_languages')) {
		ob_start();
		pll_the_languages(array(
			'dropdown' => ($atts['style'] === 'dropdown'),
			'show_names' => $atts['show_names'],
			'show_flags' => $atts['show_flags']
		));
		return ob_get_clean();
	}

	if (function_exists('icl_get_languages')) {
		$languages = icl_get_languages('skip_missing=0&orderby=code');
		if (!empty($languages)) {
			$output = '<select onchange="window.location.href=this.value">';
			foreach ($languages as $lang) {
				$selected = $lang['active'] ? 'selected' : '';
				$output .= '<option value="' . esc_url($lang['url']) . '" ' . $selected . '>';
				$output .= esc_html($lang['native_name']);
				$output .= '</option>';
			}
			$output .= '</select>';
			return $output;
		}
	}

	// Manual language switcher
	$current_lang = marcello_scavo_get_current_language();
	$languages = array(
		'it' => array('name' => 'Italiano', 'flag' => '🇮🇹'),
		'en' => array('name' => 'English', 'flag' => '🇺🇸'),
		'es' => array('name' => 'Español', 'flag' => '🇪🇸')
	);

	if ($atts['style'] === 'dropdown') {
		$output = '<select id="manual-language-select" onchange="marcelloScavoChangeLanguage(this.value)">';
		foreach ($languages as $code => $lang) {
			$selected = ($code === $current_lang) ? 'selected' : '';
			$flag = $atts['show_flags'] ? $lang['flag'] . ' ' : '';
			$name = $atts['show_names'] ? $lang['name'] : '';
			$output .= '<option value="' . esc_attr($code) . '" ' . $selected . '>';
			$output .= $flag . $name;
			$output .= '</option>';
		}
		$output .= '</select>';
	} else {
		$output = '<div class="language-switcher-links">';
		foreach ($languages as $code => $lang) {
			$active = ($code === $current_lang) ? ' active' : '';
			$flag = $atts['show_flags'] ? $lang['flag'] . ' ' : '';
			$name = $atts['show_names'] ? $lang['name'] : '';
			$output .= '<a href="#" class="lang-link' . $active . '" onclick="marcelloScavoChangeLanguage(\'' . esc_attr($code) . '\'); return false;">';
			$output .= $flag . $name;
			$output .= '</a> ';
		}
		$output .= '</div>';
	}

	return $output;
}
add_shortcode('language_switcher', 'marcello_scavo_language_switcher_shortcode');
add_action('widgets_init', 'marcello_scavo_register_custom_widgets');

// Aree widget dedicate per la pagina Portfolio
function marcello_scavo_register_portfolio_widgets()
{
	register_sidebar(array(
		'name'          => __('Portfolio - Galleria', 'marcello-scavo-tattoo'),
		'id'            => 'portfolio-gallery',
		'description'   => __('Widget per la galleria d’arte nella pagina Portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
	register_sidebar(array(
		'name'          => __('Portfolio - Tatuaggi Recenti', 'marcello-scavo-tattoo'),
		'id'            => 'portfolio-latest',
		'description'   => __('Widget per i tatuaggi recenti nella pagina Portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
	register_sidebar(array(
		'name'          => __('Portfolio - Testimonianze', 'marcello-scavo-tattoo'),
		'id'            => 'portfolio-testimonials',
		'description'   => __('Widget per le recensioni/testimonianze nella pagina Portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
	register_sidebar(array(
		'name'          => __('Portfolio - Prenotazione', 'marcello-scavo-tattoo'),
		'id'            => 'portfolio-cta',
		'description'   => __('Widget per la call-to-action/prenotazione nella pagina Portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
	register_sidebar(array(
		'name'          => __('Portfolio - Mappa', 'marcello-scavo-tattoo'),
		'id'            => 'portfolio-map',
		'description'   => __('Widget per la mappa/localizzazione nella pagina Portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
	register_sidebar(array(
		'name'          => __('Portfolio - Social', 'marcello-scavo-tattoo'),
		'id'            => 'portfolio-social',
		'description'   => __('Widget per i social nella pagina Portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
	register_sidebar(array(
		'name'          => __('Portfolio - Slider Galleria', 'marcello-scavo-tattoo'),
		'id'            => 'portfolio-gallery-slider',
		'description'   => __('Widget slider per la galleria d\'arte nella pagina Portfolio. Alternativa alla griglia normale.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget portfolio-slider %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
}
add_action('widgets_init', 'marcello_scavo_register_portfolio_widgets', 30);

/**
 * Widget Slider Personalizzato per Portfolio Gallery
 */
class Marcello_Scavo_Portfolio_Slider_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct(
			'marcello_scavo_portfolio_slider',
			__('Portfolio Slider', 'marcello-scavo-tattoo'),
			array(
				'description' => __('Slider per mostrare la galleria del portfolio con effetti di transizione.', 'marcello-scavo-tattoo'),
			)
		);
	}

	public function widget($args, $instance)
	{
		echo $args['before_widget'];

		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}

		$category = !empty($instance['category']) ? $instance['category'] : '';
		$posts_per_page = !empty($instance['posts_per_page']) ? (int)$instance['posts_per_page'] : 8;
		$autoplay = !empty($instance['autoplay']) ? $instance['autoplay'] : 'true';
		$autoplay_speed = !empty($instance['autoplay_speed']) ? (int)$instance['autoplay_speed'] : 5000;

		$query_args = array(
			'post_type' => 'portfolio',
			'posts_per_page' => $posts_per_page,
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => '_thumbnail_id',
					'compare' => 'EXISTS'
				)
			)
		);

		if (!empty($category)) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'portfolio_category',
					'field'    => 'slug',
					'terms'    => $category,
				)
			);
		}

		$portfolio_query = new WP_Query($query_args);

		if ($portfolio_query->have_posts()) {
			echo '<div class="portfolio-slider-container" data-autoplay="' . esc_attr($autoplay) . '" data-autoplay-speed="' . esc_attr($autoplay_speed) . '">';
			echo '<div class="portfolio-slider-wrapper">';
			echo '<div class="portfolio-slider-track">';

			while ($portfolio_query->have_posts()) {
				$portfolio_query->the_post();
				$image_id = get_post_thumbnail_id();
				$image_url = wp_get_attachment_image_url($image_id, 'portfolio-large');
				$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);

				echo '<div class="portfolio-slider-slide">';
				echo '<div class="portfolio-slider-image">';
				if ($image_url) {
					echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt ? $image_alt : get_the_title()) . '" loading="lazy">';
				}
				echo '<div class="portfolio-slider-overlay">';
				echo '<div class="portfolio-slider-content">';
				echo '<h4 class="portfolio-slider-title">' . get_the_title() . '</h4>';
				if (has_excerpt()) {
					echo '<p class="portfolio-slider-excerpt">' . get_the_excerpt() . '</p>';
				}
				$terms = get_the_terms(get_the_ID(), 'portfolio_category');
				if ($terms && !is_wp_error($terms)) {
					echo '<div class="portfolio-slider-categories">';
					foreach ($terms as $term) {
						echo '<span class="portfolio-category-tag">' . esc_html($term->name) . '</span>';
					}
					echo '</div>';
				}
				echo '<a href="' . get_permalink() . '" class="portfolio-slider-link">' . __('Visualizza', 'marcello-scavo-tattoo') . '</a>';
				echo '</div>';
				echo '</div>';
				echo '</div>';
				echo '</div>';
			}

			echo '</div>';
			echo '</div>';

			// Controlli slider
			echo '<div class="portfolio-slider-controls">';
			echo '<button class="portfolio-slider-prev" aria-label="' . __('Slide precedente', 'marcello-scavo-tattoo') . '">‹</button>';
			echo '<button class="portfolio-slider-next" aria-label="' . __('Slide successiva', 'marcello-scavo-tattoo') . '">›</button>';
			echo '</div>';

			// Indicatori
			echo '<div class="portfolio-slider-dots">';
			for ($i = 0; $i < $portfolio_query->post_count; $i++) {
				echo '<button class="portfolio-slider-dot' . ($i === 0 ? ' active' : '') . '" data-slide="' . $i . '" aria-label="' . sprintf(__('Vai alla slide %s', 'marcello-scavo-tattoo'), $i + 1) . '"></button>';
			}
			echo '</div>';

			echo '</div>';
		} else {
			echo '<p class="no-portfolio-items">' . __('Nessun lavoro del portfolio trovato.', 'marcello-scavo-tattoo') . '</p>';
		}

		wp_reset_postdata();
		echo $args['after_widget'];
	}

	public function form($instance)
	{
		$title = !empty($instance['title']) ? $instance['title'] : __('Galleria Portfolio', 'marcello-scavo-tattoo');
		$category = !empty($instance['category']) ? $instance['category'] : '';
		$posts_per_page = !empty($instance['posts_per_page']) ? $instance['posts_per_page'] : 8;
		$autoplay = !empty($instance['autoplay']) ? $instance['autoplay'] : 'true';
		$autoplay_speed = !empty($instance['autoplay_speed']) ? $instance['autoplay_speed'] : 5000;
	?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php _e('Categoria Portfolio:', 'marcello-scavo-tattoo'); ?></label>
			<select class="widefat" id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>">
				<option value=""><?php _e('Tutte le categorie', 'marcello-scavo-tattoo'); ?></option>
				<?php
				$categories = get_terms(array(
					'taxonomy' => 'portfolio_category',
					'hide_empty' => false,
				));
				foreach ($categories as $cat) {
					echo '<option value="' . esc_attr($cat->slug) . '"' . selected($category, $cat->slug, false) . '>' . esc_html($cat->name) . '</option>';
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('posts_per_page')); ?>"><?php _e('Numero di slide:', 'marcello-scavo-tattoo'); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('posts_per_page')); ?>" name="<?php echo esc_attr($this->get_field_name('posts_per_page')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($posts_per_page); ?>" size="3">
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked($autoplay, 'true'); ?> id="<?php echo esc_attr($this->get_field_id('autoplay')); ?>" name="<?php echo esc_attr($this->get_field_name('autoplay')); ?>" value="true">
			<label for="<?php echo esc_attr($this->get_field_id('autoplay')); ?>"><?php _e('Autoplay slider', 'marcello-scavo-tattoo'); ?></label>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('autoplay_speed')); ?>"><?php _e('Velocità autoplay (ms):', 'marcello-scavo-tattoo'); ?></label>
			<input class="small-text" id="<?php echo esc_attr($this->get_field_id('autoplay_speed')); ?>" name="<?php echo esc_attr($this->get_field_name('autoplay_speed')); ?>" type="number" step="500" min="1000" value="<?php echo esc_attr($autoplay_speed); ?>" size="5">
		</p>
<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
		$instance['category'] = (!empty($new_instance['category'])) ? sanitize_text_field($new_instance['category']) : '';
		$instance['posts_per_page'] = (!empty($new_instance['posts_per_page'])) ? absint($new_instance['posts_per_page']) : 8;
		$instance['autoplay'] = (!empty($new_instance['autoplay'])) ? 'true' : 'false';
		$instance['autoplay_speed'] = (!empty($new_instance['autoplay_speed'])) ? absint($new_instance['autoplay_speed']) : 5000;
		return $instance;
	}
}

// Registra il widget
function marcello_scavo_register_portfolio_slider_widget()
{
	register_widget('Marcello_Scavo_Portfolio_Slider_Widget');
}
add_action('widgets_init', 'marcello_scavo_register_portfolio_slider_widget');

// Aree widget specifiche per il template taxonomy portfolio category
function marcello_scavo_register_taxonomy_portfolio_widgets()
{
	register_sidebar(array(
		'name'          => __('Taxonomy Portfolio - Hero Section', 'marcello-scavo-tattoo'),
		'id'            => 'taxonomy-portfolio-hero',
		'description'   => __('Widget per personalizzare la hero section delle categorie portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Taxonomy Portfolio - Galleria', 'marcello-scavo-tattoo'),
		'id'            => 'taxonomy-portfolio-gallery',
		'description'   => __('Widget per personalizzare la sezione galleria delle categorie portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Taxonomy Portfolio - Lavori Recenti', 'marcello-scavo-tattoo'),
		'id'            => 'taxonomy-portfolio-latest',
		'description'   => __('Widget per personalizzare la sezione lavori recenti delle categorie portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Taxonomy Portfolio - Testimonianze', 'marcello-scavo-tattoo'),
		'id'            => 'taxonomy-portfolio-testimonials',
		'description'   => __('Widget per personalizzare la sezione testimonianze delle categorie portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Taxonomy Portfolio - Prenota Appuntamento', 'marcello-scavo-tattoo'),
		'id'            => 'taxonomy-portfolio-booking',
		'description'   => __('Widget per il modulo di prenotazione appuntamenti (es. Bookly). Inserire qui il plugin di prenotazione.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Taxonomy Portfolio - CTA Generale', 'marcello-scavo-tattoo'),
		'id'            => 'taxonomy-portfolio-cta',
		'description'   => __('Widget per chiamate all\'azione generali delle categorie portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Taxonomy Portfolio - Perché Sceglierci', 'marcello-scavo-tattoo'),
		'id'            => 'taxonomy-portfolio-why',
		'description'   => __('Widget per personalizzare la sezione "Perché sceglierci" delle categorie portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Taxonomy Portfolio - Contatti', 'marcello-scavo-tattoo'),
		'id'            => 'taxonomy-portfolio-contact',
		'description'   => __('Widget per personalizzare la sezione contatti delle categorie portfolio.', 'marcello-scavo-tattoo'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
}
add_action('widgets_init', 'marcello_scavo_register_taxonomy_portfolio_widgets', 35);

/**
 * Register 3D Gallery Widget Areas
 */
function marcello_scavo_register_3d_gallery_widget_areas()
{
	register_sidebar(array(
		'name'          => __('🎨 3D Gallery Hero (Categoria Portfolio)', 'marcello-scavo-tattoo'),
		'id'            => 'taxonomy-portfolio-3d-hero',
		'description'   => __('Area widget per galleria 3D nelle categorie "Illustrazioni", "Disegni", "Quadri" e "Arte". Utilizza il widget "🎨 3D Gallery Hero" per creare un\'esperienza immersiva.', 'marcello-scavo-tattoo'),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	));
}
add_action('widgets_init', 'marcello_scavo_register_3d_gallery_widget_areas', 40);

/**
 * Enqueue lazy loading script for portfolio gallery images
 */
function marcello_scavo_enqueue_lazy_loading_script()
{
	if (is_tax('portfolio_category') || is_post_type_archive('portfolio') || is_singular('portfolio')) {
		wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                // Lazy loading per immagini portfolio
                function initLazyLoading() {
                    const lazyImages = document.querySelectorAll("img[loading=\'lazy\']");
                    
                    if ("IntersectionObserver" in window) {
                        const imageObserver = new IntersectionObserver(function(entries, observer) {
                            entries.forEach(function(entry) {
                                if (entry.isIntersecting) {
                                    const image = entry.target;
                                    image.classList.add("loaded");
                                    imageObserver.unobserve(image);
                                }
                            });
                        });
                        
                        lazyImages.forEach(function(image) {
                            imageObserver.observe(image);
                        });
                    } else {
                        // Fallback per browser più vecchi
                        lazyImages.forEach(function(image) {
                            image.classList.add("loaded");
                        });
                    }
                }
                
                // Inizializza al caricamento della pagina
                initLazyLoading();
                
                // Re-inizializza dopo AJAX (per eventuali caricamenti dinamici)
                $(document).ajaxComplete(function() {
                    setTimeout(initLazyLoading, 100);
                });
            });
        ');
	}
}
add_action('wp_enqueue_scripts', 'marcello_scavo_enqueue_lazy_loading_script');

/**
 * Include 3D Gallery Widget
 */
require_once get_template_directory() . '/inc/3d-gallery-widget.php';
