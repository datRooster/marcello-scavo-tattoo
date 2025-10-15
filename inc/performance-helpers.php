<?php
/**
 * Performance Helper Functions
 * Marcello Scavo Tattoo Theme
 *
 * Quick access functions for performance settings
 *
 * @package MarcelloScavoTattoo
 * @subpackage Performance
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Quick access functions
 *
 * @param string $category     Setting category.
 * @param string $key          Setting key.
 * @param mixed  $default_value Default value.
 * @return mixed Setting value.
 */
function marcello_scavo_get_performance_setting( $category, $key = null, $default_value = null ) {
	return MarcelloScavoPerformance::get( $category, $key, $default_value );
}

/**
 * Check if optimization is enabled.
 *
 * @param string $optimization Optimization name.
 * @return bool True if enabled.
 */
function marcello_scavo_optimization_enabled( $optimization ) {
	return MarcelloScavoPerformance::is_enabled( $optimization );
}

/**
 * Get performance metrics.
 *
 * @return array Performance data.
 */
function marcello_scavo_get_performance_metrics() {
	return MarcelloScavoPerformance::get_metrics();
}
