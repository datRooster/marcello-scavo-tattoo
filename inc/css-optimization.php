<?php
/**
 * CSS Optimization and Minification Functions
 * Marcello Scavo Tattoo Theme
 *
 * @package MarcelloScavoTattoo
 * @subpackage CSS
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Minify CSS content
 *
 * @param string $css CSS content to minify.
 * @return string Minified CSS
 */
function marcello_scavo_minify_css( $css ) {
	// Remove comments.
	$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );

	// Remove unnecessary whitespace.
	$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

	// Remove whitespace around specific characters.
	$css = str_replace( array( ' {', '{ ', ' }', '} ', '; ', ' ;', ': ', ' :', ', ', ' ,' ), array( '{', '{', '}', '}', ';', ';', ':', ':', ',', ',' ), $css );

	// Remove trailing semicolon before closing brace.
	$css = str_replace( ';}', '}', $css );

	return trim( $css );
}

/**
 * Generate minified CSS file if it doesn't exist or source is newer
 *
 * @param string $source_file Source CSS file path.
 * @param string $minified_file Minified CSS file path.
 * @return bool True if minified file was created/updated
 */
function marcello_scavo_create_minified_css( $source_file, $minified_file ) {
	// Check if source file exists.
	if ( ! file_exists( $source_file ) ) {
		return false;
	}

	// Check if minified file needs to be created/updated.
	if ( ! file_exists( $minified_file ) || filemtime( $source_file ) > filemtime( $minified_file ) ) {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$css_content  = $wp_filesystem->get_contents( $source_file );
		$minified_css = marcello_scavo_minify_css( $css_content );

		// Create directory if it doesn't exist.
		$minified_dir = dirname( $minified_file );
		if ( ! is_dir( $minified_dir ) ) {
			wp_mkdir_p( $minified_dir );
		}

		// Write minified CSS.
		return $wp_filesystem->put_contents( $minified_file, $minified_css );
	}

	return true;
}

/**
 * Get optimized CSS file URL
 *
 * @param string $css_file Relative path to CSS file.
 * @return array Array with URL and version
 */
function marcello_scavo_get_optimized_css( $css_file ) {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	$source_file   = $theme_dir . '/' . $css_file;
	$minified_file = $theme_dir . '/assets/css/min/' . basename( $css_file, '.css' ) . '.min.css';
	$minified_url  = $theme_uri . '/assets/css/min/' . basename( $css_file, '.css' ) . '.min.css';

	// Create/update minified version if needed.
	if ( marcello_scavo_create_minified_css( $source_file, $minified_file ) ) {
		return array(
			'url'     => $minified_url,
			'version' => filemtime( $minified_file ),
		);
	}

	// Fallback to original file.
	return array(
		'url'     => $theme_uri . '/' . $css_file,
		'version' => file_exists( $source_file ) ? filemtime( $source_file ) : '1.0.0',
	);
}

/**
 * Optimize and combine critical CSS files
 *
 * @return string Combined and minified critical CSS
 */
function marcello_scavo_get_critical_css() {
	$theme_dir      = get_template_directory();
	$critical_files = array(
		'/assets/css/critical.css',
	);

	$combined_css = '';

	foreach ( $critical_files as $file ) {
		$file_path = $theme_dir . $file;
		if ( file_exists( $file_path ) ) {
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				WP_Filesystem();
			}
			$css_content   = $wp_filesystem->get_contents( $file_path );
			$combined_css .= $css_content . "\n";
		}
	}

	return marcello_scavo_minify_css( $combined_css );
}

/**
 * Add cache headers for CSS files
 */
function marcello_scavo_add_css_cache_headers() {
	if ( is_admin() ) {
		return;
	}

	// Add cache headers for CSS files.
	add_action(
		'wp_enqueue_scripts',
		function () {
			// Set cache headers via header if possible.
			if ( ! headers_sent() ) {
				$current_url = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
				if ( strpos( $current_url, '.css' ) !== false ) {
					header( 'Cache-Control: public, max-age=31536000' ); // 1 year.
					header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 31536000 ) . ' GMT' );
				}
			}
		},
		1
	);
}

// Initialize CSS optimization.
add_action( 'init', 'marcello_scavo_add_css_cache_headers' );

/**
 * Preload critical resources
 */
function marcello_scavo_preload_resources() {
	if ( is_admin() ) {
		return;
	}

	// Preload critical fonts.
	echo '<link rel="preload" href="https://fonts.gstatic.com/s/poppins/v20/pxiEyp8kv8JHgFVrJJfecg.woff2" as="font" type="font/woff2" crossorigin>' . "\n";
	echo '<link rel="preload" href="https://fonts.gstatic.com/s/poppins/v20/pxiByp8kv8JHgFVrLCz7Z1xlFQ.woff2" as="font" type="font/woff2" crossorigin>' . "\n";

	// Preload critical CSS.
	$critical_css_file = get_template_directory() . '/assets/css/critical.css';
	if ( file_exists( $critical_css_file ) ) {
		echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() ) . '/assets/css/critical.css" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
	}
}

add_action( 'wp_head', 'marcello_scavo_preload_resources', 1 );

/**
 * Defer non-critical CSS
 *
 * @param string $html Style tag HTML.
 * @param string $handle Style handle.
 * @return string Modified HTML
 */
function marcello_scavo_defer_css( $html, $handle ) {
	// Don't defer critical styles.
	$critical_handles = array( 'wp-block-library', 'marcello-scavo-critical' );

	if ( in_array( $handle, $critical_handles, true ) ) {
		return $html;
	}

	// Defer non-critical CSS.
	if ( strpos( $html, 'media=' ) === false ) {
		$html = str_replace( 'rel="stylesheet"', 'rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', $html );
		// Add noscript fallback.
		$html .= '<noscript>' . str_replace( 'rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', 'rel="stylesheet"', $html ) . '</noscript>';
	}

	return $html;
}

// Apply CSS deferring in production.
if ( ! WP_DEBUG ) {
	add_filter( 'style_loader_tag', 'marcello_scavo_defer_css', 10, 2 );
}
