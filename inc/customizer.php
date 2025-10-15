<?php
/**
 * Theme Customizer Configuration
 *
 * @package MarcelloScavoTattoo
 * @subpackage Customizer
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer Settings
 *
 * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager.
 */
function marcello_scavo_customize_register( $wp_customize ) {
	// Determina il contesto della pagina per mostrare solo le sezioni pertinenti.
	$is_portfolio_page = false;
	$is_home_page      = true; // Default per homepage.

	// Metodo 1: Controlla tramite URL preview nel Customizer.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Customizer preview URL check
	if ( isset( $_GET['url'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Customizer preview URL processing
		$current_url = urldecode( sanitize_text_field( wp_unslash( $_GET['url'] ) ) );
		if (
			strpos( $current_url, '/portfolio' ) !== false ||
			strpos( $current_url, 'portfolio_category' ) !== false ||
			strpos( $current_url, 'taxonomy=portfolio_category' ) !== false
		) {
			$is_portfolio_page = true;
			$is_home_page      = false;
		}
	}

	// Hero Section Homepage.
	if ( $is_home_page && ! $is_portfolio_page ) {
		$wp_customize->add_section(
			'hero_section',
			array(
				'title'       => __( '🏠 Homepage - Sezione Hero', 'marcello-scavo-tattoo' ),
				'priority'    => 30,
				'description' => __( 'Personalizza la sezione Hero della homepage.', 'marcello-scavo-tattoo' ),
			)
		);

		// Hero Label (sopra il titolo).
		$wp_customize->add_setting(
			'hero_label',
			array(
				'default'           => 'L\'ARTE DEL TATTOO',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'hero_label',
			array(
				'label'       => __( 'Etichetta Hero (sopra il titolo)', 'marcello-scavo-tattoo' ),
				'description' => __( 'Inserisci in italiano - sarà tradotto automaticamente', 'marcello-scavo-tattoo' ),
				'section'     => 'hero_section',
				'type'        => 'text',
			)
		);

		// Hero Title.
		$wp_customize->add_setting(
			'hero_title',
			array(
				'default'           => 'Scopri l\'essenza dei miei tatuaggi e opere d\'arte.',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			'hero_title',
			array(
				'label'       => __( 'Titolo Hero Principale', 'marcello-scavo-tattoo' ),
				'description' => __( 'Inserisci in italiano - sarà tradotto automaticamente. Usa &lt;br&gt; per andare a capo', 'marcello-scavo-tattoo' ),
				'section'     => 'hero_section',
				'type'        => 'textarea',
			)
		);

		// Hero Description.
		$wp_customize->add_setting(
			'hero_description',
			array(
				'default'           => __( 'Benvenuti nel mio mondo creativo, dove ogni storia racconta una storia, i miei tatuaggi e le opere d\'arte nascono dall\'ispirazione e dalla passione. Che tu stia cercando un tatuaggio personalizzato per il tuo corpo o un\'opera unica per la tua parete, sei nel posto giusto. Esplora il mio portfolio e lasciati ispirare dalla fusione di arte e tatuaggio.', 'marcello-scavo-tattoo' ),
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);

		$wp_customize->add_control(
			'hero_description',
			array(
				'label'   => __( 'Descrizione Hero', 'marcello-scavo-tattoo' ),
				'section' => 'hero_section',
				'type'    => 'textarea',
			)
		);

		// Hero Button Text.
		$wp_customize->add_setting(
			'hero_button_text',
			array(
				'default'           => 'Esplora Ora',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'hero_button_text',
			array(
				'label'       => __( 'Testo Bottone Hero', 'marcello-scavo-tattoo' ),
				'description' => __( 'Inserisci in italiano - sarà tradotto automaticamente', 'marcello-scavo-tattoo' ),
				'section'     => 'hero_section',
				'type'        => 'text',
			)
		);
	}

	// Contact Info.
	$wp_customize->add_section(
		'contact_info',
		array(
			'title'    => __( 'Informazioni Contatto', 'marcello-scavo-tattoo' ),
			'priority' => 31,
		)
	);

	$wp_customize->add_setting(
		'contact_milano_address',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	$wp_customize->add_control(
		'contact_milano_address',
		array(
			'label'   => __( 'Indirizzo Milano', 'marcello-scavo-tattoo' ),
			'section' => 'contact_info',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'contact_messina_address',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	$wp_customize->add_control(
		'contact_messina_address',
		array(
			'label'   => __( 'Indirizzo Messina', 'marcello-scavo-tattoo' ),
			'section' => 'contact_info',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'contact_phone',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'contact_phone',
		array(
			'label'   => __( 'Telefono', 'marcello-scavo-tattoo' ),
			'section' => 'contact_info',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'contact_email',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_email',
		)
	);

	$wp_customize->add_control(
		'contact_email',
		array(
			'label'   => __( 'Email', 'marcello-scavo-tattoo' ),
			'section' => 'contact_info',
			'type'    => 'email',
		)
	);

	// WhatsApp Booking Section.
	$wp_customize->add_section(
		'whatsapp_booking',
		array(
			'title'       => __( '📱 WhatsApp Prenotazioni', 'marcello-scavo-tattoo' ),
			'description' => __( 'Configurazione per il bottone "Prenota Consulenza" che invia un messaggio WhatsApp.', 'marcello-scavo-tattoo' ),
			'priority'    => 35,
		)
	);

	// WhatsApp Number.
	$wp_customize->add_setting(
		'whatsapp_number',
		array(
			'default'           => '393331234567',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'whatsapp_number',
		array(
			'label'       => __( 'Numero WhatsApp', 'marcello-scavo-tattoo' ),
			'description' => __( 'Inserisci il numero in formato internazionale (es: 393331234567)', 'marcello-scavo-tattoo' ),
			'section'     => 'whatsapp_booking',
			'type'        => 'text',
			'input_attrs' => array(
				'placeholder' => '393331234567',
			),
		)
	);

	// WhatsApp Message.
	$wp_customize->add_setting(
		'whatsapp_message',
		array(
			'default'           => 'Ciao! Vorrei prenotare una consulenza per un tatuaggio.',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	$wp_customize->add_control(
		'whatsapp_message',
		array(
			'label'       => __( 'Messaggio WhatsApp', 'marcello-scavo-tattoo' ),
			'description' => __( 'Messaggio che verrà precompilato quando l\'utente clicca "Prenota Consulenza".', 'marcello-scavo-tattoo' ),
			'section'     => 'whatsapp_booking',
			'type'        => 'textarea',
		)
	);
}
add_action( 'customize_register', 'marcello_scavo_customize_register' );

/**
 * Helper function per ottenere testi multilingua dal Customizer
 *
 * @param string      $base_field   The base field name.
 * @param string      $fallback_it  Fallback text in Italian.
 * @param string|null $current_lang Current language code.
 * @return string The multilingual theme mod value.
 */
function marcello_scavo_get_multilingual_theme_mod( $base_field, $fallback_it = '', $current_lang = null ) {
	// Se non è specificata, rileva la lingua corrente dal localStorage o default.
	if ( ! $current_lang ) {
		$current_lang = 'it'; // Default italiano.
	}

	$field_name = $base_field . '_' . $current_lang;
	$value      = get_theme_mod( $field_name, '' );

	// Se non c'è valore personalizzato, usa il fallback.
	if ( empty( $value ) ) {
		$value = $fallback_it;
	}

	return $value;
}

/**
 * Sistema di traduzione automatica con Google Translate + Cache
 *
 * @param string $text        Text to translate.
 * @param string $target_lang Target language code.
 * @param string $source_lang Source language code.
 * @return string Translated text or original text if translation fails.
 */
function marcello_scavo_get_cached_translation( $text, $target_lang, $source_lang = 'it' ) {
	if ( $source_lang === $target_lang ) {
		return $text;
	}

	// Genera chiave cache unica.
	$cache_key = 'translation_' . md5( $text . '_' . $source_lang . '_' . $target_lang );

	// Cerca in cache.
	$cached = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	// Chiama Google Translate.
	$translation = marcello_scavo_google_translate_text( $text, $target_lang, $source_lang );

	if ( $translation ) {
		// Salva in cache per 30 giorni.
		set_transient( $cache_key, $translation, 30 * DAY_IN_SECONDS );
		return $translation;
	}

	// Fallback: usa il dizionario locale o testo originale.
	$fallback = marcello_scavo_fallback_translation( $text, $target_lang );
	return $fallback ? $fallback : $text;
}

/**
 * Funzioni di supporto per traduzione.
 *
 * @param string $text        Text to translate.
 * @param string $target_lang Target language code.
 * @param string $source_lang Source language code.
 * @return string|false Translated text or false on failure.
 */
function marcello_scavo_google_translate_text( $text, $target_lang, $source_lang = 'it' ) {
	$text = trim( $text );
	if ( empty( $text ) ) {
		return '';
	}

	// URL dell'API gratuita di Google Translate.
	$url = 'https://translate.googleapis.com/translate_a/single?' . http_build_query(
		array(
			'client' => 'gtx',
			'sl'     => $source_lang,
			'tl'     => $target_lang,
			'dt'     => 't',
			'q'      => $text,
		)
	);

	// Chiamata HTTP.
	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 10,
			'headers' => array(
				'User-Agent' => 'Mozilla/5.0 (compatible; WordPress)',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = wp_remote_retrieve_body( $response );

	// Parse della risposta JSON di Google.
	$data = json_decode( $body, true );

	if ( $data && isset( $data[0][0][0] ) ) {
		$translated = $data[0][0][0];
		return $translated;
	}

	return false;
}

/**
 * Fallback translation using a local dictionary.
 *
 * @param string $text        Text to translate.
 * @param string $target_lang Target language code.
 * @return string|false Translated text or false if not found.
 */
function marcello_scavo_fallback_translation( $text, $target_lang ) {
	$simple_dict = array(
		'it' => array(
			'ciao sono marcello scavo' => array(
				'en' => 'hello i am marcello scavo',
				'es' => 'hola soy marcello scavo',
			),
			'l\'arte del tattoo'       => array(
				'en' => 'the art of tattoo',
				'es' => 'el arte del tatuaje',
			),
			'esplora ora'              => array(
				'en' => 'explore now',
				'es' => 'explora ahora',
			),
		),
	);

	$text_lower = strtolower( trim( $text ) );

	if ( isset( $simple_dict['it'][ $text_lower ][ $target_lang ] ) ) {
		return $simple_dict['it'][ $text_lower ][ $target_lang ];
	}

	return false;
}

/**
 * Sanitization Functions
 *
 * @param string $input Input value to sanitize.
 * @return string Sanitized footer layout value.
 */
function marcello_scavo_sanitize_footer_layout( $input ) {
	$valid = array(
		'one_column',
		'two_columns',
		'three_columns',
		'four_columns',
	);

	if ( in_array( $input, $valid, true, true ) ) {
		return $input;
	}

	return 'three_columns';
}

/**
 * Sanitize social style options.
 *
 * @param string $input Input value to sanitize.
 * @return string Sanitized social style value.
 */
function marcello_scavo_sanitize_social_style( $input ) {
	$valid = array(
		'modern',
		'minimal',
		'buttons',
		'cards',
	);

	if ( in_array( $input, $valid, true, true ) ) {
		return $input;
	}

	return 'modern';
}
