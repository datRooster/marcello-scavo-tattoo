<?php

/**
 * Portfolio Navigation Component - Enhanced Version
 *
 * Provides contextual navigation based on current page with improved breadcrumbs and navigation cards
 */

global $wp_query;
$nav_links = array();
$current_context = '';

// Generate breadcrumb based on context
$breadcrumb_items = array();

// Start with home
$breadcrumb_items[] = array(
    'url'   => home_url('/'),
    'label' => __('Home', 'marcello-scavo-tattoo'),
    'icon'  => 'fas fa-home'
);

if (is_post_type_archive('portfolio')) {
    $current_context = 'archive';

    $breadcrumb_items[] = array(
        'url'   => get_post_type_archive_link('portfolio'),
        'label' => __('Portfolio', 'marcello-scavo-tattoo'),
        'icon'  => 'fas fa-image',
        'current' => true
    );

    // Get portfolio categories for filtering
    $categories = get_terms(array(
        'taxonomy'   => 'portfolio_category',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC'
    ));

    if ($categories && ! is_wp_error($categories)) {
        foreach ($categories as $category) {
            $nav_links[$category->slug] = array(
                'url'   => get_term_link($category),
                'label' => $category->name,
                'icon'  => 'fas fa-filter',
                'count' => $category->count,
                'description' => sprintf(__('Filtra %d progetti', 'marcello-scavo-tattoo'), $category->count)
            );
        }
    }
} elseif (is_tax('portfolio_category')) {
    $current_context = 'taxonomy';
    $current_term = get_queried_object();

    $breadcrumb_items[] = array(
        'url'   => get_post_type_archive_link('portfolio'),
        'label' => __('Portfolio', 'marcello-scavo-tattoo'),
        'icon'  => 'fas fa-image'
    );

    $breadcrumb_items[] = array(
        'url'   => '',
        'label' => $current_term->name,
        'icon'  => 'fas fa-folder-open',
        'current' => true
    );

    // Add "back to all" link
    $nav_links['archive'] = array(
        'url'   => get_post_type_archive_link('portfolio'),
        'label' => __('Tutto il Portfolio', 'marcello-scavo-tattoo'),
        'icon'  => 'fas fa-th-large',
        'description' => __('Torna alla vista completa del portfolio', 'marcello-scavo-tattoo')
    );

    // Get other categories
    $other_categories = get_terms(array(
        'taxonomy'   => 'portfolio_category',
        'hide_empty' => true,
        'exclude'    => array($current_term->term_id),
        'orderby'    => 'count',
        'order'      => 'DESC',
        'number'     => 4,
    ));

    if ($other_categories && ! is_wp_error($other_categories)) {
        foreach ($other_categories as $category) {
            $nav_links[$category->slug] = array(
                'url'   => get_term_link($category),
                'label' => $category->name,
                'icon'  => 'fas fa-folder',
                'count' => $category->count,
                'description' => sprintf(__('Esplora %d progetti simili', 'marcello-scavo-tattoo'), $category->count)
            );
        }
    }
} elseif (is_singular('portfolio')) {
    $current_context = 'single';

    $breadcrumb_items[] = array(
        'url'   => get_post_type_archive_link('portfolio'),
        'label' => __('Portfolio', 'marcello-scavo-tattoo'),
        'icon'  => 'fas fa-image'
    );

    // Get current project's category
    $categories = get_the_terms(get_the_ID(), 'portfolio_category');
    if ($categories && ! is_wp_error($categories)) {
        $main_category = reset($categories);

        $breadcrumb_items[] = array(
            'url'   => get_term_link($main_category),
            'label' => $main_category->name,
            'icon'  => 'fas fa-folder'
        );

        $nav_links['category'] = array(
            'url'   => get_term_link($main_category),
            'label' => $main_category->name,
            'icon'  => 'fas fa-folder',
            'count' => $main_category->count,
            'description' => sprintf(__('%d progetti in questa categoria', 'marcello-scavo-tattoo'), $main_category->count)
        );
    }

    $breadcrumb_items[] = array(
        'url'   => '',
        'label' => get_the_title(),
        'icon'  => 'fas fa-image',
        'current' => true
    );

    $nav_links['archive'] = array(
        'url'   => get_post_type_archive_link('portfolio'),
        'label' => __('Portfolio Completo', 'marcello-scavo-tattoo'),
        'icon'  => 'fas fa-th-large',
        'description' => __('Visualizza tutti i progetti', 'marcello-scavo-tattoo')
    );
}

// Only show navigation if we have links
if (empty($nav_links) && empty($breadcrumb_items)) {
    return;
}
?>

<section class="portfolio-navigation">
    <div class="container">
        <!-- Enhanced Breadcrumb -->
        <?php if (!empty($breadcrumb_items)) : ?>
            <nav class="portfolio-breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb Navigation', 'marcello-scavo-tattoo'); ?>">
                <ol class="breadcrumb-list">
                    <?php foreach ($breadcrumb_items as $index => $item) : ?>
                        <li class="breadcrumb-item <?php echo isset($item['current']) ? 'current' : ''; ?>">
                            <?php if (isset($item['current'])) : ?>
                                <span class="breadcrumb-current">
                                    <i class="<?php echo esc_attr($item['icon']); ?>" aria-hidden="true"></i>
                                    <?php echo esc_html($item['label']); ?>
                                </span>
                            <?php else : ?>
                                <a href="<?php echo esc_url($item['url']); ?>" class="breadcrumb-link">
                                    <i class="<?php echo esc_attr($item['icon']); ?>" aria-hidden="true"></i>
                                    <?php echo esc_html($item['label']); ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($index < count($breadcrumb_items) - 1) : ?>
                                <i class="breadcrumb-separator fas fa-chevron-right" aria-hidden="true"></i>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>

        <!-- Navigation Cards -->
        <?php if (!empty($nav_links)) : ?>
            <div class="portfolio-nav">
                <div class="portfolio-nav__header">
                    <h3 class="portfolio-nav__title">
                        <?php
                        switch ($current_context) {
                            case 'page':
                                esc_html_e('Esplora per Categoria', 'marcello-scavo-tattoo');
                                break;
                            case 'archive':
                                esc_html_e('Filtra per Tipologia', 'marcello-scavo-tattoo');
                                break;
                            case 'taxonomy':
                                esc_html_e('Altre Collezioni', 'marcello-scavo-tattoo');
                                break;
                            case 'single':
                                esc_html_e('Portfolio Correlato', 'marcello-scavo-tattoo');
                                break;
                        }
                        ?>
                    </h3>
                    <p class="portfolio-nav__subtitle">
                        <?php
                        switch ($current_context) {
                            case 'page':
                                esc_html_e('Scegli una categoria per vedere i progetti specifici', 'marcello-scavo-tattoo');
                                break;
                            case 'archive':
                                esc_html_e('Filtra i progetti per stile e tipologia di tatuaggio', 'marcello-scavo-tattoo');
                                break;
                            case 'taxonomy':
                                esc_html_e('Scopri altre categorie e stili artistici', 'marcello-scavo-tattoo');
                                break;
                            case 'single':
                                esc_html_e('Torna alla categoria o esplora tutto il portfolio', 'marcello-scavo-tattoo');
                                break;
                        }
                        ?>
                    </p>
                </div>

                <div class="portfolio-nav__grid">
                    <?php foreach ($nav_links as $key => $link) : ?>
                        <article class="nav-card">
                            <a href="<?php echo esc_url($link['url']); ?>" class="nav-card__link">
                                <div class="nav-card__icon">
                                    <i class="<?php echo esc_attr($link['icon']); ?>" aria-hidden="true"></i>
                                </div>
                                <div class="nav-card__content">
                                    <h4 class="nav-card__title"><?php echo esc_html($link['label']); ?></h4>
                                    <?php if (isset($link['description'])) : ?>
                                        <p class="nav-card__description"><?php echo esc_html($link['description']); ?></p>
                                    <?php endif; ?>
                                    <?php if (isset($link['count'])) : ?>
                                        <span class="nav-card__count">
                                            <?php
                                            /* translators: %d is the number of projects */
                                            echo esc_html(sprintf(_n('%d progetto', '%d progetti', $link['count'], 'marcello-scavo-tattoo'), $link['count']));
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="nav-card__arrow">
                                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>