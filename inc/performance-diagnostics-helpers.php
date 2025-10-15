<?php
/**
 * Performance Helper Functions
 * Marcello Scavo Tattoo Theme
 *
 * Helper functions for performance diagnostics
 *
 * @package MarcelloScavoTattoo
 * @subpackage Performance
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Capture wp_head output for analysis
 *
 * @return string The captured wp_head output.
 */
function marcello_scavo_capture_wp_head_output() {
	ob_start();
	wp_head();
	return ob_get_clean();
}
