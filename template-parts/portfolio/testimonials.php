<?php

/**
 * Portfolio Testimonials Component
 * Unified testimonials section for all portfolio templates
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
$section_title   = '';
$section_subtitle = '';

if (is_post_type_archive('portfolio')) {
    $current_context = 'archive';
    $section_title   = __('Storie di Soddisfazione', 'marcello-scavo-tattoo');
    $section_subtitle = __('Testimonianze reali dai nostri clienti più affezionati', 'marcello-scavo-tattoo');
} elseif (is_tax('portfolio_category')) {
    $current_context = 'taxonomy';
    $current_term    = get_queried_object();
    $section_title   = sprintf(__('Esperienza %s', 'marcello-scavo-tattoo'), $current_term->name);
    $section_subtitle = sprintf(__('Clienti che hanno scelto il nostro stile %s', 'marcello-scavo-tattoo'), strtolower($current_term->name));
} elseif (is_singular('portfolio')) {
    $current_context = 'single';
    $section_title   = __('Feedback dei Clienti', 'marcello-scavo-tattoo');
    $section_subtitle = __('L\'opinione di chi ha vissuto l\'esperienza Marcello Scavo', 'marcello-scavo-tattoo');
}

// Get testimonials from customizer or default
$testimonials = get_theme_mod('portfolio_testimonials', array(
    array(
        'name'    => 'Sofia R.',
        'rating'  => 5,
        'text'    => 'Esperienza incredibile! Marcello ha trasformato la mia idea in un\'opera d\'arte che porto con orgoglio sulla pelle. Professionalità e talento unici.',
        'image'   => '',
        'style'   => 'Realismo',
        'date'    => '2024-01-15',
    ),
    array(
        'name'    => 'Marco T.',
        'rating'  => 5,
        'text'    => 'Qualità eccezionale e attenzione ai dettagli impressionante. Il tatuaggio geometric che ho fatto supera ogni aspettativa. Consigliatissimo!',
        'image'   => '',
        'style'   => 'Geometric',
        'date'    => '2024-01-10',
    ),
    array(
        'name'    => 'Elena M.',
        'rating'  => 5,
        'text'    => 'Studio pulitissimo, ambiente accogliente e risultato fantastico. Marcello è un vero artista che sa ascoltare i desideri del cliente.',
        'image'   => '',
        'style'   => 'Traditional',
        'date'    => '2024-01-05',
    ),
    array(
        'name'    => 'Andrea L.',
        'rating'  => 5,
        'text'    => 'Il mio sleeve dotwork è semplicemente perfetto. Ogni sessione è stata gestita con cura e professionalità. Tornerò sicuramente!',
        'image'   => '',
        'style'   => 'Dotwork',
        'date'    => '2023-12-20',
    ),
    array(
        'name'    => 'Giulia P.',
        'rating'  => 5,
        'text'    => 'Finalmente ho trovato il tatuatore che cercavo. Competenza, creatività e risultati che vanno oltre le aspettative. Grazie Marcello!',
        'image'   => '',
        'style'   => 'Watercolor',
        'date'    => '2023-12-15',
    ),
    array(
        'name'    => 'Lorenzo C.',
        'rating'  => 5,
        'text'    => 'Esperienza top! Dal primo incontro alla realizzazione finale, tutto perfetto. Consiglio vivamente a chiunque cerchi qualità e professionalità.',
        'image'   => '',
        'style'   => 'Blackwork',
        'date'    => '2023-12-10',
    ),
));

// Filter testimonials by context if applicable
if ($current_context === 'taxonomy' && isset($current_term)) {
    $testimonials = array_filter($testimonials, function ($testimonial) use ($current_term) {
        return strtolower($testimonial['style']) === strtolower($current_term->name);
    });

    // If no specific testimonials found, use all
    if (empty($testimonials)) {
        $testimonials = get_theme_mod('portfolio_testimonials', array());
    }
}

// Calculate average rating
$total_rating = 0;
$rating_count = 0;
foreach ($testimonials as $testimonial) {
    if (isset($testimonial['rating'])) {
        $total_rating += (int) $testimonial['rating'];
        $rating_count++;
    }
}
$average_rating = $rating_count > 0 ? round($total_rating / $rating_count, 1) : 5.0;

// Limit testimonials to 6 for display
$displayed_testimonials = array_slice($testimonials, 0, 6);
?>

<section class="portfolio-testimonials portfolio-testimonials--<?php echo esc_attr($current_context); ?>">
    <div class="container">
        <div class="testimonials__header">
            <h2 class="testimonials__title">
                <?php echo esc_html($section_title); ?>
            </h2>

            <?php if ($section_subtitle) : ?>
                <p class="testimonials__subtitle">
                    <?php echo esc_html($section_subtitle); ?>
                </p>
            <?php endif; ?>

            <div class="testimonials__divider">
                <span class="divider-line"></span>
                <span class="divider-icon">
                    <i class="fas fa-quote-right" aria-hidden="true"></i>
                </span>
                <span class="divider-line"></span>
            </div>

            <div class="testimonials__stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html(number_format($average_rating, 1)); ?></span>
                    <div class="stat-stars">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <i class="fas fa-star <?php echo $i <= $average_rating ? 'star--filled' : 'star--empty'; ?>" aria-hidden="true"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="stat-label"><?php esc_html_e('Valutazione media', 'marcello-scavo-tattoo'); ?></span>
                </div>

                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html(count($testimonials)); ?>+</span>
                    <span class="stat-label"><?php esc_html_e('Clienti soddisfatti', 'marcello-scavo-tattoo'); ?></span>
                </div>

                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <span class="stat-label"><?php esc_html_e('Raccomandazioni', 'marcello-scavo-tattoo'); ?></span>
                </div>
            </div>
        </div>

        <div class="testimonials__grid">
            <?php foreach ($displayed_testimonials as $index => $testimonial) : ?>
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($index * 100); ?>">
                    <div class="testimonial-card__header">
                        <div class="testimonial__rating">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <i class="fas fa-star <?php echo $i <= (int) $testimonial['rating'] ? 'star--filled' : 'star--empty'; ?>" aria-hidden="true"></i>
                            <?php endfor; ?>
                        </div>

                        <?php if (isset($testimonial['date'])) : ?>
                            <time class="testimonial__date" datetime="<?php echo esc_attr($testimonial['date']); ?>">
                                <?php echo esc_html(date_i18n('F Y', strtotime($testimonial['date']))); ?>
                            </time>
                        <?php endif; ?>
                    </div>

                    <blockquote class="testimonial__text">
                        "<?php echo esc_html($testimonial['text']); ?>"
                    </blockquote>

                    <div class="testimonial__footer">
                        <div class="testimonial__author">
                            <?php if (! empty($testimonial['image'])) : ?>
                                <img src="<?php echo esc_url($testimonial['image']); ?>"
                                    alt="<?php echo esc_attr($testimonial['name']); ?>"
                                    class="testimonial__avatar"
                                    loading="lazy">
                            <?php else : ?>
                                <div class="testimonial__avatar testimonial__avatar--placeholder">
                                    <i class="fas fa-user" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>

                            <div class="testimonial__author-info">
                                <cite class="testimonial__name"><?php echo esc_html($testimonial['name']); ?></cite>

                                <?php if (isset($testimonial['style'])) : ?>
                                    <span class="testimonial__style"><?php echo esc_html($testimonial['style']); ?> Style</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="testimonial__quote-icon">
                            <i class="fas fa-quote-right" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="testimonials__actions">
            <a href="<?php echo esc_url(home_url('/recensioni/')); ?>" class="btn btn--outline">
                <i class="fas fa-comments" aria-hidden="true"></i>
                <span class="btn__text"><?php esc_html_e('Leggi Tutte le Recensioni', 'marcello-scavo-tattoo'); ?></span>
            </a>

            <a href="<?php echo esc_url(home_url('/lascia-recensione/')); ?>" class="btn btn--secondary">
                <i class="fas fa-edit" aria-hidden="true"></i>
                <span class="btn__text"><?php esc_html_e('Lascia una Recensione', 'marcello-scavo-tattoo'); ?></span>
            </a>
        </div>
    </div>
</section>