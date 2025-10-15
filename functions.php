<?php
/**
 * Marcello Scavo Tattoo Theme Functions - Modular Architecture
 *
 * @package MarcelloScavoTattoo
 * @version 2.0.0
 * @description Modular functions file with organized includes
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define theme constants with proper prefixes.
 */
define( 'MARCELLO_SCAVO_THEME_VERSION', '2.0.0' );
define( 'MARCELLO_SCAVO_THEME_DIR', get_template_directory() );
define( 'MARCELLO_SCAVO_THEME_URI', get_template_directory_uri() );

/**
 * Load theme modules
 * Each module handles a specific aspect of the theme
 */

// Core theme setup and configuration.
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/setup.php';

// Assets management (CSS, JS, fonts).
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/enqueue.php';

// Performance configuration.
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/performance-config.php';

// Performance helper functions.
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/performance-helpers.php';

// CSS optimization and performance.
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/css-optimization.php';

// Performance diagnostics (debug mode only).
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/performance-diagnostics.php';

// Performance diagnostics helper functions.
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/performance-diagnostics-helpers.php';

// Custom post types and taxonomies.
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/post-types.php';

// Widget areas registration.
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/widgets.php';

// Custom meta boxes and fields.
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/meta-boxes.php';

// Bookly integration and AJAX functions.
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/ajax-functions.php';

// Theme customizer configuration.
require_once MARCELLO_SCAVO_THEME_DIR . '/inc/customizer.php';

/**
 * Additional theme utilities and helper functions
 */

/**
 * Get theme version for cache busting
 */
function marcello_scavo_get_theme_version() {
	return MARCELLO_SCAVO_THEME_VERSION;
}

/**
 * Check if we're in development mode
 */
function marcello_scavo_is_development() {
	return defined( 'WP_DEBUG' ) && WP_DEBUG;
}

/**
 * Get optimized asset version
 * Uses file modification time in development, theme version in production
 *
 * @param string $file_path Path to the asset file.
 * @return string Asset version.
 */
function marcello_scavo_get_asset_version( $file_path ) {
	if ( marcello_scavo_is_development() && file_exists( $file_path ) ) {
		return filemtime( $file_path );
	}
	return MARCELLO_SCAVO_THEME_VERSION;
}

/**
 * Theme activation hooks
 */
function marcello_scavo_theme_activation() {
	// Flush rewrite rules on theme activation.
	flush_rewrite_rules();

	// Set default theme options.
	$default_options = array(
		'hero_label'       => 'L\'ARTE DEL TATTOO',
		'hero_title'       => 'Scopri l\'essenza dei miei tatuaggi e opere d\'arte.',
		'hero_button_text' => 'Esplora Ora',
		'whatsapp_number'  => '393331234567',
		'whatsapp_message' => 'Ciao! Vorrei prenotare una consulenza per un tatuaggio.',
	);

	foreach ( $default_options as $option => $value ) {
		if ( ! get_theme_mod( $option ) ) {
			set_theme_mod( $option, $value );
		}
	}
}
add_action( 'after_switch_theme', 'marcello_scavo_theme_activation' );

/**
 * Theme deactivation cleanup
 */
function marcello_scavo_theme_deactivation() {
	// Flush rewrite rules on theme deactivation.
	flush_rewrite_rules();
}
add_action( 'switch_theme', 'marcello_scavo_theme_deactivation' );

/**
 * Admin notices for missing dependencies
 */
function marcello_scavo_admin_notices() {
	// Check for recommended plugins.
	$missing_plugins = array();

	if ( ! is_plugin_active( 'bookly-responsive-appointment-booking-tool/main.php' ) ) {
		$missing_plugins[] = 'Bookly Booking Plugin';
	}

	if ( ! empty( $missing_plugins ) && current_user_can( 'install_plugins' ) ) {
		echo '<div class="notice notice-warning is-dismissible">';
		echo '<h3>Marcello Scavo Tattoo Theme</h3>';
		echo '<p>Per sfruttare al meglio tutte le funzionalità del tema, si consiglia di installare i seguenti plugin:</p>';
		echo '<ul>';
		foreach ( $missing_plugins as $plugin ) {
			echo '<li>• ' . esc_html( $plugin ) . '</li>';
		}
		echo '</ul>';
		echo '<p><a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">Gestisci Plugin</a></p>';
		echo '</div>';
	}
}
add_action( 'admin_notices', 'marcello_scavo_admin_notices' );

/**
 * Performance optimizations
 */

// Remove emoji scripts for better performance.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// Remove WordPress version from head for security.
remove_action( 'wp_head', 'wp_generator' );

// Disable XML-RPC for security (if not needed).
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Remove unnecessary query strings from static assets
 *
 * @param string $src Asset source URL.
 * @return string Modified source URL.
 */
function marcello_scavo_remove_query_strings( $src ) {
	if ( strpos( $src, '?ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'script_loader_src', 'marcello_scavo_remove_query_strings', 15 );
add_filter( 'style_loader_src', 'marcello_scavo_remove_query_strings', 15 );

/**
 * Security enhancements
 */

/**
 * Hide sensitive information in error messages
 *
 * @return string Generic error message.
 */
function marcello_scavo_hide_wp_errors() {
	return 'Errore di connessione al database. Contatta l\'amministratore del sito.';
}
if ( ! marcello_scavo_is_development() ) {
	add_filter( 'wp_die_handler', 'marcello_scavo_hide_wp_errors' );
}

// Disable file editing from admin.
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/**
 * SEO and Performance helpers
 */

/**
 * Add preload hints for critical resources
 */
function marcello_scavo_add_preload_hints() {
	// Preload critical fonts.
	echo '<link rel="preload" href="' . esc_url( MARCELLO_SCAVO_THEME_URI . '/assets/fonts/poppins-v20-latin-regular.woff2' ) . '" as="font" type="font/woff2" crossorigin>';

	// DNS prefetch for external resources.
	echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">';
	echo '<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">';
}
add_action( 'wp_head', 'marcello_scavo_add_preload_hints', 1 );

/**
 * Debug and development helpers
 */
if ( marcello_scavo_is_development() ) {
	/**
	 * Show template hierarchy in HTML comments
	 */
	function marcello_scavo_show_template() {
		if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
			global $template;
			echo "\n<!-- Template: " . esc_html( basename( $template ) ) . " -->\n";
		}
	}
	add_action( 'wp_footer', 'marcello_scavo_show_template' );

	/**
	 * Add development toolbar info
	 */
	function marcello_scavo_dev_info() {
		if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
			echo '<div style="position:fixed;bottom:0;right:0;background:#333;color:#fff;padding:5px 10px;font-size:12px;z-index:9999;">';
			echo 'Theme: v' . esc_html( MARCELLO_SCAVO_THEME_VERSION ) . ' | ';
			echo 'Template: ' . esc_html( basename( get_page_template() ) ) . ' | ';
			echo 'Queries: ' . esc_html( get_num_queries() ) . ' | ';
			echo 'Time: ' . esc_html( timer_stop() ) . 's';
			echo '</div>';
		}
	}
	add_action( 'wp_footer', 'marcello_scavo_dev_info' );
}

/**
 * Compatibility and fallbacks
 */

// Ensure compatibility with older WordPress versions.
if ( ! function_exists( 'wp_body_open' ) ) {
	/**
	 * WordPress core fallback for wp_body_open hook.
	 * Fire the wp_body_open action.
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

// Fallback for missing translation functions in CLI context.
if ( ! function_exists( '__' ) ) {
	/**
	 * WordPress core fallback for translation function.
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Text domain.
	 * @return string Translated text.
	 */
	function __( $text, $domain = 'default' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		return $text;
	}
}

if ( ! function_exists( '_e' ) ) {
	/**
	 * WordPress core fallback for echo translation function.
	 *
	 * @param string $text   Text to translate and echo.
	 * @param string $domain Text domain.
	 */
	function _e( $text, $domain = 'default' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		echo esc_html( $text );
	}
}

/**
 * Theme info for admin
 */
function marcello_scavo_add_theme_info() {
	echo '<div class="notice notice-info" style="border-left-color: #c9b05f;">';
	echo '<h3>🎨 Marcello Scavo Tattoo Theme</h3>';
	echo '<p><strong>Versione:</strong> ' . esc_html( MARCELLO_SCAVO_THEME_VERSION ) . ' | ';
	echo '<strong>Architettura:</strong> Modulare | ';
	echo '<strong>Performance:</strong> Ottimizzato</p>';
	echo '<p>Tema sviluppato con architettura modulare per ottimali prestazioni e manutenibilità.</p>';
	echo '</div>';
}

// Show theme info only once after activation.
if ( get_transient( 'marcello_scavo_theme_activated' ) ) {
	add_action( 'admin_notices', 'marcello_scavo_add_theme_info' );
	delete_transient( 'marcello_scavo_theme_activated' );
}

/**
 * Set activation flag for one-time theme info display
 */
function marcello_scavo_set_activation_flag() {
	set_transient( 'marcello_scavo_theme_activated', true, 30 );
}
add_action( 'after_switch_theme', 'marcello_scavo_set_activation_flag' );
