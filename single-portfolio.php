<?php
/**
 * The template for displaying single portfolio items
 * 
 * @package MarcelloScavoTattoo
 */

get_header(); ?>

<div class="container">
    <div class="portfolio-single-content">
        <?php while (have_posts()) : the_post(); ?>
            
            <!-- Portfolio Header -->
            <div class="portfolio-header section">
                <div class="row">
                    <div class="col-md-8">
                        <h1 class="portfolio-title"><?php the_title(); ?></h1>
                        <div class="portfolio-meta">
                            <?php
                            $client_name = get_post_meta(get_the_ID(), '_portfolio_client_name', true);
                            $project_date = get_post_meta(get_the_ID(), '_portfolio_project_date', true);
                            $project_location = get_post_meta(get_the_ID(), '_portfolio_project_location', true);
                            $project_type = get_post_meta(get_the_ID(), '_portfolio_project_type', true);
                            $project_duration = get_post_meta(get_the_ID(), '_portfolio_project_duration', true);
                            ?>
                            
                            <div class="meta-grid">
                                <?php if ($client_name) : ?>
                                <div class="meta-item">
                                    <span class="meta-label"><?php _e('Cliente:', 'marcello-scavo-tattoo'); ?></span>
                                    <span class="meta-value"><?php echo esc_html($client_name); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($project_date) : ?>
                                <div class="meta-item">
                                    <span class="meta-label"><?php _e('Data:', 'marcello-scavo-tattoo'); ?></span>
                                    <span class="meta-value"><?php echo esc_html(date('F Y', strtotime($project_date))); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($project_location) : ?>
                                <div class="meta-item">
                                    <span class="meta-label"><?php _e('Luogo:', 'marcello-scavo-tattoo'); ?></span>
                                    <span class="meta-value"><?php echo esc_html(ucfirst($project_location)); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($project_type) : ?>
                                <div class="meta-item">
                                    <span class="meta-label"><?php _e('Tipo:', 'marcello-scavo-tattoo'); ?></span>
                                    <span class="meta-value">
                                        <?php 
                                        $type_labels = array(
                                            'tattoo' => __('Tatuaggio', 'marcello-scavo-tattoo'),
                                            'illustration' => __('Illustrazione', 'marcello-scavo-tattoo'),
                                            'graphic_design' => __('Grafica', 'marcello-scavo-tattoo')
                                        );
                                        echo esc_html($type_labels[$project_type] ?? $project_type);
                                        ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($project_duration) : ?>
                                <div class="meta-item">
                                    <span class="meta-label"><?php _e('Durata:', 'marcello-scavo-tattoo'); ?></span>
                                    <span class="meta-value"><?php echo esc_html($project_duration); ?> <?php _e('ore', 'marcello-scavo-tattoo'); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Portfolio Categories and Tags -->
                            <div class="portfolio-taxonomy">
                                <?php
                                $categories = get_the_terms(get_the_ID(), 'portfolio_category');
                                if ($categories && !is_wp_error($categories)) :
                                ?>
                                    <div class="portfolio-categories">
                                        <span class="tax-label"><?php _e('Categorie:', 'marcello-scavo-tattoo'); ?></span>
                                        <?php foreach ($categories as $category) : ?>
                                            <a href="<?php echo get_term_link($category); ?>" class="category-tag"><?php echo esc_html($category->name); ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php
                                $tags = get_the_terms(get_the_ID(), 'portfolio_tag');
                                if ($tags && !is_wp_error($tags)) :
                                ?>
                                    <div class="portfolio-tags">
                                        <span class="tax-label"><?php _e('Tag:', 'marcello-scavo-tattoo'); ?></span>
                                        <?php foreach ($tags as $tag) : ?>
                                            <a href="<?php echo get_term_link($tag); ?>" class="portfolio-tag"><?php echo esc_html($tag->name); ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="portfolio-actions">
                            <a href="#booking" class="btn btn-primary btn-lg">
                                <i class="fas fa-calendar-plus"></i> <?php _e('Prenota Simile', 'marcello-scavo-tattoo'); ?>
                            </a>
                            <a href="<?php echo get_post_type_archive_link('portfolio'); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> <?php _e('Torna al Portfolio', 'marcello-scavo-tattoo'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Portfolio Gallery -->
            <div class="portfolio-gallery section">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="main-image">
                        <?php the_post_thumbnail('portfolio-large', array('class' => 'img-fluid')); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Additional images could be added here with a custom field for gallery -->
            </div>
            
            <!-- Portfolio Description -->
            <?php if (get_the_content()) : ?>
            <div class="portfolio-description section">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <h2><?php _e('Descrizione del Progetto', 'marcello-scavo-tattoo'); ?></h2>
                        <div class="portfolio-content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Process/Story Section -->
            <div class="portfolio-process section" style="background-color: var(--light-gray);">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h3><?php _e('Il Processo Creativo', 'marcello-scavo-tattoo'); ?></h3>
                            <p><?php _e('Ogni tatuaggio inizia con un\'idea e si trasforma attraverso un processo di collaborazione tra artista e cliente.', 'marcello-scavo-tattoo'); ?></p>
                            
                            <div class="process-steps">
                                <div class="process-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4><?php _e('Consultazione', 'marcello-scavo-tattoo'); ?></h4>
                                        <p><?php _e('Discutiamo la tua idea, il significato e le preferenze stilistiche.', 'marcello-scavo-tattoo'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="process-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4><?php _e('Design', 'marcello-scavo-tattoo'); ?></h4>
                                        <p><?php _e('Creo un design personalizzato basato sulla nostra discussione.', 'marcello-scavo-tattoo'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="process-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h4><?php _e('Realizzazione', 'marcello-scavo-tattoo'); ?></h4>
                                        <p><?php _e('Realizziamo insieme il tatuaggio nel massimo comfort e sicurezza.', 'marcello-scavo-tattoo'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="cta-booking">
                                <h3><?php _e('Ti piace questo stile?', 'marcello-scavo-tattoo'); ?></h3>
                                <p><?php _e('Prenota una consultazione per discutere il tuo prossimo tatuaggio.', 'marcello-scavo-tattoo'); ?></p>
                                <a href="#booking" class="btn btn-primary btn-lg">
                                    <i class="fas fa-comments"></i> <?php _e('Inizia la Consultazione', 'marcello-scavo-tattoo'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Related Portfolio Items -->
            <div class="related-portfolio section">
                <div class="container">
                    <h3 class="text-center"><?php _e('Lavori Correlati', 'marcello-scavo-tattoo'); ?></h3>
                    
                    <?php
                    // Get related portfolio items
                    $related_args = array(
                        'post_type' => 'portfolio',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'meta_query' => array(
                            array(
                                'key' => '_portfolio_project_type',
                                'value' => $project_type,
                                'compare' => '='
                            )
                        )
                    );
                    
                    $related_query = new WP_Query($related_args);
                    
                    if ($related_query->have_posts()) :
                    ?>
                        <div class="portfolio-grid">
                            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                                <div class="portfolio-item">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('portfolio-thumb'); ?>
                                        </a>
                                    <?php endif; ?>
                                    <div class="portfolio-overlay">
                                        <div class="overlay-content">
                                            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                            <p><?php echo esc_html(get_post_meta(get_the_ID(), '_portfolio_project_location', true)); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php
                        wp_reset_postdata();
                    else :
                        // If no related items by type, show recent ones
                        $recent_args = array(
                            'post_type' => 'portfolio',
                            'posts_per_page' => 3,
                            'post__not_in' => array(get_the_ID())
                        );
                        $recent_query = new WP_Query($recent_args);
                        
                        if ($recent_query->have_posts()) :
                        ?>
                            <div class="portfolio-grid">
                                <?php while ($recent_query->have_posts()) : $recent_query->the_post(); ?>
                                    <div class="portfolio-item">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('portfolio-thumb'); ?>
                                            </a>
                                        <?php endif; ?>
                                        <div class="portfolio-overlay">
                                            <div class="overlay-content">
                                                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                                <p><?php echo esc_html(get_post_meta(get_the_ID(), '_portfolio_project_location', true)); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php
                            wp_reset_postdata();
                        endif;
                    endif;
                    ?>
                </div>
            </div>
            
        <?php endwhile; ?>
    </div>
</div>

<style>
/* Portfolio Single Styles */
.portfolio-single-content {
    padding-top: calc(var(--spacing-xl) + 80px);
}

.portfolio-header {
    border-bottom: 1px solid var(--light-gray);
}

.portfolio-title {
    font-size: 3rem;
    margin-bottom: var(--spacing-md);
    color: var(--primary-blue);
}

.meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
}

.meta-item {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.meta-label {
    font-weight: 600;
    color: var(--medium-gray);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.meta-value {
    font-weight: 500;
    color: var(--dark-gray);
}

.portfolio-taxonomy {
    margin-top: var(--spacing-md);
    padding-top: var(--spacing-md);
    border-top: 1px solid var(--light-gray);
}

.portfolio-categories,
.portfolio-tags {
    margin-bottom: var(--spacing-sm);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex-wrap: wrap;
}

.tax-label {
    font-weight: 600;
    color: var(--medium-gray);
    min-width: 80px;
}

.category-tag,
.portfolio-tag {
    background: var(--primary-blue);
    color: var(--white);
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--border-radius);
    text-decoration: none;
    font-size: 0.9rem;
    transition: background 0.3s ease;
}

.category-tag:hover,
.portfolio-tag:hover {
    background: var(--secondary-blue);
    color: var(--white);
}

.portfolio-tag {
    background: var(--primary-gold);
}

.portfolio-tag:hover {
    background: var(--secondary-gold);
}

.portfolio-actions {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
    align-items: flex-end;
}

.portfolio-gallery .main-image {
    text-align: center;
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.portfolio-gallery .main-image img {
    width: 100%;
    max-width: 800px;
    height: auto;
}

.portfolio-description {
    background: var(--white);
}

.portfolio-content {
    font-size: 1.1rem;
    line-height: 1.8;
}

.process-steps {
    margin-top: var(--spacing-md);
}

.process-step {
    display: flex;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
    align-items: flex-start;
}

.step-number {
    width: 50px;
    height: 50px;
    background: var(--primary-gold);
    color: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.step-content h4 {
    color: var(--primary-blue);
    margin-bottom: var(--spacing-xs);
}

.cta-booking {
    background: var(--white);
    padding: var(--spacing-lg);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    text-align: center;
    border-top: 4px solid var(--primary-gold);
}

.cta-booking h3 {
    color: var(--primary-blue);
    margin-bottom: var(--spacing-sm);
}

@media (max-width: 768px) {
    .portfolio-title {
        font-size: 2rem;
    }
    
    .meta-grid {
        grid-template-columns: 1fr;
    }
    
    .portfolio-actions {
        align-items: stretch;
        margin-top: var(--spacing-md);
    }
    
    .portfolio-categories,
    .portfolio-tags {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .tax-label {
        min-width: auto;
    }
}
</style>

<?php get_footer(); ?>
