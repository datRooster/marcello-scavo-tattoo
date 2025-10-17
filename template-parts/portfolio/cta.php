<?php

/**
 * Portfolio CTA (Call to Action) Component
 * Unified CTA section for all portfolio templates
 *
 * @package MarcelloScavoTattoo
 * @version 1.0.0
 */

// Prevent direct access.
if (! defined('ABSPATH')) {
    exit;
}

// Determine current context
$current_context = '';
$cta_title       = '';
$cta_description = '';
$primary_button  = array();
$secondary_button = array();

if (is_post_type_archive('portfolio')) {
    $current_context = 'archive';
    $cta_title       = __('Ti piace quello che vedi?', 'marcello-scavo-tattoo');
    $cta_description = __('Iniziamo a creare insieme il tuo prossimo tatuaggio unico. Ogni progetto è personalizzato e realizzato con la massima cura.', 'marcello-scavo-tattoo');

    $primary_button = array(
        'url'  => '#booking',
        'text' => __('Prenota Consultazione', 'marcello-scavo-tattoo'),
        'icon' => 'fas fa-calendar-plus',
    );

    $secondary_button = array(
        'url'  => '#contact',
        'text' => __('Contattami', 'marcello-scavo-tattoo'),
        'icon' => 'fas fa-envelope',
    );
} elseif (is_tax('portfolio_category')) {
    $current_context = 'taxonomy';
    $current_term    = get_queried_object();
    $cta_title       = sprintf(__('Interessato al nostro stile %s?', 'marcello-scavo-tattoo'), $current_term->name);
    $cta_description = sprintf(__('Lasciati ispirare dalla nostra collezione %s e iniziamo a progettare il tuo prossimo tatuaggio personalizzato.', 'marcello-scavo-tattoo'), strtolower($current_term->name));

    $primary_button = array(
        'url'  => '#booking',
        'text' => __('Prenota Consulenza', 'marcello-scavo-tattoo'),
        'icon' => 'fas fa-calendar-plus',
    );

    $secondary_button = array(
        'url'  => get_post_type_archive_link('portfolio'),
        'text' => __('Esplora Tutto', 'marcello-scavo-tattoo'),
        'icon' => 'fas fa-th-large',
    );
} elseif (is_singular('portfolio')) {
    $current_context = 'single';
    $cta_title       = __('Vuoi realizzare qualcosa di simile?', 'marcello-scavo-tattoo');
    $cta_description = __('Ogni progetto è unico e personalizzato. Raccontami la tua idea e la trasformeremo in arte sulla tua pelle.', 'marcello-scavo-tattoo');

    $primary_button = array(
        'url'  => '#booking',
        'text' => __('Discuti il Progetto', 'marcello-scavo-tattoo'),
        'icon' => 'fas fa-comments',
    );

    $secondary_button = array(
        'url'  => get_post_type_archive_link('portfolio'),
        'text' => __('Altri Progetti', 'marcello-scavo-tattoo'),
        'icon' => 'fas fa-images',
    );
}

// WhatsApp integration
$whatsapp_number  = get_theme_mod('whatsapp_number', '393331234567');
$whatsapp_message = get_theme_mod('whatsapp_message', 'Ciao! Vorrei prenotare una consulenza per un tatuaggio.');

// Custom message per context
if ($current_context === 'single') {
    $whatsapp_message = sprintf(
        __('Ciao! Sono interessato al progetto "%s" che ho visto nel portfolio. Vorrei discutere di qualcosa di simile.', 'marcello-scavo-tattoo'),
        get_the_title()
    );
} elseif ($current_context === 'taxonomy') {
    $whatsapp_message = sprintf(
        __('Ciao! Mi piace molto la categoria %s del vostro portfolio. Vorrei discutere di un progetto simile.', 'marcello-scavo-tattoo'),
        $current_term->name
    );
}

$whatsapp_url = 'https://wa.me/' . $whatsapp_number . '?text=' . urlencode($whatsapp_message);

// Update primary button URL for WhatsApp
if (strpos($primary_button['url'], '#booking') !== false) {
    $primary_button['url'] = $whatsapp_url;
}
?>

<section class="portfolio-cta portfolio-cta--<?php echo esc_attr($current_context); ?>">
    <div class="portfolio-cta__background"></div>
    <div class="portfolio-cta__overlay"></div>

    <div class="container">
        <div class="portfolio-cta__content">
            <div class="cta-content__inner">
                <h2 class="portfolio-cta__title">
                    <?php echo esc_html($cta_title); ?>
                </h2>

                <div class="portfolio-cta__divider">
                    <span class="divider-line"></span>
                    <span class="divider-icon">
                        <i class="fas fa-heart" aria-hidden="true"></i>
                    </span>
                    <span class="divider-line"></span>
                </div>

                <p class="portfolio-cta__description">
                    <?php echo esc_html($cta_description); ?>
                </p>

                <div class="portfolio-cta__actions">
                    <?php if ($primary_button) : ?>
                        <a href="<?php echo esc_url($primary_button['url']); ?>"
                            class="btn btn--primary btn--large"
                            <?php echo strpos($primary_button['url'], 'wa.me') !== false ? 'target="_blank" rel="noopener"' : ''; ?>>
                            <i class="<?php echo esc_attr($primary_button['icon']); ?>" aria-hidden="true"></i>
                            <span class="btn__text"><?php echo esc_html($primary_button['text']); ?></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($secondary_button) : ?>
                        <a href="<?php echo esc_url($secondary_button['url']); ?>" class="btn btn--outline btn--large">
                            <i class="<?php echo esc_attr($secondary_button['icon']); ?>" aria-hidden="true"></i>
                            <span class="btn__text"><?php echo esc_html($secondary_button['text']); ?></span>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="portfolio-cta__features">
                    <div class="cta-feature">
                        <span class="cta-feature__icon">🎨</span>
                        <span class="cta-feature__text"><?php esc_html_e('Consulenza gratuita', 'marcello-scavo-tattoo'); ?></span>
                    </div>
                    <div class="cta-feature">
                        <span class="cta-feature__icon">✨</span>
                        <span class="cta-feature__text"><?php esc_html_e('Design personalizzato', 'marcello-scavo-tattoo'); ?></span>
                    </div>
                    <div class="cta-feature">
                        <span class="cta-feature__icon">🏆</span>
                        <span class="cta-feature__text"><?php esc_html_e('Qualità professionale', 'marcello-scavo-tattoo'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>