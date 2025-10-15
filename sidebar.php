<?php
/**
 * The sidebar containing the main widget area
 *
 * @package MarcelloScavoTattoo
 */

if ( ! is_active_sidebar( 'primary-sidebar' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area sidebar">
	<div class="sidebar-content">
		<?php dynamic_sidebar( 'primary-sidebar' ); ?>
	</div>
</aside><!-- #secondary -->
