<?php
/**
 * Widget Areas Registration
 *
 * @package MarcelloScavoTattoo
 * @subpackage Widgets
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Widget Areas
 */
function marcello_scavo_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar Principale', 'marcello-scavo-tattoo' ),
			'id'            => 'primary-sidebar',
			'description'   => __( 'Aggiungi widget qui per apparire nella sidebar.', 'marcello-scavo-tattoo' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer Colonna 1', 'marcello-scavo-tattoo' ),
			'id'            => 'footer-1',
			'description'   => __( 'Widget per la prima colonna del footer.', 'marcello-scavo-tattoo' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer Colonna 2', 'marcello-scavo-tattoo' ),
			'id'            => 'footer-2',
			'description'   => __( 'Widget per la seconda colonna del footer.', 'marcello-scavo-tattoo' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer Colonna 3', 'marcello-scavo-tattoo' ),
			'id'            => 'footer-3',
			'description'   => __( 'Widget per la terza colonna del footer.', 'marcello-scavo-tattoo' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Social Media', 'marcello-scavo-tattoo' ),
			'id'            => 'social-media',
			'description'   => __( 'Area widget per contenuti social media (Instagram feed, etc.).', 'marcello-scavo-tattoo' ),
			'before_widget' => '<div id="%1$s" class="widget social-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title social-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Recensioni Clienti', 'marcello-scavo-tattoo' ),
			'id'            => 'reviews-section',
			'description'   => __( 'Area widget per recensioni Google e testimonianze clienti.', 'marcello-scavo-tattoo' ),
			'before_widget' => '<div id="%1$s" class="widget reviews-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title reviews-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Prenotazione Booking', 'marcello-scavo-tattoo' ),
			'id'            => 'booking-section',
			'description'   => __( 'Area widget per sistemi di prenotazione (Bookly, form contatti, etc.).', 'marcello-scavo-tattoo' ),
			'before_widget' => '<div id="%1$s" class="widget booking-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title booking-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Galleria Artistica', 'marcello-scavo-tattoo' ),
			'id'            => 'gallery-showcase',
			'description'   => __( 'Area widget per mostrare la galleria dei lavori artistici e tatuaggi.', 'marcello-scavo-tattoo' ),
			'before_widget' => '<div id="%1$s" class="widget gallery-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title gallery-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Uploader Sicuro', 'marcello-scavo-tattoo' ),
			'id'            => 'secure-uploader',
			'description'   => __( 'Area widget per il caricamento sicuro di immagini nei media WordPress.', 'marcello-scavo-tattoo' ),
			'before_widget' => '<div id="%1$s" class="widget uploader-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title uploader-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( '📧 Sezione Contatti', 'marcello-scavo-tattoo' ),
			'id'            => 'contact-section',
			'description'   => __( 'Area widget per personalizzare la sezione "Contattaci per informazioni". Include contenuto di fallback accattivante.', 'marcello-scavo-tattoo' ),
			'before_widget' => '<div id="%1$s" class="widget contact-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="contact-widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( '🗺️ Mappa Localizzazione', 'marcello-scavo-tattoo' ),
			'id'            => 'location-map',
			'description'   => __( 'Area widget per personalizzare la mappa di localizzazione aziendale. Supporta Google Maps, OpenStreetMap e mappe personalizzate.', 'marcello-scavo-tattoo' ),
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		)
	);
}
add_action( 'widgets_init', 'marcello_scavo_widgets_init' );
