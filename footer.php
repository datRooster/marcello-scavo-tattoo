    </main><!-- #primary -->

    <footer class="site-footer">
        <?php 
        $footer_layout = get_theme_mod('footer_layout_type', 'three_columns');
        $social_style = get_theme_mod('footer_social_style', 'modern');
        ?>
        
        <div class="container">
            <!-- Footer Content -->
            <div class="footer-content footer-layout-<?php echo esc_attr($footer_layout); ?>">
                
                <!-- DEBUG: Mostra quante sezioni vengono create -->
                <?php echo "<!-- Layout attivo: $footer_layout -->"; ?>
                
                <!-- Footer Widget Area 1 -->
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <div class="footer-section footer-widget-1">
                        <?php dynamic_sidebar('footer-1'); ?>
                    </div>
                <?php else : ?>
                    <!-- Default content for Footer 1 if no widgets -->
                    <div class="footer-section footer-about">
                        <h3><?php _e('Marcello Scavo', 'marcello-scavo-tattoo'); ?></h3>
                        <p><?php _e('Artista del tatuaggio con base a Milano e Messina. Ogni tatuaggio è un\'opera d\'arte unica che racconta una storia.', 'marcello-scavo-tattoo'); ?></p>
                        
                        <?php 
                        // Usa il nuovo sistema di social links
                        echo marcello_scavo_get_social_links($social_style);
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Footer Widget Area 2 -->
                <?php if ($footer_layout !== 'one_column') : ?>
                    <?php if (is_active_sidebar('footer-2')) : ?>
                        <div class="footer-section footer-widget-2">
                            <?php dynamic_sidebar('footer-2'); ?>
                        </div>
                    <?php else : ?>
                        <!-- Default content for Footer 2 if no widgets -->
                        <div class="footer-section footer-links">
                            <h3><?php _e('Link Rapidi', 'marcello-scavo-tattoo'); ?></h3>
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'footer',
                                'menu_class'     => 'footer-menu',
                                'container'      => false,
                                'fallback_cb'    => 'marcello_scavo_footer_menu_fallback',
                            ));
                            ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Footer Widget Area 3 -->
                <?php if (in_array($footer_layout, ['three_columns', 'four_columns'])) : ?>
                    <?php if (is_active_sidebar('footer-3')) : ?>
                        <div class="footer-section footer-widget-3">
                            <?php dynamic_sidebar('footer-3'); ?>
                        </div>
                    <?php else : ?>
                        <!-- Default content for Footer 3 if no widgets -->
                        <div class="footer-section footer-contact">
                            <h3><?php _e('Contatti', 'marcello-scavo-tattoo'); ?></h3>
                            
                            <div class="contact-location">
                                <h4><i class="fas fa-map-marker-alt"></i> <?php _e('Milano', 'marcello-scavo-tattoo'); ?></h4>
                                <p><?php echo nl2br(esc_html(get_theme_mod('contact_milano_address', 'Via Example, 123\n20121 Milano (MI)'))); ?></p>
                            </div>
                            
                            <div class="contact-location">
                                <h4><i class="fas fa-map-marker-alt"></i> <?php _e('Messina', 'marcello-scavo-tattoo'); ?></h4>
                                <p><?php echo nl2br(esc_html(get_theme_mod('contact_messina_address', 'Via Example, 456\n98121 Messina (ME)'))); ?></p>
                            </div>
                            
                            <div class="contact-details">
                                <p><i class="fas fa-phone"></i> <a href="tel:<?php echo esc_attr(str_replace(' ', '', get_theme_mod('contact_phone', '+39123456789'))); ?>"><?php echo esc_html(get_theme_mod('contact_phone', '+39 123 456 7890')); ?></a></p>
                                <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo esc_attr(get_theme_mod('contact_email', 'info@marcelloscavo.com')); ?>"><?php echo esc_html(get_theme_mod('contact_email', 'info@marcelloscavo.com')); ?></a></p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Footer Widget Area 4 (only for four columns layout) -->
                <?php if ($footer_layout === 'four_columns') : ?>
                    <?php if (is_active_sidebar('footer-4')) : ?>
                        <div class="footer-section footer-widget-4">
                            <?php dynamic_sidebar('footer-4'); ?>
                        </div>
                    <?php else : ?>
                        <!-- Default content for Footer 4 if no widgets -->
                        <div class="footer-section footer-extra">
                            <h3><?php _e('Seguici', 'marcello-scavo-tattoo'); ?></h3>
                            <?php echo marcello_scavo_get_social_links($social_style); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
            </div>

            <!-- Copyright -->
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <div class="copyright">
                        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('Tutti i diritti riservati.', 'marcello-scavo-tattoo'); ?></p>
                    </div>
                    
                    <div class="footer-bottom-links">
                        <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php _e('Privacy Policy', 'marcello-scavo-tattoo'); ?></a>
                        <span class="separator">|</span>
                        <a href="<?php echo esc_url(home_url('/terms-of-service')); ?>"><?php _e('Termini di Servizio', 'marcello-scavo-tattoo'); ?></a>
                        <span class="separator">|</span>
                        <a href="#" onclick="scrollToTop()"><?php _e('Torna su', 'marcello-scavo-tattoo'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="back-to-top" aria-label="<?php _e('Torna su', 'marcello-scavo-tattoo'); ?>">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p><?php _e('Caricamento...', 'marcello-scavo-tattoo'); ?></p>
        </div>
    </div>

</div><!-- #page -->

<?php wp_footer(); ?>

<script>
// Utility functions
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Language switcher function
function marcelloScavoChangeLanguage(lang) {
    // This would integrate with a multilingual plugin
    console.log('Language changed to:', lang);
    // Implementation depends on the multilingual plugin used
}

// Show/hide back to top button
window.addEventListener('scroll', function() {
    const backToTopBtn = document.getElementById('back-to-top');
    if (window.pageYOffset > 300) {
        backToTopBtn.classList.add('show');
    } else {
        backToTopBtn.classList.remove('show');
    }
});

// Back to top button click handler
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('back-to-top');
    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', scrollToTop);
    }
    
    // Gallery Modal Functionality
    const galleryBtn = document.getElementById('gallery-btn');
    const galleryModal = document.getElementById('gallery-modal');
    const galleryClose = document.querySelector('.gallery-close');
    const galleryTabs = document.querySelectorAll('.gallery-tab');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    // Open modal
    if (galleryBtn) {
        galleryBtn.addEventListener('click', function() {
            galleryModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close modal
    if (galleryClose) {
        galleryClose.addEventListener('click', closeModal);
    }
    
    // Close modal when clicking outside
    galleryModal?.addEventListener('click', function(e) {
        if (e.target === galleryModal) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && galleryModal.style.display === 'block') {
            closeModal();
        }
    });
    
    function closeModal() {
        galleryModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Tab filtering
    galleryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active tab
            galleryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter gallery items
            galleryItems.forEach(item => {
                if (category === 'all' || item.getAttribute('data-category') === category) {
                    item.style.display = 'block';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, 10);
                } else {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
    
    // Gallery item animations
    galleryItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

</body>
</html>

<?php
/**
 * Footer menu fallback
 */
function marcello_scavo_footer_menu_fallback() {
    ?>
    <ul class="footer-menu">
        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'marcello-scavo-tattoo'); ?></a></li>
        <li><a href="#about"><?php _e('About', 'marcello-scavo-tattoo'); ?></a></li>
        <li><a href="#portfolio"><?php _e('Portfolio', 'marcello-scavo-tattoo'); ?></a></li>
        <li><a href="#services"><?php _e('Servizi', 'marcello-scavo-tattoo'); ?></a></li>
        <li><a href="<?php echo get_post_type_archive_link('shop_product'); ?>"><?php _e('Shop', 'marcello-scavo-tattoo'); ?></a></li>
        <li><a href="<?php echo get_permalink(get_option('page_for_posts')); ?>"><?php _e('Blog', 'marcello-scavo-tattoo'); ?></a></li>
        <li><a href="#contact"><?php _e('Contatti', 'marcello-scavo-tattoo'); ?></a></li>
    </ul>
    <?php
}
?>

<script>
// Funzione globale per aprire il visualizzatore di immagini
function openImageViewer(imageSrc, title, caption) {
    const modal = document.getElementById('image-viewer-modal');
    const img = document.getElementById('image-viewer-img');
    const titleEl = document.getElementById('image-viewer-title');
    const descEl = document.getElementById('image-viewer-description');
    
    if (!modal) return;
    
    // Imposta i contenuti
    if (img) img.src = imageSrc;
    if (titleEl) titleEl.textContent = title || '';
    if (descEl) descEl.textContent = caption || '';
    
    // Mostra il modal
    modal.classList.add('show');
    modal.style.display = 'flex';
    modal.style.zIndex = '10000';
    document.body.classList.add('modal-open');
}

// Funzione per chiudere il modal
function closeImageViewer() {
    const modal = document.getElementById('image-viewer-modal');
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
}

// Event listeners per il modal
document.addEventListener('DOMContentLoaded', function() {
    const closeBtn = document.querySelector('.image-viewer-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeImageViewer);
    }
    
    document.addEventListener('keyup', function(e) {
        if (e.keyCode === 27) closeImageViewer();
    });
    
    const modal = document.getElementById('image-viewer-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeImageViewer();
        });
    }
});
</script>
