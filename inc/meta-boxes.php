<?php
/**
 * Custom Meta Boxes and Fields
 *
 * @package MarcelloScavoTattoo
 * @subpackage MetaBoxes
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Custom Fields Support
 */
function marcello_scavo_add_meta_boxes() {
	// Portfolio Meta Box.
	add_meta_box(
		'portfolio_details',
		__( 'Dettagli Portfolio', 'marcello-scavo-tattoo' ),
		'marcello_scavo_portfolio_meta_box_callback',
		'portfolio',
		'normal',
		'high'
	);

	// Gallery Meta Box.
	add_meta_box(
		'gallery_details',
		__( 'Dettagli Galleria', 'marcello-scavo-tattoo' ),
		'marcello_scavo_gallery_meta_box_callback',
		'gallery',
		'normal',
		'high'
	);

	// Shop Product Meta Box.
	add_meta_box(
		'product_details',
		__( 'Dettagli Prodotto', 'marcello-scavo-tattoo' ),
		'marcello_scavo_product_meta_box_callback',
		'shop_product',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'marcello_scavo_add_meta_boxes' );

/**
 * Portfolio Meta Box Callback
 *
 * @param WP_Post $post The post object.
 */
function marcello_scavo_portfolio_meta_box_callback( $post ) {
	wp_nonce_field( 'save_portfolio_details', 'portfolio_meta_nonce' );

	$client_name      = get_post_meta( $post->ID, '_portfolio_client_name', true );
	$project_date     = get_post_meta( $post->ID, '_portfolio_project_date', true );
	$project_location = get_post_meta( $post->ID, '_portfolio_project_location', true );
	$project_type     = get_post_meta( $post->ID, '_portfolio_project_type', true );
	$project_duration = get_post_meta( $post->ID, '_portfolio_project_duration', true );
	$gallery_images   = get_post_meta( $post->ID, '_portfolio_gallery', true );

	?>
	<table class="form-table">
		<tr>
			<th><label for="portfolio_client_name"><?php _e( 'Nome Cliente', 'marcello-scavo-tattoo' ); ?></label></th>
			<td><input type="text" id="portfolio_client_name" name="portfolio_client_name" value="<?php echo esc_attr( $client_name ); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th><label for="portfolio_project_date"><?php _e( 'Data Progetto', 'marcello-scavo-tattoo' ); ?></label></th>
			<td><input type="date" id="portfolio_project_date" name="portfolio_project_date" value="<?php echo esc_attr( $project_date ); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th><label for="portfolio_project_location"><?php _e( 'Luogo', 'marcello-scavo-tattoo' ); ?></label></th>
			<td>
				<select id="portfolio_project_location" name="portfolio_project_location" class="regular-text">
					<option value=""><?php _e( 'Seleziona Luogo', 'marcello-scavo-tattoo' ); ?></option>
					<option value="milano" <?php selected( $project_location, 'milano' ); ?>><?php _e( 'Milano', 'marcello-scavo-tattoo' ); ?></option>
					<option value="messina" <?php selected( $project_location, 'messina' ); ?>><?php _e( 'Messina', 'marcello-scavo-tattoo' ); ?></option>
					<option value="altro" <?php selected( $project_location, 'altro' ); ?>><?php _e( 'Altro', 'marcello-scavo-tattoo' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="portfolio_project_type"><?php _e( 'Tipo Progetto', 'marcello-scavo-tattoo' ); ?></label></th>
			<td>
				<select id="portfolio_project_type" name="portfolio_project_type" class="regular-text">
					<option value=""><?php _e( 'Seleziona Tipo', 'marcello-scavo-tattoo' ); ?></option>
					<option value="tattoo" <?php selected( $project_type, 'tattoo' ); ?>><?php _e( 'Tatuaggio', 'marcello-scavo-tattoo' ); ?></option>
					<option value="illustration" <?php selected( $project_type, 'illustration' ); ?>><?php _e( 'Illustrazione', 'marcello-scavo-tattoo' ); ?></option>
					<option value="graphic_design" <?php selected( $project_type, 'graphic_design' ); ?>><?php _e( 'Grafica', 'marcello-scavo-tattoo' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="portfolio_project_duration"><?php _e( 'Durata (ore)', 'marcello-scavo-tattoo' ); ?></label></th>
			<td><input type="number" id="portfolio_project_duration" name="portfolio_project_duration" value="<?php echo esc_attr( $project_duration ); ?>" class="small-text" min="0" step="0.5" /></td>
		</tr>
	</table>
	<?php
}

/**
 * Gallery Meta Box Callback
 *
 * @param WP_Post $post The post object.
 */
function marcello_scavo_gallery_meta_box_callback( $post ) {
	wp_nonce_field( 'save_gallery_details', 'gallery_meta_nonce' );

	$image_caption    = get_post_meta( $post->ID, '_gallery_image_caption', true );
	$image_alt_text   = get_post_meta( $post->ID, '_gallery_image_alt_text', true );
	$image_technique  = get_post_meta( $post->ID, '_gallery_image_technique', true );
	$image_dimensions = get_post_meta( $post->ID, '_gallery_image_dimensions', true );
	$creation_date    = get_post_meta( $post->ID, '_gallery_creation_date', true );
	$featured_order   = get_post_meta( $post->ID, '_gallery_featured_order', true );
	$is_featured      = get_post_meta( $post->ID, '_gallery_is_featured', true );

	?>
	<table class="form-table">
		<tr>
			<th><label for="gallery_image_caption"><?php _e( 'Didascalia Immagine', 'marcello-scavo-tattoo' ); ?></label></th>
			<td>
				<textarea id="gallery_image_caption" name="gallery_image_caption" rows="3" class="large-text"><?php echo esc_textarea( $image_caption ); ?></textarea>
				<p class="description"><?php _e( 'Descrizione che apparirà sotto l\'immagine nella galleria.', 'marcello-scavo-tattoo' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="gallery_image_alt_text"><?php _e( 'Testo Alternativo', 'marcello-scavo-tattoo' ); ?></label></th>
			<td>
				<input type="text" id="gallery_image_alt_text" name="gallery_image_alt_text" value="<?php echo esc_attr( $image_alt_text ); ?>" class="regular-text" />
				<p class="description"><?php _e( 'Testo alternativo per l\'accessibilità.', 'marcello-scavo-tattoo' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="gallery_image_technique"><?php _e( 'Tecnica', 'marcello-scavo-tattoo' ); ?></label></th>
			<td>
				<select id="gallery_image_technique" name="gallery_image_technique" class="regular-text">
					<option value=""><?php _e( 'Seleziona Tecnica', 'marcello-scavo-tattoo' ); ?></option>
					<option value="olio_su_tela" <?php selected( $image_technique, 'olio_su_tela' ); ?>><?php _e( 'Olio su tela', 'marcello-scavo-tattoo' ); ?></option>
					<option value="acrilico" <?php selected( $image_technique, 'acrilico' ); ?>><?php _e( 'Acrilico', 'marcello-scavo-tattoo' ); ?></option>
					<option value="disegno_matita" <?php selected( $image_technique, 'disegno_matita' ); ?>><?php _e( 'Disegno a matita', 'marcello-scavo-tattoo' ); ?></option>
					<option value="tatuaggio_blackwork" <?php selected( $image_technique, 'tatuaggio_blackwork' ); ?>><?php _e( 'Tatuaggio Blackwork', 'marcello-scavo-tattoo' ); ?></option>
					<option value="tatuaggio_realistico" <?php selected( $image_technique, 'tatuaggio_realistico' ); ?>><?php _e( 'Tatuaggio Realistico', 'marcello-scavo-tattoo' ); ?></option>
					<option value="tatuaggio_geometrico" <?php selected( $image_technique, 'tatuaggio_geometrico' ); ?>><?php _e( 'Tatuaggio Geometrico', 'marcello-scavo-tattoo' ); ?></option>
					<option value="watercolor" <?php selected( $image_technique, 'watercolor' ); ?>><?php _e( 'Acquerello', 'marcello-scavo-tattoo' ); ?></option>
					<option value="misto" <?php selected( $image_technique, 'misto' ); ?>><?php _e( 'Tecnica mista', 'marcello-scavo-tattoo' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="gallery_image_dimensions"><?php _e( 'Dimensioni', 'marcello-scavo-tattoo' ); ?></label></th>
			<td>
				<input type="text" id="gallery_image_dimensions" name="gallery_image_dimensions" value="<?php echo esc_attr( $image_dimensions ); ?>" class="regular-text" placeholder="es. 30x40 cm" />
				<p class="description"><?php _e( 'Dimensioni dell\'opera o area del tatuaggio.', 'marcello-scavo-tattoo' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="gallery_creation_date"><?php _e( 'Data di Creazione', 'marcello-scavo-tattoo' ); ?></label></th>
			<td><input type="date" id="gallery_creation_date" name="gallery_creation_date" value="<?php echo esc_attr( $creation_date ); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th><label for="gallery_is_featured"><?php _e( 'Immagine in Evidenza', 'marcello-scavo-tattoo' ); ?></label></th>
			<td>
				<label>
					<input type="checkbox" id="gallery_is_featured" name="gallery_is_featured" value="1" <?php checked( $is_featured, '1' ); ?> />
					<?php _e( 'Mostra questa immagine in evidenza nella galleria', 'marcello-scavo-tattoo' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th><label for="gallery_featured_order"><?php _e( 'Ordine in Evidenza', 'marcello-scavo-tattoo' ); ?></label></th>
			<td>
				<input type="number" id="gallery_featured_order" name="gallery_featured_order" value="<?php echo esc_attr( $featured_order ? $featured_order : 0 ); ?>" class="small-text" min="0" />
				<p class="description"><?php _e( 'Ordine di visualizzazione tra le immagini in evidenza (0 = primo).', 'marcello-scavo-tattoo' ); ?></p>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Shop Product Meta Box Callback
 *
 * @param WP_Post $post The post object.
 */
function marcello_scavo_product_meta_box_callback( $post ) {
	wp_nonce_field( 'save_product_details', 'product_meta_nonce' );

	$product_price = get_post_meta( $post->ID, '_product_price', true );
	$product_type  = get_post_meta( $post->ID, '_product_type', true );
	$product_stock = get_post_meta( $post->ID, '_product_stock', true );
	$product_sku   = get_post_meta( $post->ID, '_product_sku', true );

	?>
	<table class="form-table">
		<tr>
			<th><label for="product_price"><?php _e( 'Prezzo (€)', 'marcello-scavo-tattoo' ); ?></label></th>
			<td><input type="number" id="product_price" name="product_price" value="<?php echo esc_attr( $product_price ); ?>" class="small-text" min="0" step="0.01" required /></td>
		</tr>
		<tr>
			<th><label for="product_type"><?php _e( 'Tipo Prodotto', 'marcello-scavo-tattoo' ); ?></label></th>
			<td>
				<select id="product_type" name="product_type" class="regular-text" required>
					<option value=""><?php _e( 'Seleziona Tipo', 'marcello-scavo-tattoo' ); ?></option>
					<option value="flash_tattoo" <?php selected( $product_type, 'flash_tattoo' ); ?>><?php _e( 'Flash Tattoo', 'marcello-scavo-tattoo' ); ?></option>
					<option value="merchandise" <?php selected( $product_type, 'merchandise' ); ?>><?php _e( 'Merchandising', 'marcello-scavo-tattoo' ); ?></option>
					<option value="print" <?php selected( $product_type, 'print' ); ?>><?php _e( 'Stampa', 'marcello-scavo-tattoo' ); ?></option>
					<option value="digital" <?php selected( $product_type, 'digital' ); ?>><?php _e( 'Digitale', 'marcello-scavo-tattoo' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="product_stock"><?php _e( 'Quantità in Stock', 'marcello-scavo-tattoo' ); ?></label></th>
			<td><input type="number" id="product_stock" name="product_stock" value="<?php echo esc_attr( $product_stock ); ?>" class="small-text" min="0" /></td>
		</tr>
		<tr>
			<th><label for="product_sku"><?php _e( 'Codice Prodotto (SKU)', 'marcello-scavo-tattoo' ); ?></label></th>
			<td><input type="text" id="product_sku" name="product_sku" value="<?php echo esc_attr( $product_sku ); ?>" class="regular-text" /></td>
		</tr>
	</table>
	<?php
}

/**
 * Save Meta Box Data
 *
 * @param int $post_id The post ID.
 * @return int|void Post ID on success, void on failure.
 */
function marcello_scavo_save_meta_boxes( $post_id ) {
	// Prevent autosave and bulk edit.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check user permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Portfolio meta.
	if ( isset( $_POST['portfolio_meta_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['portfolio_meta_nonce'] ) ), 'save_portfolio_details' ) ) {
		if ( isset( $_POST['portfolio_client_name'] ) ) {
			update_post_meta( $post_id, '_portfolio_client_name', isset( $_POST['portfolio_client_name'] ) ? sanitize_text_field( wp_unslash( $_POST['portfolio_client_name'] ) ) : '' );
		}
		if ( isset( $_POST['portfolio_project_date'] ) ) {
			update_post_meta( $post_id, '_portfolio_project_date', isset( $_POST['portfolio_project_date'] ) ? sanitize_text_field( wp_unslash( $_POST['portfolio_project_date'] ) ) : '' );
		}
		if ( isset( $_POST['portfolio_project_location'] ) ) {
			update_post_meta( $post_id, '_portfolio_project_location', isset( $_POST['portfolio_project_location'] ) ? sanitize_text_field( wp_unslash( $_POST['portfolio_project_location'] ) ) : '' );
		}
		if ( isset( $_POST['portfolio_project_type'] ) ) {
			update_post_meta( $post_id, '_portfolio_project_type', isset( $_POST['portfolio_project_type'] ) ? sanitize_text_field( wp_unslash( $_POST['portfolio_project_type'] ) ) : '' );
		}
		if ( isset( $_POST['portfolio_project_duration'] ) ) {
			update_post_meta( $post_id, '_portfolio_project_duration', floatval( $_POST['portfolio_project_duration'] ) );
		}
	}

	// Gallery meta.
	if ( isset( $_POST['gallery_meta_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gallery_meta_nonce'] ) ), 'save_gallery_details' ) ) {
		if ( isset( $_POST['gallery_image_caption'] ) ) {
			update_post_meta( $post_id, '_gallery_image_caption', sanitize_textarea_field( wp_unslash( $_POST['gallery_image_caption'] ) ) );
		}
		if ( isset( $_POST['gallery_image_alt_text'] ) ) {
			update_post_meta( $post_id, '_gallery_image_alt_text', isset( $_POST['gallery_image_alt_text'] ) ? sanitize_text_field( wp_unslash( $_POST['gallery_image_alt_text'] ) ) : '' );
		}
		if ( isset( $_POST['gallery_image_technique'] ) ) {
			update_post_meta( $post_id, '_gallery_image_technique', isset( $_POST['gallery_image_technique'] ) ? sanitize_text_field( wp_unslash( $_POST['gallery_image_technique'] ) ) : '' );
		}
		if ( isset( $_POST['gallery_image_dimensions'] ) ) {
			update_post_meta( $post_id, '_gallery_image_dimensions', isset( $_POST['gallery_image_dimensions'] ) ? sanitize_text_field( wp_unslash( $_POST['gallery_image_dimensions'] ) ) : '' );
		}
		if ( isset( $_POST['gallery_creation_date'] ) ) {
			update_post_meta( $post_id, '_gallery_creation_date', isset( $_POST['gallery_creation_date'] ) ? sanitize_text_field( wp_unslash( $_POST['gallery_creation_date'] ) ) : '' );
		}
		if ( isset( $_POST['gallery_featured_order'] ) ) {
			update_post_meta( $post_id, '_gallery_featured_order', intval( $_POST['gallery_featured_order'] ) );
		}
		// Checkbox handling.
		if ( isset( $_POST['gallery_is_featured'] ) ) {
			update_post_meta( $post_id, '_gallery_is_featured', '1' );
		} else {
			delete_post_meta( $post_id, '_gallery_is_featured' );
		}
	}

	// Product meta.
	if ( isset( $_POST['product_meta_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['product_meta_nonce'] ) ), 'save_product_details' ) ) {
		if ( isset( $_POST['product_price'] ) ) {
			update_post_meta( $post_id, '_product_price', floatval( $_POST['product_price'] ) );
		}
		if ( isset( $_POST['product_type'] ) ) {
			update_post_meta( $post_id, '_product_type', isset( $_POST['product_type'] ) ? sanitize_text_field( wp_unslash( $_POST['product_type'] ) ) : '' );
		}
		if ( isset( $_POST['product_stock'] ) ) {
			update_post_meta( $post_id, '_product_stock', intval( $_POST['product_stock'] ) );
		}
		if ( isset( $_POST['product_sku'] ) ) {
			update_post_meta( $post_id, '_product_sku', isset( $_POST['product_sku'] ) ? sanitize_text_field( wp_unslash( $_POST['product_sku'] ) ) : '' );
		}
	}
}
add_action( 'save_post', 'marcello_scavo_save_meta_boxes' );
