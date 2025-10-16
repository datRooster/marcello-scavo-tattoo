<?php

/**
 * Theme Setup and Configuration
 *
 * @package MarcelloScavoTattoo
 * @subpackage Setup
 */

// Prevent direct access.
if (! defined('ABSPATH')) {
	exit;
}

/**
 * Theme Setup
 */
function marcello_scavo_theme_setup()
{
	// Add theme support.
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('custom-logo');
	add_theme_support('custom-header');
	add_theme_support('custom-background');
	add_theme_support('customize-selective-refresh-widgets');
	add_theme_support('wp-block-styles');
	add_theme_support('align-wide');

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
			'primary' => __('Menu Principale', 'marcello-scavo-tattoo'),
			'footer'  => __('Menu Footer', 'marcello-scavo-tattoo'),
			'social'  => __('Social Media', 'marcello-scavo-tattoo'),
		)
	);

	// Image sizes.
	add_image_size('portfolio-thumb', 400, 300, true);
	add_image_size('portfolio-large', 800, 600, true);
	add_image_size('hero-bg', 1920, 1080, true);

	// Load theme textdomain for translations.
	load_theme_textdomain('marcello-scavo-tattoo', get_template_directory() . '/languages');

	// Hook for manual language switching support.
	add_action('init', 'marcello_scavo_handle_language_switch');
}
add_action('after_setup_theme', 'marcello_scavo_theme_setup');

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
 * Handle manual language switching
 */
function marcello_scavo_handle_language_switch()
{
	// Language switching logic here if needed.
}

/**
 * Get social media links with optional styling
 *
 * @param string $style Style type for social links (modern, classic, minimal).
 * @return string HTML output for social links.
 */
function marcello_scavo_get_social_links($style = 'modern')
{
	// Get social links from theme customizer.
	$social_links = array(
		'facebook'  => get_theme_mod('social_facebook', ''),
		'instagram' => get_theme_mod('social_instagram', ''),
		'twitter'   => get_theme_mod('social_twitter', ''),
		'linkedin'  => get_theme_mod('social_linkedin', ''),
		'youtube'   => get_theme_mod('social_youtube', ''),
		'tiktok'    => get_theme_mod('social_tiktok', ''),
		'whatsapp'  => get_theme_mod('social_whatsapp', ''),
		'email'     => get_theme_mod('social_email', ''),
	);

	// Filter out empty links.
	$social_links = array_filter($social_links);

	if (empty($social_links)) {
		return '';
	}

	// Icon mapping.
	$social_icons = array(
		'facebook'  => 'fab fa-facebook-f',
		'instagram' => 'fab fa-instagram',
		'twitter'   => 'fab fa-twitter',
		'linkedin'  => 'fab fa-linkedin-in',
		'youtube'   => 'fab fa-youtube',
		'tiktok'    => 'fab fa-tiktok',
		'whatsapp'  => 'fab fa-whatsapp',
		'email'     => 'fas fa-envelope',
	);

	// Generate social links HTML.
	$output = '<div class="social-links social-style-' . esc_attr($style) . '">';

	foreach ($social_links as $platform => $url) {
		if (empty($url)) {
			continue;
		}

		// Handle email links.
		if ('email' === $platform) {
			$url = 'mailto:' . $url;
		}

		// Handle WhatsApp links.
		if ('whatsapp' === $platform) {
			$url = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $url);
		}

		$icon_class    = isset($social_icons[$platform]) ? $social_icons[$platform] : 'fas fa-link';
		$platform_name = ucfirst($platform);

		$output .= sprintf(
			'<a href="%s" class="social-link social-%s" target="_blank" rel="noopener noreferrer" aria-label="%s">
				<i class="%s" aria-hidden="true"></i>
				<span class="sr-only">%s</span>
			</a>',
			esc_url($url),
			esc_attr($platform),
			// translators: %s is the social media platform name.
			esc_attr(sprintf(__('Seguici su %s', 'marcello-scavo-tattoo'), $platform_name)),
			esc_attr($icon_class),
			esc_html($platform_name)
		);
	}

	$output .= '</div>';

	return $output;
}
