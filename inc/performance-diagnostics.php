<?php
/**
 * Performance Diagnostics and Monitoring
 * Marcello Scavo Tattoo Theme
 *
 * @package MarcelloScavoTattoo
 * @subpackage Performance
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Performance Diagnostics Class
 */
class MarcelloScavoPerformanceDiagnostics {




	/**
	 * Start time for performance measurement.
	 *
	 * @var float
	 */
	private static $start_time;

	/**
	 * Number of queries at start.
	 *
	 * @var int
	 */
	private static $queries_start;

	/**
	 * Memory usage at start.
	 *
	 * @var int
	 */
	private static $memory_start;

	/**
	 * Initialize performance monitoring
	 */
	public static function init() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			add_action( 'wp_head', array( __CLASS__, 'start_monitoring' ), 1 );
			add_action( 'wp_footer', array( __CLASS__, 'display_metrics' ), 999 );
			add_action( 'admin_bar_menu', array( __CLASS__, 'admin_bar_metrics' ), 999 );
		}
	}

	/**
	 * Start performance monitoring
	 */
	public static function start_monitoring() {
		self::$start_time    = microtime( true );
		self::$queries_start = get_num_queries();
		self::$memory_start  = memory_get_usage();
	}

	/**
	 * Calculate performance metrics
	 *
	 * @return array Performance metrics
	 */
	public static function get_metrics() {
		$load_time    = microtime( true ) - ( self::$start_time ? self::$start_time : microtime( true ) );
		$queries      = get_num_queries() - ( self::$queries_start ? self::$queries_start : 0 );
		$memory_usage = memory_get_usage() - ( self::$memory_start ? self::$memory_start : 0 );
		$memory_peak  = memory_get_peak_usage( true );

		return array(
			'load_time'           => round( $load_time, 3 ),
			'queries'             => $queries,
			'memory_usage'        => self::format_bytes( $memory_usage ),
			'memory_peak'         => self::format_bytes( $memory_peak ),
			'template'            => self::get_current_template(),
			'css_files'           => self::get_enqueued_styles(),
			'js_files'            => self::get_enqueued_scripts(),
			'optimization_status' => self::get_optimization_status(),
		);
	}

	/**
	 * Display performance metrics in footer (debug mode only)
	 */
	public static function display_metrics() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$metrics = self::get_metrics();

		echo '<div id="marcello-performance-metrics" style="
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0,0,0,0.9);
            color: #fff;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            z-index: 9999;
            max-width: 300px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        ">';

		echo '<h4 style="margin: 0 0 10px 0; color: #d4af37;">Performance Metrics</h4>';
		echo '<div><strong>Load Time:</strong> ' . esc_html( $metrics['load_time'] ) . 's</div>';
		echo '<div><strong>DB Queries:</strong> ' . esc_html( $metrics['queries'] ) . '</div>';
		echo '<div><strong>Memory:</strong> ' . esc_html( $metrics['memory_usage'] ) . '</div>';
		echo '<div><strong>Peak Memory:</strong> ' . esc_html( $metrics['memory_peak'] ) . '</div>';
		echo '<div><strong>Template:</strong> ' . esc_html( basename( $metrics['template'] ) ) . '</div>';
		echo '<div><strong>CSS Files:</strong> ' . count( $metrics['css_files'] ) . '</div>';
		echo '<div><strong>JS Files:</strong> ' . count( $metrics['js_files'] ) . '</div>';

		echo '<div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #333;">';
		echo '<strong>Optimizations:</strong><br>';
		foreach ( $metrics['optimization_status'] as $opt => $status ) {
			$color = $status ? '#4CAF50' : '#f44336';
			echo '<span style="color: ' . esc_attr( $color ) . ';">• ' . esc_html( ucfirst( str_replace( '_', ' ', $opt ) ) ) . '</span><br>';
		}
		echo '</div>';

		echo '</div>';

		// Add close button.
		echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                const metrics = document.getElementById("marcello-performance-metrics");
                metrics.addEventListener("dblclick", function() {
                    this.style.display = "none";
                });
                metrics.title = "Double-click to hide";
            });
        </script>';
	}

	/**
	 * Add metrics to admin bar
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The WordPress admin bar object.
	 */
	public static function admin_bar_metrics( $wp_admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$metrics = self::get_metrics();

		$wp_admin_bar->add_node(
			array(
				'id'    => 'marcello-performance',
				'title' => '⚡ ' . $metrics['load_time'] . 's | ' . $metrics['queries'] . 'q',
				'href'  => '#',
				'meta'  => array(
					'title' => 'Page Load: ' . $metrics['load_time'] . 's, DB Queries: ' . $metrics['queries'],
				),
			)
		);

		$wp_admin_bar->add_node(
			array(
				'parent' => 'marcello-performance',
				'id'     => 'performance-details',
				'title'  => 'Memory: ' . $metrics['memory_peak'] . ' | Template: ' . basename( $metrics['template'] ),
			)
		);
	}

	/**
	 * Get current template file
	 *
	 * @return string Template file path
	 */
	private static function get_current_template() {
		global $template;
		return $template ? $template : 'Unknown';
	}

	/**
	 * Get enqueued stylesheets
	 *
	 * @return array Enqueued styles
	 */
	private static function get_enqueued_styles() {
		global $wp_styles;
		return $wp_styles->queue ?? array();
	}

	/**
	 * Get enqueued scripts
	 *
	 * @return array Enqueued scripts
	 */
	private static function get_enqueued_scripts() {
		global $wp_scripts;
		return $wp_scripts->queue ?? array();
	}

	/**
	 * Get optimization status
	 *
	 * @return array Optimization statuses
	 */
	private static function get_optimization_status() {
		$critical_css_file = get_template_directory() . '/assets/css/critical.css';
		$minified_dir      = get_template_directory() . '/assets/css/min';

		return array(
			'critical_css'     => file_exists( $critical_css_file ),
			'css_minification' => is_dir( $minified_dir ),
			'font_preload'     => strpos( marcello_scavo_capture_wp_head_output(), 'rel="preload"' ) !== false,
			'cache_busting'    => true, // Always enabled with filemtime().
			'gzip_compression' => function_exists( 'gzencode' ),
			'wp_debug'         => defined( 'WP_DEBUG' ) && WP_DEBUG,
		);
	}

	/**
	 * Format bytes to human readable format
	 *
	 * @param int $bytes The number of bytes to format.
	 * @return string Formatted bytes string.
	 */
	private static function format_bytes( $bytes ) {
		if ( $bytes >= 1073741824 ) {
			return number_format( $bytes / 1073741824, 2 ) . ' GB';
		} elseif ( $bytes >= 1048576 ) {
			return number_format( $bytes / 1048576, 2 ) . ' MB';
		} elseif ( $bytes >= 1024 ) {
			return number_format( $bytes / 1024, 2 ) . ' KB';
		} else {
			return $bytes . ' bytes';
		}
	}

	/**
	 * Generate performance report
	 *
	 * @return string HTML report
	 */
	public static function generate_report() {
		$metrics       = self::get_metrics();
		$optimizations = marcello_scavo_get_performance_metrics();

		$report  = '<div class="performance-report">';
		$report .= '<h3>Performance Report - ' . gmdate( 'Y-m-d H:i:s' ) . '</h3>';

		$report .= '<div class="metrics-grid">';
		$report .= '<div class="metric-card">';
		$report .= '<h4>Load Performance</h4>';
		$report .= '<p>Load Time: <strong>' . $metrics['load_time'] . 's</strong></p>';
		$report .= '<p>DB Queries: <strong>' . $metrics['queries'] . '</strong></p>';
		$report .= '<p>Memory Peak: <strong>' . $metrics['memory_peak'] . '</strong></p>';
		$report .= '</div>';

		$report .= '<div class="metric-card">';
		$report .= '<h4>Asset Loading</h4>';
		$report .= '<p>CSS Files: <strong>' . count( $metrics['css_files'] ) . '</strong></p>';
		$report .= '<p>JS Files: <strong>' . count( $metrics['js_files'] ) . '</strong></p>';
		$report .= '<p>Template: <strong>' . basename( $metrics['template'] ) . '</strong></p>';
		$report .= '</div>';

		$report .= '<div class="metric-card">';
		$report .= '<h4>Optimizations</h4>';
		foreach ( $optimizations as $category => $settings ) {
			$report .= '<p>' . ucfirst( $category ) . ': ';
			$enabled = array_filter( $settings );
			$report .= '<strong>' . count( $enabled ) . '/' . count( $settings ) . ' enabled</strong></p>';
		}
		$report .= '</div>';
		$report .= '</div>';

		$report .= '</div>';

		return $report;
	}
}

// Initialize diagnostics.
MarcelloScavoPerformanceDiagnostics::init();
