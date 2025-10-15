<?php

/**
 * IntelliSense Test File
 * This file is used to test if WordPress functions are properly recognized
 */

// Test WordPress functions
function test_wordpress_functions() {
	// Core WordPress functions
	$post_id    = get_the_ID();
	$post_title = get_the_title();
	$content    = get_the_content();

	// WordPress hooks
	add_action( 'wp_head', 'test_function' );
	add_filter( 'the_content', 'test_filter' );

	// WordPress constants
	$upload_dir = WP_CONTENT_DIR;
	$debug      = WP_DEBUG;

	// Database functions
	global $wpdb;
	$results = $wpdb->get_results( "SELECT * FROM {$wpdb->posts}" );

	// User functions
	$current_user = wp_get_current_user();
	$can_edit     = current_user_can( 'edit_posts' );

	// Theme functions
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	// Enqueue functions
	wp_enqueue_style( 'test-style', 'style.css' );
	wp_enqueue_script( 'test-script', 'script.js' );

	return true;
}

// Test theme-specific constants
function test_theme_constants() {
	$theme_dir = MARCELLO_THEME_DIR;
	$theme_url = MARCELLO_THEME_URL;

	return array(
		'dir' => $theme_dir,
		'url' => $theme_url,
	);
}

// Test WordPress classes
function test_wordpress_classes() {
	$post  = new WP_Post( 1 );
	$query = new WP_Query();
	$user  = new WP_User();

	return compact( 'post', 'query', 'user' );
}
