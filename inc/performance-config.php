<?php
/**
 * Performance Configuration
 * Marcello Scavo Tattoo Theme
 *
 * Centralizes all performance-related settings
 *
 * @package MarcelloScavoTattoo
 * @subpackage Performance
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Performance Configuration Class
 */
class MarcelloScavoPerformance {




	/**
	 * CSS optimization settings
	 */
	const CSS_SETTINGS = array(
		'enable_minification'        => true,
		'enable_critical_css'        => true,
		'enable_css_defer'           => true,
		'enable_conditional_loading' => true,
		'cache_duration'             => 31536000, // 1 year in seconds.
		'critical_css_files'         => array(
			'critical.css',
		),
		'conditional_files'          => array(
			'portfolio.css' => array(
				'is_singular' => 'portfolio',
				'is_tax'      => 'portfolio_category',
			),
			'gallery.css'   => array(
				'is_singular' => 'gallery',
				'is_tax'      => 'gallery_category',
			),
		),
	);

	/**
	 * Font optimization settings
	 */
	const FONT_SETTINGS = array(
		'enable_font_preload'      => true,
		'enable_font_display_swap' => true,
		'critical_fonts'           => array(
			'https://fonts.gstatic.com/s/poppins/v20/pxiEyp8kv8JHgFVrJJfecg.woff2',
			'https://fonts.gstatic.com/s/poppins/v20/pxiByp8kv8JHgFVrLCz7Z1xlFQ.woff2',
		),
		'font_families'            => array(
			'Poppins' => array(
				'weights' => array( 300, 400, 500, 600, 700, 800, 900 ),
				'styles'  => array( 'normal', 'italic' ),
			),
			'Figtree' => array(
				'weights' => array( 300, 400, 500, 600, 700, 800, 900 ),
				'styles'  => array( 'normal', 'italic' ),
			),
		),
	);

	/**
	 * JavaScript optimization settings
	 */
	const JS_SETTINGS = array(
		'enable_defer'        => true,
		'enable_minification' => true,
		'combine_files'       => false, // Set to true for production.
		'exclude_from_defer'  => array(
			'jquery',
			'wp-includes',
		),
	);

	/**
	 * Image optimization settings
	 */
	const IMAGE_SETTINGS = array(
		'enable_lazy_loading'      => true,
		'enable_webp_conversion'   => true,
		'quality_jpeg'             => 85,
		'quality_webp'             => 80,
		'enable_responsive_images' => true,
		'breakpoints'              => array( 320, 768, 1024, 1200, 1920 ),
	);

	/**
	 * Caching settings
	 */
	const CACHE_SETTINGS = array(
		'enable_browser_cache' => true,
		'css_cache_duration'   => 31536000, // 1 year.
		'js_cache_duration'    => 31536000,  // 1 year.
		'image_cache_duration' => 2592000, // 30 days.
		'enable_gzip'          => true,
		'enable_etags'         => true,
	);

	/**
	 * Development settings
	 */
	const DEV_SETTINGS = array(
		'disable_in_debug'         => array(
			'css_minification',
			'js_minification',
			'css_defer',
		),
		'enable_debug_info'        => true,
		'show_performance_metrics' => true,
	);

	/**
	 * Security optimizations
	 */
	const SECURITY_SETTINGS = array(
		'disable_wp_generator' => true,
		'disable_xml_rpc'      => true,
		'disable_wp_emoji'     => true,
		'remove_query_strings' => true,
		'hide_wp_version'      => true,
		'disable_file_editing' => true,
	);

	/**
	 * Get setting value
	 *
	 * @param string $category Setting category.
	 * @param string $key Setting key.
	 * @param mixed  $default_value Default value.
	 * @return mixed Setting value.
	 */
	public static function get( $category, $key = null, $default_value = null ) {
		$category_const = strtoupper( $category ) . '_SETTINGS';

		if ( ! defined( 'self::' . $category_const ) ) {
			return $default_value;
		}

		$settings = constant( 'self::' . $category_const );

		if ( null === $key ) {
			return $settings;
		}

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default_value;
	}

	/**
	 * Check if optimization is enabled
	 *
	 * @param string $optimization Optimization name.
	 * @return bool True if enabled.
	 */
	public static function is_enabled( $optimization ) {
		// Disable certain optimizations in debug mode.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$disabled_in_debug = self::get( 'dev', 'disable_in_debug', array() );
			if ( in_array( $optimization, $disabled_in_debug, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get performance metrics
	 *
	 * @return array Performance data
	 */
	public static function get_metrics() {
		return array(
			'css_optimization'  => array(
				'minification_enabled' => self::get( 'css', 'enable_minification' ),
				'critical_css_enabled' => self::get( 'css', 'enable_critical_css' ),
				'defer_enabled'        => self::get( 'css', 'enable_css_defer' ),
				'conditional_loading'  => self::get( 'css', 'enable_conditional_loading' ),
			),
			'font_optimization' => array(
				'preload_enabled'      => self::get( 'font', 'enable_font_preload' ),
				'display_swap'         => self::get( 'font', 'enable_font_display_swap' ),
				'critical_fonts_count' => count( self::get( 'font', 'critical_fonts', array() ) ),
			),
			'security'          => array(
				'xml_rpc_disabled'      => self::get( 'security', 'disable_xml_rpc' ),
				'wp_generator_disabled' => self::get( 'security', 'disable_wp_generator' ),
				'query_strings_removed' => self::get( 'security', 'remove_query_strings' ),
			),
		);
	}
}
