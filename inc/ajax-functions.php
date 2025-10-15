<?php
/**
 * Bookly Integration and AJAX Functions
 *
 * @package MarcelloScavoTattoo
 * @subpackage Integrations
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Bookly shortcode for booking form
 */
function marcello_scavo_get_bookly_form() {
	// Debug: Check what's available.
	$debug_info = '';
	if ( current_user_can( 'manage_options' ) ) {
		$debug_info = '<!-- Debug Info: 
            bookly_shortcode exists: ' . ( function_exists( 'bookly_shortcode' ) ? 'YES' : 'NO' ) . '
            Bookly class exists: ' . ( class_exists( 'Bookly\Lib\Utils\Common' ) ? 'YES' : 'NO' ) . '
            shortcode registered: ' . ( shortcode_exists( 'bookly-form' ) ? 'YES' : 'NO' ) . '
        -->';
	}

	// Check if Bookly is active by checking if the shortcode function exists.
	if (
		function_exists( 'bookly_shortcode' ) ||
		class_exists( 'Bookly\Lib\Utils\Common' ) ||
		shortcode_exists( 'bookly-form' )
	) {

		// Process and return the Bookly shortcode.
		return $debug_info . do_shortcode( '[bookly-form]' );
	}

	// Alternative check: try to execute the shortcode and see if it returns processed content.
	$bookly_output = do_shortcode( '[bookly-form]' );
	if ( '[bookly-form]' !== $bookly_output ) {
		return $debug_info . $bookly_output;
	}

	// Fallback if Bookly is not active or configured.
	return $debug_info . '<div class="bookly-fallback">
        <h3>' . __( 'Sistema di Prenotazione Non Disponibile', 'marcello-scavo-tattoo' ) . '</h3>
        <p>' . __( 'Il sistema di prenotazione è temporaneamente non disponibile. Contattaci direttamente per prenotare il tuo appuntamento.', 'marcello-scavo-tattoo' ) . '</p>
        <div class="fallback-actions">
            <a href="tel:+393123456789" class="btn btn-primary">
                <i class="fas fa-phone"></i> ' . __( 'Chiama Ora', 'marcello-scavo-tattoo' ) . '
            </a>
            <a href="mailto:info@marcelloscavo.com" class="btn btn-outline-primary">
                <i class="fas fa-envelope"></i> ' . __( 'Scrivi Email', 'marcello-scavo-tattoo' ) . '
            </a>
        </div>
    </div>';
}

/**
 * Bookly custom styles
 */
function marcello_scavo_bookly_styles() {
	// Check if Bookly is active using frontend-compatible methods.
	if (
		function_exists( 'bookly_shortcode' ) ||
		class_exists( 'Bookly\Lib\Utils\Common' ) ||
		shortcode_exists( 'bookly-form' )
	) {
		?>
		<style>
			/* Bookly Custom Styles for Marcello Scavo Theme */

			/* Main Bookly Container */
			.bookly-form-container,
			.bookly-form {
				font-family: var(--font-primary) !important;
			}

			/* Bookly Buttons */
			.bookly-btn,
			.bookly-next-step,
			.bookly-back-step {
				background: linear-gradient(45deg, var(--primary-gold), var(--secondary-gold)) !important;
				border: none !important;
				border-radius: var(--border-radius) !important;
				color: var(--white) !important;
				font-weight: 600 !important;
				padding: var(--spacing-sm) var(--spacing-md) !important;
				transition: all 0.3s ease !important;
			}

			.bookly-btn:hover,
			.bookly-next-step:hover,
			.bookly-back-step:hover {
				transform: translateY(-2px) !important;
				box-shadow: var(--shadow-md) !important;
			}

			/* Bookly Form Fields */
			.bookly-form input[type="text"],
			.bookly-form input[type="email"],
			.bookly-form input[type="tel"],
			.bookly-form select,
			.bookly-form textarea {
				border: 2px solid var(--light-gray) !important;
				border-radius: var(--border-radius) !important;
				padding: var(--spacing-sm) !important;
				font-family: var(--font-primary) !important;
				transition: border-color 0.3s ease !important;
			}

			.bookly-form input:focus,
			.bookly-form select:focus,
			.bookly-form textarea:focus {
				border-color: var(--primary-blue) !important;
				box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1) !important;
				outline: none !important;
			}

			/* Bookly Calendar */
			.bookly-calendar {
				border-radius: var(--border-radius-lg) !important;
				overflow: hidden !important;
				box-shadow: var(--shadow-sm) !important;
			}

			.bookly-calendar .bookly-day.bookly-available:hover {
				background: var(--primary-gold) !important;
				color: var(--white) !important;
			}

			.bookly-calendar .bookly-day.bookly-selected {
				background: var(--primary-blue) !important;
				color: var(--white) !important;
			}

			/* Responsive adjustments */
			@media (max-width: 768px) {
				.bookly-form {
					padding: var(--spacing-md) !important;
				}
			}
		</style>
		<?php
	}
}
add_action( 'wp_head', 'marcello_scavo_bookly_styles' );

/**
 * AJAX Functions
 */

/**
 * Handle contact form submissions
 */
function marcello_scavo_handle_contact_form() {
	// Verify nonce.
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'marcello_scavo_nonce' ) ) {
		wp_die( esc_html__( 'Errore di sicurezza', 'marcello-scavo-tattoo' ) );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	// Validate required fields.
	if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
		wp_send_json_error( array( 'message' => __( 'Tutti i campi sono obbligatori', 'marcello-scavo-tattoo' ) ) );
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Email non valida', 'marcello-scavo-tattoo' ) ) );
	}

	// Prepare email.
	$admin_email = get_option( 'admin_email' );
	/* translators: 1: sender name, 2: email subject */
	$email_subject = sprintf( __( 'Nuovo messaggio da %1$s: %2$s', 'marcello-scavo-tattoo' ), $name, $subject );
	/* translators: 1: sender name, 2: sender email, 3: email subject, 4: message content */
	$email_message = sprintf(
		__( "Hai ricevuto un nuovo messaggio dal sito web:\n\nNome: %1\$s\nEmail: %2\$s\nOggetto: %3\$s\n\nMessaggio:\n%4\$s", 'marcello-scavo-tattoo' ),
		$name,
		$email,
		$subject,
		$message
	);

	$headers = array(
		'From: ' . get_bloginfo( 'name' ) . ' <' . $admin_email . '>',
		'Reply-To: ' . $name . ' <' . $email . '>',
		'Content-Type: text/plain; charset=UTF-8',
	);

	// Send email.
	if ( wp_mail( $admin_email, $email_subject, $email_message, $headers ) ) {
		wp_send_json_success( array( 'message' => __( 'Messaggio inviato con successo!', 'marcello-scavo-tattoo' ) ) );
	} else {
		wp_send_json_error( array( 'message' => __( 'Errore nell\'invio del messaggio', 'marcello-scavo-tattoo' ) ) );
	}
}
add_action( 'wp_ajax_marcello_scavo_contact', 'marcello_scavo_handle_contact_form' );
add_action( 'wp_ajax_nopriv_marcello_scavo_contact', 'marcello_scavo_handle_contact_form' );

/**
 * Handle newsletter subscription
 */
function marcello_scavo_handle_newsletter() {
	// Verify nonce.
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'marcello_scavo_nonce' ) ) {
		wp_die( esc_html__( 'Errore di sicurezza', 'marcello-scavo-tattoo' ) );
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Email non valida', 'marcello-scavo-tattoo' ) ) );
	}

	// Here you would integrate with your newsletter service (MailChimp, etc.)
	// For now, we'll just send a confirmation email.

	$admin_email = get_option( 'admin_email' );
	$subject     = __( 'Nuova iscrizione newsletter', 'marcello-scavo-tattoo' );
	/* translators: %s: user email address */
	$message = sprintf( __( 'Nuova iscrizione alla newsletter: %s', 'marcello-scavo-tattoo' ), $email );

	if ( wp_mail( $admin_email, $subject, $message ) ) {
		wp_send_json_success( array( 'message' => __( 'Iscrizione completata!', 'marcello-scavo-tattoo' ) ) );
	} else {
		wp_send_json_error( array( 'message' => __( 'Errore nell\'iscrizione', 'marcello-scavo-tattoo' ) ) );
	}
}
add_action( 'wp_ajax_marcello_scavo_newsletter', 'marcello_scavo_handle_newsletter' );
add_action( 'wp_ajax_nopriv_marcello_scavo_newsletter', 'marcello_scavo_handle_newsletter' );

/**
 * Handle gallery filter AJAX
 */
function marcello_scavo_handle_gallery_filter() {
	// Verify nonce.
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'marcello_scavo_nonce' ) ) {
		wp_die( esc_html__( 'Errore di sicurezza', 'marcello-scavo-tattoo' ) );
	}

	$category = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';

	$args = array(
		'post_type'      => 'gallery',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Required for gallery ordering
		'meta_key'       => '_gallery_featured_order',
		'orderby'        => 'meta_value_num date',
		'order'          => 'ASC',
	);

	// Add taxonomy query if category is not 'all'.
	if ( 'all' !== $category ) {
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Required for category filtering
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'gallery_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$gallery_query = new WP_Query( $args );

	$html      = '';
	$has_items = false;

	if ( $gallery_query->have_posts() ) {
		while ( $gallery_query->have_posts() ) {
			$gallery_query->the_post();

			if ( has_post_thumbnail() ) {
				$has_items       = true;
				$image_url       = get_the_post_thumbnail_url( get_the_ID(), 'large' );
				$image_caption   = get_post_meta( get_the_ID(), '_gallery_image_caption', true );
				$image_technique = get_post_meta( get_the_ID(), '_gallery_image_technique', true );
				$image_alt       = get_post_meta( get_the_ID(), '_gallery_image_alt_text', true );

				$image_alt = $image_alt ? $image_alt : get_the_title();

				// Get categories for this item.
				$item_categories  = get_the_terms( get_the_ID(), 'gallery_category' );
				$category_classes = 'all';
				if ( ! empty( $item_categories ) ) {
					$category_slugs = array();
					foreach ( $item_categories as $cat ) {
						$category_slugs[] = $cat->slug;
					}
					$category_classes .= ' ' . implode( ' ', $category_slugs );
				}

				$html .= '<div class="gallery-item" data-category="' . esc_attr( $category_classes ) . '">';
				$html .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $image_alt ) . '">';
				$html .= '<div class="gallery-overlay">';
				$html .= '<h3>' . get_the_title() . '</h3>';

				if ( $image_technique ) {
					$html .= '<p>' . esc_html( str_replace( '_', ' ', ucfirst( $image_technique ) ) ) . '</p>';
				} elseif ( $image_caption ) {
					$html .= '<p>' . esc_html( wp_trim_words( $image_caption, 8 ) ) . '</p>';
				}

				$html .= '</div>';
				$html .= '</div>';
			}
		}
		wp_reset_postdata();
	}

	if ( ! $has_items ) {
		if ( current_user_can( 'manage_options' ) ) {
			$html  = '<div class="no-gallery-items admin-message">';
			$html .= '<h3>' . __( 'Nessuna immagine in questa categoria', 'marcello-scavo-tattoo' ) . '</h3>';
			$html .= '<p>' . __( 'Non ci sono ancora immagini per questa categoria.', 'marcello-scavo-tattoo' ) . '</p>';
			$html .= '<p><a href="' . admin_url( 'post-new.php?post_type=gallery' ) . '" class="button">' . __( 'Aggiungi nuova immagine', 'marcello-scavo-tattoo' ) . '</a></p>';
			$html .= '</div>';
		} else {
			$html = '<div class="no-gallery-items"><p>' . __( 'Nessuna immagine trovata per questa categoria.', 'marcello-scavo-tattoo' ) . '</p></div>';
		}
	}

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_marcello_scavo_gallery_filter', 'marcello_scavo_handle_gallery_filter' );
add_action( 'wp_ajax_nopriv_marcello_scavo_gallery_filter', 'marcello_scavo_handle_gallery_filter' );
