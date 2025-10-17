<?php
/**
 * The template for displaying single blog posts
 * 
 * @package MarcelloScavoTattoo
 */

get_header(); ?>

<div class="single-post-content">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Post Header -->
                    <article id="post-<?php the_ID(); ?>" <?php post_class('blog-post'); ?>>
                        <header class="post-header section" style="padding-top: calc(var(--spacing-xl) + 80px);">
                            <div class="post-header-content">
                                <!-- Post Meta -->
                                <div class="post-meta">
                                    <span class="post-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo get_the_date(); ?>
                                    </span>
                                    
                                    <span class="post-author">
                                        <i class="fas fa-user"></i>
                                        <?php _e('di', 'marcello-scavo'); ?> <?php the_author(); ?>
                                    </span>
                                    
                                    <?php if (has_category()) : ?>
                                        <span class="post-categories">
                                            <i class="fas fa-folder"></i>
                                            <?php the_category(', '); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <span class="reading-time">
                                        <i class="fas fa-clock"></i>
                                        <?php echo marcello_scavo_reading_time(); ?> <?php _e('min di lettura', 'marcello-scavo'); ?>
                                    </span>
                                </div>
                                
                                <!-- Post Title -->
                                <h1 class="post-title"><?php the_title(); ?></h1>
                                
                                <!-- Post Excerpt -->
                                <?php if (has_excerpt()) : ?>
                                    <div class="post-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </header>
                
                <!-- Featured Image -->
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-featured-image">
                        <div class="container">
                            <div class="featured-image-container">
                                <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Post Content -->
                <div class="post-content section">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <div class="post-body">
                                    <?php
                                    the_content();
                                    
                                    wp_link_pages(array(
                                        'before' => '<div class="page-links">' . __('Pagine:', 'marcello-scavo'),
                                        'after'  => '</div>',
                                    ));
                                    ?>
                                </div>
                                
                                <!-- Post Tags -->
                                <?php if (has_tag()) : ?>
                                    <div class="post-tags">
                                        <h4><?php _e('Tag:', 'marcello-scavo'); ?></h4>
                                        <div class="tags-list">
                                            <?php the_tags('', ' ', ''); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Social Share -->
                                <div class="post-share">
                                    <h4><?php _e('Condividi questo articolo:', 'marcello-scavo'); ?></h4>
                                    <div class="share-buttons">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener" class="share-btn facebook">
                                            <i class="fab fa-facebook-f"></i>
                                            <span>Facebook</span>
                                        </a>
                                        
                                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener" class="share-btn twitter">
                                            <i class="fab fa-twitter"></i>
                                            <span>Twitter</span>
                                        </a>
                                        
                                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener" class="share-btn linkedin">
                                            <i class="fab fa-linkedin-in"></i>
                                            <span>LinkedIn</span>
                                        </a>
                                        
                                        <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' - ' . get_permalink()); ?>" target="_blank" rel="noopener" class="share-btn whatsapp">
                                            <i class="fab fa-whatsapp"></i>
                                            <span>WhatsApp</span>
                                        </a>
                                        
                                        <button class="share-btn copy-link" onclick="copyToClipboard('<?php echo get_permalink(); ?>')">
                                            <i class="fas fa-link"></i>
                                            <span><?php _e('Copia Link', 'marcello-scavo'); ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Author Bio -->
                <div class="author-bio section" style="background-color: var(--light-gray);">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <div class="author-card">
                                    <div class="author-avatar">
                                        <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                                    </div>
                                    <div class="author-info">
                                        <h3><?php the_author(); ?></h3>
                                        <p class="author-description">
                                            <?php 
                                            $author_bio = get_the_author_meta('description');
                                            if ($author_bio) {
                                                echo esc_html($author_bio);
                                            } else {
                                                _e('Artista del tatuaggio con base a Milano e Messina. Ogni tatuaggio è un\'opera d\'arte unica che racconta una storia.', 'marcello-scavo');
                                            }
                                            ?>
                                        </p>
                                        <div class="author-social">
                                            <a href="#" class="social-link" target="_blank" rel="noopener">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                            <a href="#" class="social-link" target="_blank" rel="noopener">
                                                <i class="fab fa-facebook"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation -->
                <div class="post-navigation section">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $prev_post = get_previous_post();
                                if ($prev_post) :
                                ?>
                                    <div class="nav-post nav-prev">
                                        <span class="nav-label"><?php _e('Articolo Precedente', 'marcello-scavo'); ?></span>
                                        <h4><a href="<?php echo get_permalink($prev_post); ?>"><?php echo get_the_title($prev_post); ?></a></h4>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php
                                $next_post = get_next_post();
                                if ($next_post) :
                                ?>
                                    <div class="nav-post nav-next">
                                        <span class="nav-label"><?php _e('Articolo Successivo', 'marcello-scavo'); ?></span>
                                        <h4><a href="<?php echo get_permalink($next_post); ?>"><?php echo get_the_title($next_post); ?></a></h4>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Related Posts -->
                <div class="related-posts section" style="background-color: var(--light-gray);">
                    <div class="container">
                        <h3 class="text-center"><?php _e('Articoli Correlati', 'marcello-scavo'); ?></h3>
                        
                        <?php
                        $categories = get_the_category();
                        if ($categories) {
                            $category_ids = array();
                            foreach ($categories as $category) {
                                $category_ids[] = $category->term_id;
                            }
                            
                            $related_args = array(
                                'category__in' => $category_ids,
                                'post__not_in' => array(get_the_ID()),
                                'posts_per_page' => 3,
                                'post_status' => 'publish'
                            );
                            
                            $related_query = new WP_Query($related_args);
                            
                            if ($related_query->have_posts()) :
                            ?>
                                <div class="blog-grid">
                                    <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                                        <article class="blog-item">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <div class="blog-thumbnail">
                                                    <a href="<?php the_permalink(); ?>">
                                                        <?php the_post_thumbnail('medium'); ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <div class="blog-content">
                                                <div class="blog-meta">
                                                    <span class="blog-date"><?php echo get_the_date(); ?></span>
                                                </div>
                                                <h4 class="blog-title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h4>
                                                <div class="blog-excerpt">
                                                    <?php the_excerpt(); ?>
                                                </div>
                                                <a href="<?php the_permalink(); ?>" class="blog-read-more">
                                                    <?php _e('Leggi di più', 'marcello-scavo'); ?> <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </article>
                                    <?php endwhile; ?>
                                </div>
                            <?php
                                wp_reset_postdata();
                            endif;
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Comments -->
                <?php if (comments_open() || get_comments_number()) : ?>
                    <div class="post-comments section">
                        <div class="comments-content">
                            <?php comments_template(); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            </article>
            
            <?php endwhile; ?>
            </div><!-- .col-lg-8 -->
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <?php get_sidebar(); ?>
            </div>
            
        </div><!-- .row -->
    </div><!-- .container -->
</div><!-- .single-post-content -->

<style>
/* Single Post Styles */
.single-post-content {
    padding-top: 0;
}

.post-header {
    text-align: center;
}

.post-meta {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
    font-size: 0.9rem;
    color: var(--medium-gray);
}

.post-meta span {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.post-meta i {
    color: var(--primary-gold);
}

.post-title {
    font-size: 3rem;
    margin-bottom: var(--spacing-md);
    color: var(--primary-blue);
    line-height: 1.2;
}

.post-excerpt {
    font-size: 1.2rem;
    color: var(--medium-gray);
    line-height: 1.6;
    font-style: italic;
}

.post-featured-image {
    margin: var(--spacing-lg) 0;
}

.featured-image-container {
    text-align: center;
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.featured-image-container img {
    width: 100%;
    max-width: 800px;
    height: auto;
}

.post-body {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--dark-gray);
}

.post-body h2,
.post-body h3,
.post-body h4 {
    margin-top: var(--spacing-lg);
    margin-bottom: var(--spacing-md);
    color: var(--primary-blue);
}

.post-body blockquote {
    border-left: 4px solid var(--primary-gold);
    padding: var(--spacing-md);
    margin: var(--spacing-lg) 0;
    background: var(--light-gray);
    font-style: italic;
    border-radius: 0 var(--border-radius) var(--border-radius) 0;
}

.post-tags {
    margin-top: var(--spacing-lg);
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--light-gray);
}

.post-tags h4 {
    color: var(--primary-blue);
    margin-bottom: var(--spacing-sm);
}

.tags-list a {
    display: inline-block;
    background: var(--primary-blue);
    color: var(--white);
    padding: var(--spacing-xs) var(--spacing-sm);
    margin: var(--spacing-xs);
    border-radius: var(--border-radius);
    text-decoration: none;
    font-size: 0.9rem;
    transition: background 0.3s ease;
}

.tags-list a:hover {
    background: var(--secondary-blue);
}

.post-share {
    margin-top: var(--spacing-lg);
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--light-gray);
}

.post-share h4 {
    color: var(--primary-blue);
    margin-bottom: var(--spacing-md);
}

.share-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-sm);
}

.share-btn {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-xs) var(--spacing-sm);
    border: none;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.share-btn.facebook {
    background: #1877f2;
    color: white;
}

.share-btn.twitter {
    background: #1da1f2;
    color: white;
}

.share-btn.linkedin {
    background: #0077b5;
    color: white;
}

.share-btn.whatsapp {
    background: #25d366;
    color: white;
}

.share-btn.copy-link {
    background: var(--medium-gray);
    color: white;
}

.share-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.author-bio {
    text-align: center;
}

.author-card {
    background: var(--white);
    padding: var(--spacing-lg);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    text-align: left;
}

.author-avatar {
    flex-shrink: 0;
}

.author-avatar img {
    border-radius: 50%;
    border: 3px solid var(--primary-gold);
}

.author-info h3 {
    color: var(--primary-blue);
    margin-bottom: var(--spacing-sm);
}

.author-description {
    margin-bottom: var(--spacing-md);
    line-height: 1.6;
}

.author-social {
    display: flex;
    gap: var(--spacing-sm);
}

.author-social .social-link {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.post-navigation {
    border-top: 1px solid var(--light-gray);
    border-bottom: 1px solid var(--light-gray);
}

.nav-post {
    padding: var(--spacing-md);
    background: var(--white);
    border-radius: var(--border-radius);
    border: 1px solid var(--light-gray);
    transition: all 0.3s ease;
}

.nav-post:hover {
    box-shadow: var(--shadow-sm);
    transform: translateY(-2px);
}

.nav-label {
    display: block;
    font-size: 0.9rem;
    color: var(--medium-gray);
    margin-bottom: var(--spacing-xs);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.nav-post h4 {
    margin: 0;
}

.nav-post a {
    color: var(--primary-blue);
    text-decoration: none;
    transition: color 0.3s ease;
}

.nav-post a:hover {
    color: var(--secondary-blue);
}

.nav-next {
    text-align: right;
}

.page-links {
    margin: var(--spacing-lg) 0;
    text-align: center;
}

.page-links a {
    display: inline-block;
    padding: var(--spacing-xs) var(--spacing-sm);
    margin: 0 var(--spacing-xs);
    background: var(--primary-blue);
    color: var(--white);
    text-decoration: none;
    border-radius: var(--border-radius);
    transition: background 0.3s ease;
}

.page-links a:hover {
    background: var(--secondary-blue);
}

@media (max-width: 768px) {
    .post-title {
        font-size: 2rem;
    }
    
    .post-meta {
        flex-direction: column;
        align-items: center;
        gap: var(--spacing-sm);
    }
    
    .author-card {
        flex-direction: column;
        text-align: center;
    }
    
    .nav-next {
        text-align: left;
        margin-top: var(--spacing-md);
    }
    
    .share-buttons {
        justify-content: center;
    }
}
</style>

<script>
function copyToClipboard(url) {
    navigator.clipboard.writeText(url).then(function() {
        // Show success notification
        if (typeof showNotification === 'function') {
            showNotification('Link copiato negli appunti!', 'success');
        } else {
            alert('Link copiato negli appunti!');
        }
    }).catch(function(err) {
        console.error('Errore nella copia: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        if (typeof showNotification === 'function') {
            showNotification('Link copiato negli appunti!', 'success');
        } else {
            alert('Link copiato negli appunti!');
        }
    });
}
</script>

<?php 
/**
 * Calculate reading time
 */
function marcello_scavo_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Assuming 200 words per minute
    return $reading_time;
}

get_footer(); ?>
