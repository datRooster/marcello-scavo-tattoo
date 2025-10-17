<?php

/**
 * Portfolio Hero Section Component
 * Unified hero section for all portfolio templates
 *
 * @package MarcelloScavoTattoo
 * @version 1.0.0
 */

// Prevent direct access.
if (! defined('ABSPATH')) {
    exit;
}

// Get current context
$current_object = get_queried_object();
$context_type   = '';
$hero_title     = '';
$hero_subtitle  = '';
$hero_cta_text  = '';
$hero_cta_link  = '';

if (is_post_type_archive('portfolio')) {
    // Archive Portfolio (Complete)
    $context_type  = 'archive';
    $hero_title    = __('Portfolio Completo', 'marcello-scavo-tattoo');
    $hero_subtitle = __('Una collezione completa dei miei lavori più significativi e rappresentativi', 'marcello-scavo-tattoo');
    $hero_cta_text = __('Filtra Progetti', 'marcello-scavo-tattoo');
    $hero_cta_link = '#portfolio-filters';
} elseif (is_tax('portfolio_category')) {
    // Taxonomy Portfolio (Category)
    $context_type  = 'taxonomy';
    $hero_title    = $current_object->name;
    $hero_subtitle = $current_object->description ? $current_object->description : sprintf(__('Esplora la collezione %s con opere selezionate e curate con passione', 'marcello-scavo-tattoo'), strtolower($current_object->name));
    $hero_cta_text = __('Scopri la Collezione', 'marcello-scavo-tattoo');
    $hero_cta_link = '#collezione';
} elseif (is_singular('portfolio')) {
    // Single Portfolio (Project Detail)
    $context_type  = 'single';
    $hero_title    = get_the_title();
    $hero_subtitle = has_excerpt() ? get_the_excerpt() : __('Dettagli del progetto e processo creativo', 'marcello-scavo-tattoo');
    $hero_cta_text = __('Scopri il Progetto', 'marcello-scavo-tattoo');
    $hero_cta_link = '#project-details';
}

// Portfolio stats for archive/taxonomy
$show_stats = in_array($context_type, array('archive', 'taxonomy'));
?>

<section class="portfolio-hero portfolio-hero--<?php echo esc_attr($context_type); ?>">
    <div class="portfolio-hero__background"></div>
    <div class="portfolio-hero__overlay"></div>

    <div class="container">
        <!-- Breadcrumb Navigation -->
        <?php if ($context_type !== 'archive') : ?>
            <nav class="portfolio-breadcrumb" aria-label="<?php esc_attr_e('Navigazione Portfolio', 'marcello-scavo-tattoo'); ?>">
                <ul class="breadcrumb-list">
                    <li class="breadcrumb-item">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumb-link">
                            <i class="fas fa-home" aria-hidden="true"></i>
                            <span class="sr-only"><?php esc_html_e('Home', 'marcello-scavo-tattoo'); ?></span>
                        </a>
                    </li>
                    <?php if ($context_type === 'taxonomy') : ?>
                        <li class="breadcrumb-item breadcrumb-item--current" aria-current="page">
                            <?php echo esc_html($current_object->name); ?>
                        </li>
                    <?php elseif ($context_type === 'single') : ?>
                        <?php
                        $categories = get_the_terms(get_the_ID(), 'portfolio_category');
                        if ($categories && ! is_wp_error($categories)) :
                            $main_category = reset($categories);
                        ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo esc_url(get_term_link($main_category)); ?>" class="breadcrumb-link">
                                    <?php echo esc_html($main_category->name); ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="breadcrumb-item breadcrumb-item--current" aria-current="page">
                            <?php echo esc_html(wp_trim_words(get_the_title(), 4)); ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <!-- Hero Content -->
        <div class="portfolio-hero__content">
            <div class="hero-content__inner">
                <h1 class="portfolio-hero__title">
                    <?php echo esc_html($hero_title); ?>
                </h1>

                <div class="portfolio-hero__divider">
                    <span class="divider-line"></span>
                    <span class="divider-icon">
                        <?php if ($context_type === 'single') : ?>
                            <i class="fas fa-star" aria-hidden="true"></i>
                        <?php else : ?>
                            <i class="fas fa-palette" aria-hidden="true"></i>
                        <?php endif; ?>
                    </span>
                    <span class="divider-line"></span>
                </div>

                <p class="portfolio-hero__subtitle">
                    <?php echo esc_html($hero_subtitle); ?>
                </p>

                <?php if ($hero_cta_link) : ?>
                    <div class="portfolio-hero__actions">
                        <a href="<?php echo esc_url($hero_cta_link); ?>" class="btn btn--hero btn--outline-light">
                            <span class="btn__text"><?php echo esc_html($hero_cta_text); ?></span>
                            <i class="btn__icon fas fa-arrow-down" aria-hidden="true"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Portfolio Statistics (Archive/Taxonomy only) -->
            <?php if ($show_stats) : ?>
                <div class="portfolio-hero__stats">
                    <div class="stats-grid">
                        <?php
                        // Total portfolio count
                        $total_portfolio = wp_count_posts('portfolio');
                        $total_published = $total_portfolio->publish;

                        // Category-specific count for taxonomy
                        if ($context_type === 'taxonomy') {
                            $term_count = $current_object->count;
                        }
                        ?>

                        <div class="stat-item">
                            <span class="stat-item__number">
                                <?php echo esc_html($context_type === 'taxonomy' ? $term_count : $total_published); ?>
                            </span>
                            <span class="stat-item__label">
                                <?php
                                if ($context_type === 'taxonomy') {
                                    esc_html_e('Progetti in categoria', 'marcello-scavo-tattoo');
                                } else {
                                    esc_html_e('Progetti Totali', 'marcello-scavo-tattoo');
                                }
                                ?>
                            </span>
                        </div>

                        <?php if ($context_type === 'archive') : ?>
                            <?php
                            // Count different project types
                            $tattoo_count = new WP_Query(array(
                                'post_type'      => 'portfolio',
                                'meta_query'     => array(
                                    array(
                                        'key'     => '_portfolio_project_type',
                                        'value'   => 'tattoo',
                                        'compare' => '=',
                                    ),
                                ),
                                'posts_per_page' => -1,
                            ));

                            $categories_count = wp_count_terms(array(
                                'taxonomy'   => 'portfolio_category',
                                'hide_empty' => true,
                            ));
                            ?>

                            <div class="stat-item">
                                <span class="stat-item__number"><?php echo esc_html($tattoo_count->found_posts); ?></span>
                                <span class="stat-item__label"><?php esc_html_e('Tatuaggi', 'marcello-scavo-tattoo'); ?></span>
                            </div>

                            <div class="stat-item">
                                <span class="stat-item__number"><?php echo esc_html($categories_count); ?></span>
                                <span class="stat-item__label"><?php esc_html_e('Categorie', 'marcello-scavo-tattoo'); ?></span>
                            </div>

                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <?php if ($hero_cta_link && strpos($hero_cta_link, '#') === 0) : ?>
        <div class="portfolio-hero__scroll-indicator">
            <div class="scroll-arrow">
                <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </div>
        </div>
    <?php endif; ?>
</section>