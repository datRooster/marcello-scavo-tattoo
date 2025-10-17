<?php

/**
 * Template per categoria Portfolio
 * Stile allineato alla homepage, senza gradienti
 * @package MarcelloScavoTattoo
 */

get_header();

// Ottieni informazioni sulla categoria corrente
$current_term = get_queried_object();
$term_name = $current_term->name;
$term_description = $current_term->description;
?>

<!-- Hero Section con stile homepage -->
<section class="hero-section">
	<div class="container">
		<div class="hero-content">
			<div class="hero-label">
				<span data-translatable="true" data-original-text="CATEGORIA <?php echo strtoupper($term_name); ?>">
					<?php printf(__('CATEGORIA %s', 'marcello-scavo-tattoo'), strtoupper($term_name)); ?>
				</span>
			</div>
			<h1 class="hero-title">
				<span data-translatable="true" data-original-text="<?php echo esc_attr($term_name); ?>">
					<?php echo esc_html($term_name); ?>
				</span>
			</h1>
			<p class="hero-description">
				<span data-translatable="true" data-original-text="<?php echo esc_attr($term_description ? $term_description : 'Esplora questa collezione unica di opere d\'arte e tatuaggi selezionati con cura.'); ?>">
					<?php echo $term_description ? esc_html($term_description) : __('Esplora questa collezione unica di opere d\'arte e tatuaggi selezionati con cura.', 'marcello-scavo-tattoo'); ?>
				</span>
			</p>
			<a href="#portfolio" class="btn btn-gold">
				<span data-translatable="true" data-original-text="Esplora la Collezione">
					<?php _e('Esplora la Collezione', 'marcello-scavo-tattoo'); ?>
				</span>
			</a>
		</div>
	</div>
</section>

<!-- Portfolio/Gallery Section -->
<section id="portfolio" class="section portfolio-section">
	<div class="container">
		<div class="section-header">
			<h2 class="section-title">
				<span data-translatable="true" data-original-text="<?php echo esc_attr($term_name); ?>">
					<?php echo esc_html($term_name); ?>
				</span>
			</h2>
			<p class="section-subtitle">
				<span data-translatable="true" data-original-text="<?php echo esc_attr($term_description ? $term_description : 'Scopri questa raccolta selezionata di opere d\'arte e tatuaggi unici.'); ?>">
					<?php echo $term_description ? esc_html($term_description) : __('Scopri questa raccolta selezionata di opere d\'arte e tatuaggi unici.', 'marcello-scavo-tattoo'); ?>
				</span>
			</p>
		</div>

		<div class="portfolio-category-gallery">
			<?php if (have_posts()) : ?>
				<div class="portfolio-grid">
					<?php while (have_posts()) : the_post(); ?>
						<div class="portfolio-item">
							<div class="portfolio-image">
								<?php if (has_post_thumbnail()) : ?>
									<img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>"
										alt="<?php echo esc_attr(get_the_title()); ?>"
										loading="lazy">
								<?php else : ?>
									<div class="portfolio-placeholder">
										<span class="dashicons dashicons-format-image"></span>
									</div>
								<?php endif; ?>
								<div class="portfolio-overlay">
									<div class="portfolio-overlay-content">
										<h3><?php the_title(); ?></h3>
										<?php if (has_excerpt()) : ?>
											<p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
										<?php endif; ?>

										<?php
										$portfolio_categories = get_the_terms(get_the_ID(), 'portfolio_category');
										if ($portfolio_categories && !is_wp_error($portfolio_categories)) : ?>
											<div class="portfolio-categories">
												<?php foreach ($portfolio_categories as $cat) : ?>
													<span class="portfolio-category"><?php echo esc_html($cat->name); ?></span>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>

										<a href="<?php the_permalink(); ?>" class="portfolio-link">
											<?php _e('Visualizza', 'marcello-scavo-tattoo'); ?>
										</a>
									</div>
								</div>
							</div>
							<div class="portfolio-info">
								<h3 class="portfolio-title"><?php the_title(); ?></h3>
								<div class="portfolio-date">
									<?php echo get_the_date(); ?>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				</div>

				<?php
				// Pagination con stile homepage
				$pagination = paginate_links(array(
					'prev_text' => '‹ ' . __('Precedente', 'marcello-scavo-tattoo'),
					'next_text' => __('Successivo', 'marcello-scavo-tattoo') . ' ›',
					'mid_size' => 2,
					'type' => 'array'
				));

				if ($pagination) : ?>
					<nav class="portfolio-pagination">
						<ul class="pagination">
							<?php foreach ($pagination as $page) : ?>
								<li><?php echo $page; ?></li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>

			<?php else : ?>
				<div class="portfolio-empty">
					<div class="empty-icon">
						<span class="dashicons dashicons-format-image"></span>
					</div>
					<h3><?php _e('Nessuna opera trovata', 'marcello-scavo-tattoo'); ?></h3>
					<p><?php _e('Non ci sono ancora opere in questa categoria. Torna presto per vedere nuovi lavori!', 'marcello-scavo-tattoo'); ?></p>
					<a href="<?php echo home_url('/portfolio'); ?>" class="btn btn-gold">
						<?php _e('Torna al Portfolio', 'marcello-scavo-tattoo'); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<!-- Instagram Social Section -->
<section id="instagram" class="section instagram-section">
	<div class="container">
		<div class="section-header">
			<h2 class="section-title">
				<span data-translatable="true" data-original-text="Seguimi sui Social">
					<?php _e('Seguimi sui Social', 'marcello-scavo-tattoo'); ?>
				</span>
			</h2>
			<p class="section-subtitle">
				<span data-translatable="true" data-original-text="Scopri il processo creativo dietro ogni tatuaggio e resta aggiornato sui miei ultimi lavori.">
					<?php _e('Scopri il processo creativo dietro ogni tatuaggio e resta aggiornato sui miei ultimi lavori.', 'marcello-scavo-tattoo'); ?>
				</span>
			</p>
		</div>

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
	</div>
</section>

<!-- Contact Section -->
<section id="contact" class="section contact-section">
	<div class="container">
		<div class="section-header">
			<h2 class="section-title">
				<span data-translatable="true" data-original-text="Contattaci per informazioni">
					<?php _e('Contattaci per informazioni', 'marcello-scavo-tattoo'); ?>
				</span>
			</h2>
		</div>

		<div class="contact-content">
			<div class="contact-methods">
				<div class="contact-method">
					<div class="contact-icon">
						<i class="fas fa-envelope"></i>
					</div>
					<h3>
						<span data-translatable="true" data-original-text="Scrivici un messaggio">
							<?php _e('Scrivici un messaggio', 'marcello-scavo-tattoo'); ?>
						</span>
					</h3>
					<p>
						<span data-translatable="true" data-original-text="Hai domande o richieste? Compila il modulo di contatto e ti risponderemo al più presto.">
							<?php _e('Hai domande o richieste? Compila il modulo di contatto e ti risponderemo al più presto.', 'marcello-scavo-tattoo'); ?>
						</span>
					</p>
				</div>

				<div class="contact-method">
					<div class="contact-icon">
						<i class="fas fa-map-marker-alt"></i>
					</div>
					<h3>
						<span data-translatable="true" data-original-text="Le nostre informazioni">
							<?php _e('Le nostre informazioni', 'marcello-scavo-tattoo'); ?>
						</span>
					</h3>
					<p>
						<span data-translatable="true" data-original-text="Puoi trovarci all'indirizzo indicato qui sotto. Siamo disponibili anche via email e sui social.">
							<?php _e('Puoi trovarci all\'indirizzo indicato qui sotto. Siamo disponibili anche via email e sui social.', 'marcello-scavo-tattoo'); ?>
						</span>
					</p>
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
					<h3>
						<span data-translatable="true" data-original-text="Segui i nostri social">
							<?php _e('Segui i nostri social', 'marcello-scavo-tattoo'); ?>
						</span>
					</h3>
					<p>
						<span data-translatable="true" data-original-text="Resta aggiornato sulle ultime novità e eventi seguendoci sui nostri profili social.">
							<?php _e('Resta aggiornato sulle ultime novità e eventi seguendoci sui nostri profili social.', 'marcello-scavo-tattoo'); ?>
						</span>
					</p>
					<div class="contact-social">
						<a href="https://instagram.com/marcelloscavo_art" target="_blank" class="social-link instagram">
							<i class="fab fa-instagram"></i>
							Instagram
						</a>
						<a href="https://tiktok.com/@marcello.scavo" target="_blank" class="social-link tiktok">
							<i class="fab fa-tiktok"></i>
							TikTok
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>