<?php
/**
 * The main template file
 * 
 * @package MarcelloScavoTattoo
 */

get_header(); ?>

<section class="hero-section">
    <div class="container">
        <!-- TEST-CACHE-COPILOT -->
        <div class="hero-content">
            <div class="hero-label">
                <?php 
                // Sistema semplificato - solo italiano nel Customizer
                $hero_label = get_theme_mod('hero_label', 'L\'ARTE DEL TATTOO');
                echo '<span data-translatable="true" data-original-text="' . esc_attr($hero_label) . '">';
                echo esc_html($hero_label);
                echo '</span>';
                ?>
            </div>
            <h1 class="hero-title">
                <?php 
                // Sistema semplificato - solo italiano nel Customizer
                $hero_title = get_theme_mod('hero_title', 'Scopri l\'essenza dei miei tatuaggi e opere d\'arte.');
                echo '<span data-translatable="true" data-original-text="' . esc_attr($hero_title) . '">';
                echo wp_kses_post($hero_title);
                echo '</span>';
                ?>
            </h1>
            <p class="hero-description">
                <?php 
                // Sistema semplificato - solo italiano nel Customizer
                $hero_description = get_theme_mod('hero_description', 'Benvenuti nel mio mondo creativo, dove ogni storia racconta una storia, i miei tatuaggi e le opere d\'arte nascono dall\'ispirazione e dalla passione. Che tu stia cercando un tatuaggio personalizzato per il tuo corpo o un\'opera unica per la tua parete, sei nel posto giusto. Esplora il mio portfolio e lasciati ispirare dalla fusione di arte e tatuaggio.');
                echo '<span data-translatable="true" data-original-text="' . esc_attr($hero_description) . '">';
                echo esc_html($hero_description);
                echo '</span>';
                ?>
            </p>
            <a href="#portfolio" class="btn btn-gold">
                <?php 
                // Sistema semplificato - solo italiano nel Customizer
                $hero_button = get_theme_mod('hero_button_text', 'Esplora Ora');
                echo '<span data-translatable="true" data-original-text="' . esc_attr($hero_button) . '">';
                echo esc_html($hero_button);
                echo '</span>';
                ?>
            </a>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="section about-section">
    <div class="container">
        <div class="about-content-flag">
            <div class="about-image-float">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/DSC03105.JPEG" alt="Marcello Scavo at work" class="about-image-flag">
            </div>
            <h2 class="section-title">
                <span data-translatable="true" data-original-text="L'arte di Marcello: un viaggio">
                    <?php _e('L\'arte di Marcello: un viaggio', 'marcello-scavo-tattoo'); ?>
                </span>
            </h2>
            <p class="about-text-flag">
                <span data-translatable="true" data-original-text="Marcello è un artista che unisce passione e talento in ogni opera d'arte e tatuaggio. Fin da giovane, ha coltivato il suo amore per l'arte, esplorando tecniche diverse e affascinandosi sempre della vita che lo circonda. La sua evoluzione è evidente, non solo nei suoi lavori, ma anche nella continua ricerca di innovazione e stile. Ogni tatuaggio racconta una storia, un pezzo della personalità di chi lo indossa. Con la sua capacità di ascoltare e interpretare i desideri dei clienti, Marcello trasforma ogni foglio bianco in un'opera unica, creando un legame speciale con ognuno di loro.">
                    <?php _e('Marcello è un artista che unisce passione e talento in ogni opera d\'arte e tatuaggio. Fin da giovane, ha coltivato il suo amore per l\'arte, esplorando tecniche diverse e affascinandosi sempre della vita che lo circonda. La sua evoluzione è evidente, non solo nei suoi lavori, ma anche nella continua ricerca di innovazione e stile. Ogni tatuaggio racconta una storia, un pezzo della personalità di chi lo indossa. Con la sua capacità di ascoltare e interpretare i desideri dei clienti, Marcello trasforma ogni foglio bianco in un\'opera unica, creando un legame speciale con ognuno di loro.', 'marcello-scavo-tattoo'); ?>
                </span>
            </p>
            <p class="about-text-flag">
                <span data-translatable="true" data-original-text="La filosofia di Marcello si basa sull'ascolto attento delle esigenze del cliente e sulla traduzione delle loro idee in opere d'arte durature. Ogni sessione è un momento di creazione condivisa, dove l'esperienza e la creatività si fondono per dare vita a qualcosa di veramente speciale.">
                    <?php _e('La filosofia di Marcello si basa sull\'ascolto attento delle esigenze del cliente e sulla traduzione delle loro idee in opere d\'arte durature. Ogni sessione è un momento di creazione condivisa, dove l\'esperienza e la creatività si fondono per dare vita a qualcosa di veramente speciale.', 'marcello-scavo-tattoo'); ?>
                </span>
            </p>
        </div>
    </div>
</section>

<!-- Portfolio/Gallery Section -->

<!-- Image Viewer Modal -->
<div id="image-viewer-modal" class="image-viewer-modal">
    <div class="image-viewer-content">
        <button class="image-viewer-close">&times;</button>
        <img id="image-viewer-img" src="" alt="">
        <div class="image-viewer-info">
            <h3 id="image-viewer-title"></h3>
            <p id="image-viewer-description"></p>
        </div>
    </div>
</div>

<!-- Portfolio/Gallery Section -->
<section id="portfolio" class="section portfolio-section">
    <div class="container">
        <?php if (is_active_sidebar('gallery-showcase')) : ?>
            <!-- Gallery Widget Area -->
            <div class="gallery-showcase-area">
                <?php dynamic_sidebar('gallery-showcase'); ?>
            </div>
        <?php else : ?>
            <!-- Fallback: Solo avviso chiaro -->
            <div class="theme-widget-info" style="background:#fffbe6;border:1px solid #c9b05f;padding:32px 24px;margin:40px auto 40px auto;border-radius:12px;color:#273a59;max-width:600px;text-align:center;font-size:1.15rem;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                <strong style="font-size:1.2em;display:block;margin-bottom:8px;"><?php _e('Suggerimento:', 'marcello-scavo-tattoo'); ?></strong>
                <?php _e('Questa sezione può essere personalizzata aggiungendo il widget <b>Galleria Portfolio</b> dal Customizer o dalla sezione Widget di WordPress.<br>Vai su <b>Aspetto &gt; Personalizza</b> oppure <b>Aspetto &gt; Widget</b> e attiva <b>Gallery Showcase</b> per mostrare la tua galleria personalizzata.', 'marcello-scavo-tattoo'); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Instagram Social Section -->
<section id="instagram" class="section instagram-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php _e('Seguimi sui Social', 'marcello-scavo-tattoo'); ?></h2>
            <p class="section-subtitle"><?php _e('Scopri il processo creativo dietro ogni tatuaggio e resta aggiornato sui miei ultimi lavori.', 'marcello-scavo-tattoo'); ?></p>
        </div>
        
        <!-- Social Media Widget Area -->
        <?php if (is_active_sidebar('social-media')) : ?>
            <div class="social-media-widget-area">
                <?php dynamic_sidebar('social-media'); ?>
            </div>
        <?php else : ?>
            <!-- Fallback: contenuto predefinito se nessun widget è presente -->
            <div class="instagram-widget-container">
                <div class="theme-widget-info" style="background:#fffbe6;border:1px solid #c9b05f;padding:16px;margin:24px 0 0 0;border-radius:8px;color:#273a59;">
                    <strong><?php _e('Suggerimento:', 'marcello-scavo-tattoo'); ?></strong> <?php _e('Puoi mostrare il feed Instagram personalizzato aggiungendo il widget "Social Media" dal Customizer o dalla sezione Widget di WordPress. Vai su <b>Aspetto &gt; Personalizza</b> oppure <b>Aspetto &gt; Widget</b> e attiva "Social Media" per visualizzare i tuoi social.', 'marcello-scavo-tattoo'); ?>
                </div>
                <?php
                // Display Instagram widget with safety check
                if (class_exists('Marcello_Scavo_Instagram_Feed_Widget')) {
                    $instagram_widget = new Marcello_Scavo_Instagram_Feed_Widget();
                    $instagram_args = array(
                        'before_widget' => '<div class="instagram-widget-wrapper">',
                        'after_widget' => '</div>',
                        'before_title' => '<h3 class="instagram-widget-title">',
                        'after_title' => '</h3>'
                    );
                    $instagram_instance = array(
                    'title' => '',
                    'username' => 'marcelloscavo_art',
                    'count' => 6,
                    'layout' => 'grid',
                    'show_captions' => false,
                    'cache_time' => 3600
                );
                $instagram_widget->widget($instagram_args, $instagram_instance);
                } else {
                    // Fallback if widget class doesn't exist
                    echo '<div class="instagram-fallback">';
                    echo '<p><a href="https://instagram.com/marcelloscavo_art" target="_blank">Seguimi su Instagram @marcelloscavo_art</a></p>';
                    echo '</div>';
                }
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Social Media Cards -->
        <div class="social-media-grid">
            <div class="social-card instagram-card">
                <div class="social-card-header">
                    <i class="fab fa-instagram"></i>
                    <div class="social-card-info">
                        <h3>Instagram</h3>
                        <p>@marcelloscavo_art</p>
                    </div>
                </div>
                <div class="social-card-content">
                    <p>Seguimi per vedere i miei ultimi tatuaggi, il processo creativo e i momenti dietro le quinte dello studio.</p>
                    <div class="social-stats">
                        <span><strong>3.2K</strong> Followers</span>
                        <span><strong>520+</strong> Posts</span>
                    </div>
                </div>
                <a href="https://instagram.com/marcelloscavo_art" target="_blank" rel="noopener" class="social-card-link">
                    <i class="fas fa-external-link-alt"></i>
                    Segui
                </a>
            </div>
            
            <div class="social-card tiktok-card">
                <div class="social-card-header">
                    <i class="fab fa-tiktok"></i>
                    <div class="social-card-info">
                        <h3>TikTok</h3>
                        <p>@marcello.scavo</p>
                    </div>
                </div>
                <div class="social-card-content">
                    <p>Video creativi, tutorial di tatuaggi e contenuti esclusivi per scoprire l'arte del tattoo.</p>
                    <div class="social-stats">
                        <span><strong>850</strong> Followers</span>
                        <span><strong>120+</strong> Video</span>
                    </div>
                </div>
                <a href="https://tiktok.com/@marcello.scavo" target="_blank" rel="noopener" class="social-card-link">
                    <i class="fas fa-external-link-alt"></i>
                    Segui
                </a>
            </div>
            
            <div class="social-card youtube-card">
                <div class="social-card-header">
                    <i class="fab fa-youtube"></i>
                    <div class="social-card-info">
                        <h3>YouTube</h3>
                        <p>Marcello Scavo</p>
                    </div>
                </div>
                <div class="social-card-content">
                    <p>Tutorial approfonditi, interviste e documentari sul mondo dei tatuaggi e dell'arte.</p>
                    <div class="social-stats">
                        <span><strong>420</strong> Iscritti</span>
                        <span><strong>25</strong> Video</span>
                    </div>
                </div>
                <a href="https://youtube.com/@marcelloscavo" target="_blank" rel="noopener" class="social-card-link">
                    <i class="fas fa-external-link-alt"></i>
                    Iscriviti
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials/Reviews Section -->
<section class="testimonials-section">
    <div class="container">
        <?php if (is_active_sidebar('reviews-section')) : ?>
            <!-- Widget Area for Reviews -->
            <div class="reviews-widget-area">
                <?php dynamic_sidebar('reviews-section'); ?>
            </div>
        <?php else : ?>
            <!-- Fallback: Default testimonials content -->
            <div class="testimonials-content">
                <div class="theme-widget-info" style="background:#fffbe6;border:1px solid #c9b05f;padding:16px;margin:24px 0 16px 0;border-radius:8px;color:#273a59;">
                    <strong><?php _e('Suggerimento:', 'marcello-scavo-tattoo'); ?></strong> <?php _e('Puoi mostrare le recensioni Google aggiungendo il widget "Recensioni Google" dal Customizer o dalla sezione Widget di WordPress. Vai su <b>Aspetto &gt; Personalizza</b> oppure <b>Aspetto &gt; Widget</b> e attiva "Reviews Section" per visualizzare le testimonianze dei tuoi clienti.', 'marcello-scavo-tattoo'); ?>
                </div>
                <div class="testimonials-label">La soddisfazione clienti</div>
                <h2>Cosa dicono di noi i nostri clienti soddisfatti</h2>
                <div class="stars">
                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                </div>
                <blockquote>
                    "Ogni creazione è un viaggio. Ha avuto un'esperienza fantastica e il risultato è un'opera d'arte
                    che porto con orgoglio."
                </blockquote>
                <cite>
                    <strong>Marco Rossi</strong><br>
                    Designer
                </cite>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="section services-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <span data-translatable="true" data-original-text="I nostri servizi">
                    <?php _e('I nostri servizi', 'marcello-scavo-tattoo'); ?>
                </span>
            </h2>
            <p class="section-subtitle">
                <span data-translatable="true" data-original-text="Scopri le nostre offerte principali, pensate per soddisfare le tue passioni.">
                    <?php _e('Scopri le nostre offerte principali, pensate per soddisfare le tue passioni.', 'marcello-scavo-tattoo'); ?>
                </span>
            </p>
        </div>
        
        <div class="services-grid-new">
            <div class="service-card">
                <div class="service-image">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/IMG_4854.JPG" alt="Vendita opere d'arte">
                </div>
                <div class="service-content">
                    <h3>
                        <span data-translatable="true" data-original-text="Vendita opere d'arte">
                            <?php _e('Vendita opere d\'arte', 'marcello-scavo-tattoo'); ?>
                        </span>
                    </h3>
                    <p>
                        <span data-translatable="true" data-original-text="Esplora la nostra collezione di opere d'arte uniche e porta a casa un pezzo di creatività.">
                            <?php _e('Esplora la nostra collezione di opere d\'arte uniche e porta a casa un pezzo di creatività.', 'marcello-scavo-tattoo'); ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="service-card">
                <div class="service-image">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/54B3F245-E22C-4DDC-B5D2-885750AD64E6.JPG" alt="Mostra di tatuaggi">
                </div>
                <div class="service-content">
                    <h3>
                        <span data-translatable="true" data-original-text="Mostra di tatuaggi">
                            <?php _e('Mostra di tatuaggi', 'marcello-scavo-tattoo'); ?>
                        </span>
                    </h3>
                    <p>
                        <span data-translatable="true" data-original-text="Assisti ai nostri eventi di tatuaggio, dove talenti internazionali mostrano le loro creazioni dal vivo.">
                            <?php _e('Assisti ai nostri eventi di tatuaggio, dove talenti internazionali mostrano le loro creazioni dal vivo.', 'marcello-scavo-tattoo'); ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="service-card">
                <div class="service-image">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/IMG_4800.jpg" alt="Prenotazione tatuaggi">
                </div>
                <div class="service-content">
                    <h3>
                        <span data-translatable="true" data-original-text="Prenotazione tatuaggi">
                            <?php _e('Prenotazione tatuaggi', 'marcello-scavo-tattoo'); ?>
                        </span>
                    </h3>
                    <p>
                        <span data-translatable="true" data-original-text="Prenota il tuo tatuaggio direttamente online e realizza il tuo design personalizzato con noi.">
                            <?php _e('Prenota il tuo tatuaggio direttamente online e realizza il tuo design personalizzato con noi.', 'marcello-scavo-tattoo'); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA/Booking Section -->
<section id="booking" class="cta-section">
    <div class="container">
        <?php if (is_active_sidebar('booking-section')) : ?>
            <!-- Booking Widget Area -->
            <div class="booking-widget-area">
                <div class="bookly-integration-container">
                    <?php dynamic_sidebar('booking-section'); ?>
                </div>
            </div>
        <?php else : ?>
            <!-- Fallback: Enhanced Mobile-Friendly CTA -->
            <div class="cta-content">
                <h2>
                    <span data-translatable="true" data-original-text="Prenota il tuo tatuaggio oggi!">
                        <?php _e('Prenota il tuo tatuaggio oggi!', 'marcello-scavo-tattoo'); ?>
                    </span>
                </h2>
                <p>
                    <span data-translatable="true" data-original-text="Scopri l'arte del tatuaggio e prenota il tuo appuntamento per un'esperienza unica.">
                        <?php _e('Scopri l\'arte del tatuaggio e prenota il tuo appuntamento per un\'esperienza unica.', 'marcello-scavo-tattoo'); ?>
                    </span>
                </p>
                
                <!-- Mobile-Optimized Booking Options -->
                <div class="booking-fallback">
                    <div class="fallback-actions">
                        <a href="tel:+393401234567" class="btn btn-primary">
                            <i class="fas fa-phone"></i>
                            <span data-translatable="true" data-original-text="Chiama Ora">
                                <?php _e('Chiama Ora', 'marcello-scavo-tattoo'); ?>
                            </span>
                        </a>
                        <a href="https://wa.me/393401234567" target="_blank" class="btn btn-outline-primary">
                            <i class="fab fa-whatsapp"></i>
                            <span data-translatable="true" data-original-text="WhatsApp">
                                <?php _e('WhatsApp', 'marcello-scavo-tattoo'); ?>
                            </span>
                        </a>
                        <a href="mailto:info@marcelloscavo.com" class="btn btn-outline-primary">
                            <i class="fas fa-envelope"></i>
                            <span data-translatable="true" data-original-text="Email">
                                <?php _e('Email', 'marcello-scavo-tattoo'); ?>
                            </span>
                        </a>
                    </div>
                    
                    <!-- Alternative Contact Information -->
                    <div class="booking-alternative-contact">
                        <p>
                            <span data-translatable="true" data-original-text="Preferisci un contatto diretto? Siamo disponibili per consultazioni personalizzate.">
                                <?php _e('Preferisci un contatto diretto? Siamo disponibili per consultazioni personalizzate.', 'marcello-scavo-tattoo'); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="section contact-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Contattaci per informazioni</h2>
        </div>
        
        <div class="contact-content">
            <?php if (is_active_sidebar('contact-section')) : ?>
                <!-- Widget personalizzabili -->
                <div class="contact-widgets">
                    <?php dynamic_sidebar('contact-section'); ?>
                </div>
            <?php else : ?>
                <!-- Contenuto di fallback accattivante -->
                <div class="contact-methods">
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>Scrivici un messaggio</h3>
                        <p>Hai domande o richieste? Compila il modulo qui nel fianco e ti risponderemo al più presto.</p>
                        <div class="contact-action">
                            <a href="#contact-form" class="btn-contact">
                                <i class="fas fa-paper-plane"></i>
                                Invia Messaggio
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Le nostre informazioni</h3>
                        <p>Puoi trovarci all'indirizzo indicato qui sotto. Siamo disponibili anche via email e su social.</p>
                        <div class="contact-info">
                            <p><i class="fas fa-phone"></i> <strong>+39 123 456 7890</strong></p>
                            <p><i class="fas fa-envelope"></i> <strong>info@marcelloscavo.com</strong></p>
                            <p><i class="fas fa-map-pin"></i> <strong>Via Example 123, Milano</strong></p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <h3>Segui i nostri social</h3>
                        <p>Resta aggiornato sulle ultime novità e eventi seguendoci sui nostri profili social.</p>
                        <div class="contact-social">
                            <a href="https://instagram.com/marcelloscavo_art" target="_blank" class="social-link instagram">
                                <i class="fab fa-instagram"></i>
                                Instagram
                            </a>
                            <a href="https://tiktok.com/@marcello.scavo" target="_blank" class="social-link tiktok">
                                <i class="fab fa-tiktok"></i>
                                TikTok
                            </a>
                            <a href="https://youtube.com/@MarcelloScavo" target="_blank" class="social-link youtube">
                                <i class="fab fa-youtube"></i>
                                YouTube
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Mappa Full-Width separata dal container -->
    <div class="contact-map-fullwidth">
        <?php if (is_active_sidebar('location-map')) : ?>
            <!-- Widget mappa personalizzabile -->
            <?php dynamic_sidebar('location-map'); ?>
        <?php else : ?>
            <!-- Placeholder di fallback -->
            <div class="map-placeholder">
                <div class="map-content">
                    <i class="fas fa-map-marked-alt"></i>
                    <h4>Mappa Localizzazione</h4>
                    <p>Configura il widget "🗺️ Mappa Personalizzata" nell'area "🗺️ Mappa Localizzazione" per mostrare la posizione della tua attività.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
