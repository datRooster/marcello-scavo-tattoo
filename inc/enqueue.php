<?php
/**
 * Theme Assets Enqueue Functions
 *
 * @package MarcelloScavoTattoo
 * @subpackage Assets
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue scripts and styles
 */
function marcello_scavo_scripts() {
	// Get file modification time for cache busting.
	$style_version = file_exists( get_template_directory() . '/style.css' )
		? filemtime( get_template_directory() . '/style.css' )
		: '1.0.0';
	$js_version    = file_exists( get_template_directory() . '/assets/js/main.js' )
		? filemtime( get_template_directory() . '/assets/js/main.js' )
		: '1.0.0';

	// Inline critical CSS for above-the-fold content.
	$critical_css_file = get_template_directory() . '/assets/css/critical.css';
	if ( file_exists( $critical_css_file ) ) {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}
		$critical_css = $wp_filesystem->get_contents( $critical_css_file );
		if ( $critical_css ) {
			wp_add_inline_style( 'wp-block-library', $critical_css );
		}
	}

	// Enqueue CSS with cache busting (deferred for non-critical styles).
	wp_enqueue_style( 'marcello-scavo-style', get_stylesheet_uri(), array(), $style_version );

	// Additional components CSS.
	if ( file_exists( get_template_directory() . '/assets/css/components.css' ) ) {
		$components_version = filemtime( get_template_directory() . '/assets/css/components.css' );
		wp_enqueue_style( 'marcello-scavo-components', get_template_directory_uri() . '/assets/css/components.css', array( 'marcello-scavo-style' ), $components_version );
	}

	// Conditional CSS loading for better performance.
	if ( is_singular( 'portfolio' ) || is_tax( 'portfolio_category' ) ) {
		wp_enqueue_style( 'marcello-scavo-portfolio', get_template_directory_uri() . '/assets/css/portfolio.css', array( 'marcello-scavo-style' ), $style_version );
	}

	if ( is_singular( 'gallery' ) || is_tax( 'gallery_category' ) ) {
		wp_enqueue_style( 'marcello-scavo-gallery', get_template_directory_uri() . '/assets/css/gallery.css', array( 'marcello-scavo-style' ), $style_version );
	}

	// Google Fonts (updated for Poppins + Figtree).
	wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Figtree:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap', array(), '2024.1' );

	// Font Awesome - Single CDN source (removed backup for performance).
	wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0' );

	// Add preload for Font Awesome.
	add_filter(
		'style_loader_tag',
		function ( $html, $handle ) {
			if ( 'font-awesome' === $handle ) {
				$html = str_replace( "rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\" crossorigin='anonymous'", $html );
			}
			return $html;
		},
		10,
		2
	);

	// Add noscript fallback for Font Awesome via wp_head.
	add_action(
		'wp_head',
		function () {
			echo '<noscript><style>@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css");</style></noscript>' . "\n";
		}
	);

	// Enqueue JavaScript with cache busting.
	wp_enqueue_script( 'marcello-scavo-script', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), $js_version, true );

	// Enqueue Portfolio Slider JavaScript.
	if ( file_exists( get_template_directory() . '/assets/js/portfolio-slider.js' ) ) {
		$slider_version = filemtime( get_template_directory() . '/assets/js/portfolio-slider.js' );
		wp_enqueue_script( 'portfolio-slider', get_template_directory_uri() . '/assets/js/portfolio-slider.js', array( 'jquery' ), $slider_version, true );
	}

	// Enqueue Instagram Feed JavaScript.
	if ( file_exists( get_template_directory() . '/assets/js/instagram-feed.js' ) ) {
		$instagram_version = filemtime( get_template_directory() . '/assets/js/instagram-feed.js' );
		wp_enqueue_script( 'instagram-feed', get_template_directory_uri() . '/assets/js/instagram-feed.js', array( 'jquery' ), $instagram_version, true );
	}

	// Localize script for AJAX.
	wp_localize_script(
		'marcello-scavo-script',
		'ajax_object',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'marcello_scavo_nonce' ),
		)
	);

	// Font Awesome fallback check (optimized).
	wp_add_inline_script(
		'marcello-scavo-script',
		'
        jQuery(document).ready(function($) {
            // Check if Font Awesome has loaded after a short delay.
            setTimeout(function() {
                var testIcon = $("<i class=\"fas fa-paint-brush\" style=\"position:absolute;top:-9999px;left:-9999px;\"></i>");
                $("body").append(testIcon);
                
                // Check if the icon has the expected font-family.
                var fontFamily = testIcon.css("font-family");
                var hasFontAwesome = fontFamily && (
                    fontFamily.indexOf("Font Awesome") !== -1 || 
                    fontFamily.indexOf("FontAwesome") !== -1
                );
                
                testIcon.remove();
                
                if (!hasFontAwesome) {
                    console.log("Font Awesome not detected, applying fallbacks");
                    $("body").addClass("font-awesome-fallback");
                    
                    // Apply specific fallbacks.
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
    '
	);
}
add_action( 'wp_enqueue_scripts', 'marcello_scavo_scripts' );

/**
 * Add version parameter to all theme CSS files to prevent caching
 *
 * @param string $src    The source URL of the enqueued style.
 * @param string $handle The style's registered handle.
 * @return string Modified source URL with version parameter.
 */
function marcello_scavo_add_css_version( $src, $handle ) {
	// Only add version to theme-specific styles.
	$theme_handles = array( 'marcello-scavo-style', 'marcello-scavo-components', 'marcello-scavo-portfolio', 'marcello-scavo-gallery' );

	if ( in_array( $handle, $theme_handles, true ) && strpos( $src, get_template_directory_uri() ) !== false ) {
		// Use filemtime instead of time() for better performance.
		$file_path = str_replace( get_template_directory_uri(), get_template_directory(), $src );
		if ( file_exists( $file_path ) ) {
			$version = filemtime( $file_path );
			$src     = add_query_arg( 'v', $version, $src );
		}
	}
	return $src;
}
add_filter( 'style_loader_src', 'marcello_scavo_add_css_version', 9999, 2 );
