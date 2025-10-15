<?php
/**
 * Theme Setup and Configuration
 *
 * @package MarcelloScavoTattoo
 * @subpackage Setup
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme Setup
 */
function marcello_scavo_theme_setup() {
	// Add theme support.
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'custom-header' );
	add_theme_support( 'custom-background' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );

	// HTML5 support.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		)
	);

	// Register navigation menus.
	register_nav_menus(
		array(
			'primary' => __( 'Menu Principale', 'marcello-scavo-tattoo' ),
			'footer'  => __( 'Menu Footer', 'marcello-scavo-tattoo' ),
			'social'  => __( 'Social Media', 'marcello-scavo-tattoo' ),
		)
	);

	// Image sizes.
	add_image_size( 'portfolio-thumb', 400, 300, true );
	add_image_size( 'portfolio-large', 800, 600, true );
	add_image_size( 'hero-bg', 1920, 1080, true );

	// Load theme textdomain for translations.
	load_theme_textdomain( 'marcello-scavo-tattoo', get_template_directory() . '/languages' );

	// Hook for manual language switching support.
	add_action( 'init', 'marcello_scavo_handle_language_switch' );
}
add_action( 'after_setup_theme', 'marcello_scavo_theme_setup' );

/**
 * Force cache refresh for development
 * This adds cache-busting headers to prevent browser caching during development
 */
function marcello_scavo_no_cache_headers() {
	if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
	}
}
add_action( 'send_headers', 'marcello_scavo_no_cache_headers' );

/**
 * Handle manual language switching
 */
function marcello_scavo_handle_language_switch() {
	// Language switching logic here if needed.
}
