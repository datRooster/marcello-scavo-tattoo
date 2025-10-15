<?php
/**
 * Template per categoria Portfolio (stanza galleria d'arte)
 * Mostra i lavori della categoria corrente con layout ricco e mo            </div>
		<?php endif; ?>
	</div>
</section>

<section class="portfolio-latest-section">
	<div class="container">
		<?php if (is_active_sidebar('taxonomy-portfolio-latest')) : ?>
			<div class="taxonomy-portfolio-latest-widget-area">
				<?php dynamic_sidebar('taxonomy-portfolio-latest'); ?>
			</div>
		<?php else : ?>
			<div class="section-header">
				<h2 class="section-title"><?php _e('I miei lavori recenti', 'marcello-scavo'); ?></h2>
				<div class="section-divider"></div>
				<p class="section-subtitle">
					<?php _e('Una selezione dei lavori più recenti, tra tradizione e innovazione artistica.', 'marcello-scavo'); ?>
				</p>
			</div>age MarcelloScavoTattoo
 */

get_header();

// Ottieni informazioni sulla categoria corrente
$current_term     = get_queried_object();
$term_name        = $current_term->name;
$term_description = $current_term->description;
?>

<section class="portfolio-hero">
	<div class="portfolio-hero-bg"></div>
	<div class="portfolio-hero-overlay"></div>
	<div class="container">
		<?php if ( is_active_sidebar( 'taxonomy-portfolio-hero' ) ) : ?>
			<div class="taxonomy-portfolio-hero-widget-area">
				<?php dynamic_sidebar( 'taxonomy-portfolio-hero' ); ?>
			</div>
		<?php else : ?>
			<div class="portfolio-hero-content">
				<h1 class="portfolio-hero-title">
					<?php echo esc_html( $term_name ); ?>
				</h1>
				<div class="portfolio-hero-divider"></div>
				<p class="portfolio-hero-desc">
					<?php echo $term_description ? esc_html( $term_description ) : 'Esplora questa collezione unica di opere d\'arte e tatuaggi selezionati con cura.'; ?>
				</p>
				<div class="portfolio-hero-cta">
					<a href="#galleria" class="btn btn-outline-light btn-hero"><?php _e( 'Esplora la Collezione', 'marcello-scavo' ); ?></a>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="portfolio-hero-scroll-indicator">
		<div class="scroll-arrow"></div>
	</div>
</section>

<section id="galleria" class="portfolio-gallery-section">
	<div class="container">
		<?php if ( is_active_sidebar( 'taxonomy-portfolio-gallery' ) ) : ?>
			<div class="taxonomy-portfolio-gallery-widget-area">
				<?php dynamic_sidebar( 'taxonomy-portfolio-gallery' ); ?>
			</div>
		<?php else : ?>
			<div class="section-header">
				<h2 class="section-title"><?php echo esc_html( $term_name ); ?></h2>
				<div class="section-divider"></div>
				<p class="section-subtitle">
					<?php echo $term_description ? esc_html( $term_description ) : 'Scopri questa raccolta selezionata di opere d\'arte e tatuaggi unici.'; ?>
				</p>
			</div>
			
			<div class="portfolio-category-gallery">
			<?php if ( have_posts() ) : ?>
				<div class="portfolio-masonry-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<div class="portfolio-masonry-item">
							<div class="portfolio-showcase-item">
								<div class="portfolio-showcase-image">
									<?php if ( has_post_thumbnail() ) : ?>
										<img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'portfolio-large' ) ); ?>" 
											alt="<?php echo esc_attr( get_the_title() ); ?>" 
											loading="lazy">
									<?php else : ?>
										<div class="portfolio-placeholder-image">
											<span class="dashicons dashicons-format-image"></span>
										</div>
									<?php endif; ?>
									<div class="portfolio-showcase-overlay">
										<div class="portfolio-showcase-content">
											<h4><?php the_title(); ?></h4>
											<?php if ( has_excerpt() ) : ?>
												<p class="portfolio-excerpt"><?php the_excerpt(); ?></p>
											<?php endif; ?>
											
											<?php
											$portfolio_categories = get_the_terms( get_the_ID(), 'portfolio_category' );
											if ( $portfolio_categories && ! is_wp_error( $portfolio_categories ) ) :
												?>
												<div class="portfolio-categories">
													<?php foreach ( $portfolio_categories as $cat ) : ?>
														<span class="portfolio-category-tag"><?php echo esc_html( $cat->name ); ?></span>
													<?php endforeach; ?>
												</div>
											<?php endif; ?>
											
											<a href="<?php the_permalink(); ?>" class="portfolio-showcase-link"><?php _e( 'Visualizza', 'marcello-scavo' ); ?></a>
										</div>
									</div>
								</div>
								<div class="portfolio-item-meta">
									<h3 class="portfolio-item-title"><?php the_title(); ?></h3>
									<div class="portfolio-item-date">
										<?php echo get_the_date(); ?>
									</div>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				</div>
				
				<?php
				// Pagination
				$pagination = paginate_links(
					array(
						'prev_text' => '‹ ' . __( 'Precedente', 'marcello-scavo' ),
						'next_text' => __( 'Successivo', 'marcello-scavo' ) . ' ›',
						'mid_size'  => 2,
						'type'      => 'array',
					)
				);

				if ( $pagination ) :
					?>
					<nav class="portfolio-pagination">
						<ul class="pagination">
							<?php foreach ( $pagination as $page ) : ?>
								<li><?php echo $page; ?></li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>
				
			<?php else : ?>
				<div class="portfolio-empty-state">
					<div class="empty-state-icon">
						<span class="dashicons dashicons-format-image"></span>
					</div>
					<h3><?php _e( 'Nessuna opera trovata', 'marcello-scavo' ); ?></h3>
					<p><?php _e( 'Non ci sono ancora opere in questa categoria. Torna presto per vedere nuovi lavori!', 'marcello-scavo' ); ?></p>
					<a href="<?php echo home_url( '/portfolio' ); ?>" class="btn btn-primary">
						<?php _e( 'Esplora tutto il Portfolio', 'marcello-scavo' ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="portfolio-latest-section">
	<div class="container">
		<div class="section-header">
			<h2 class="section-title"><?php _e( 'I miei lavori recenti', 'marcello-scavo' ); ?></h2>
			<div class="section-divider"></div>
			<p class="section-subtitle">
				<?php _e( 'Una selezione dei lavori più recenti, tra tradizione e innovazione artistica.', 'marcello-scavo' ); ?>
			</p>
		</div>
		
		<div class="portfolio-latest-grid">
			<?php
			// Query per gli ultimi portfolio items (diversi dalla categoria corrente se possibile)
			$latest_args = array(
				'post_type'      => 'portfolio',
				'posts_per_page' => 4,
				'post_status'    => 'publish',
				'meta_query'     => array(
					array(
						'key'     => '_thumbnail_id',
						'compare' => 'EXISTS',
					),
				),
				'orderby'        => 'date',
				'order'          => 'DESC',
			);

			$latest_query = new WP_Query( $latest_args );

			if ( $latest_query->have_posts() ) :
				while ( $latest_query->have_posts() ) :
					$latest_query->the_post();
					?>
					<div class="portfolio-latest-item">
						<div class="portfolio-latest-image">
							<?php if ( has_post_thumbnail() ) : ?>
								<img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'portfolio-large' ) ); ?>" 
									alt="<?php echo esc_attr( get_the_title() ); ?>" 
									loading="lazy">
							<?php else : ?>
								<div class="portfolio-placeholder-image">
									<span class="dashicons dashicons-format-image"></span>
								</div>
							<?php endif; ?>
							<div class="portfolio-latest-overlay">
								<div class="portfolio-latest-content">
									<?php
									$portfolio_categories = get_the_terms( get_the_ID(), 'portfolio_category' );
									if ( $portfolio_categories && ! is_wp_error( $portfolio_categories ) ) :
										?>
										<div class="portfolio-latest-categories">
											<?php foreach ( $portfolio_categories as $cat ) : ?>
												<span class="portfolio-category-badge"><?php echo esc_html( $cat->name ); ?></span>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
									
									<h4 class="portfolio-latest-title"><?php the_title(); ?></h4>
									
									<?php if ( has_excerpt() ) : ?>
										<p class="portfolio-latest-excerpt"><?php the_excerpt(); ?></p>
									<?php endif; ?>
									
									<div class="portfolio-latest-meta">
										<span class="portfolio-latest-date"><?php echo get_the_date(); ?></span>
									</div>
									
									<a href="<?php the_permalink(); ?>" class="portfolio-latest-link"><?php _e( 'Visualizza', 'marcello-scavo' ); ?></a>
								</div>
							</div>
						</div>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
			else :
				?>
				<div class="portfolio-latest-fallback">
					<?php for ( $i = 0; $i < 4; $i++ ) : ?>
						<div class="portfolio-latest-placeholder">
							<div class="placeholder-image">
								<span class="dashicons dashicons-format-image"></span>
							</div>
							<div class="placeholder-content">
								<h3><?php _e( 'Tatuaggio Tribale', 'marcello-scavo' ); ?></h3>
								<p><?php _e( 'Descrizione breve del tatuaggio tribale, stile e significato.', 'marcello-scavo' ); ?></p>
								<span class="placeholder-date"><?php echo date( 'd M Y' ); ?></span>
							</div>
						</div>
					<?php endfor; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</section>

<section class="portfolio-testimonials-section">
	<div class="container">
		<?php if ( is_active_sidebar( 'reviews-section' ) ) : ?>
			<!-- Uso la stessa area widget della homepage per consistenza -->
			<div class="reviews-widget-area">
				<?php dynamic_sidebar( 'reviews-section' ); ?>
			</div>
		<?php else : ?>
			<div class="section-header">
				<h2 class="section-title"><?php _e( 'Cosa pensano i nostri clienti', 'marcello-scavo' ); ?></h2>
				<div class="section-divider"></div>
				<p class="section-subtitle"><?php _e( 'Le parole di chi ha vissuto l\'esperienza del tatuaggio con noi', 'marcello-scavo' ); ?></p>
			</div>
		
		<div class="testimonials-grid">
			<div class="testimonial-item">
				<div class="testimonial-content">
					<div class="testimonial-quote">
						<span class="quote-icon">❝</span>
						<p><?php _e( '"Esperienza straordinaria! Marcello ha saputo trasformare la mia idea in un\'opera d\'arte sulla pelle. Professionalità e creatività ai massimi livelli."', 'marcello-scavo' ); ?></p>
					</div>
					<div class="testimonial-author">
						<div class="author-avatar">
							<span class="dashicons dashicons-admin-users"></span>
						</div>
						<div class="author-info">
							<h4><?php _e( 'Maria Rossi', 'marcello-scavo' ); ?></h4>
							<span><?php _e( 'Cliente soddisfatta', 'marcello-scavo' ); ?></span>
							<div class="testimonial-rating">★★★★★</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="testimonial-item">
				<div class="testimonial-content">
					<div class="testimonial-quote">
						<span class="quote-icon">❝</span>
						<p><?php _e( '"Ambiente accogliente, massima igiene e un artista che sa davvero ascoltare. Il mio tatuaggio ha superato ogni aspettativa!"', 'marcello-scavo' ); ?></p>
					</div>
					<div class="testimonial-author">
						<div class="author-avatar">
							<span class="dashicons dashicons-admin-users"></span>
						</div>
						<div class="author-info">
							<h4><?php _e( 'Luca Bianchi', 'marcello-scavo' ); ?></h4>
							<span><?php _e( 'Cliente fidelizzato', 'marcello-scavo' ); ?></span>
							<div class="testimonial-rating">★★★★★</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="testimonial-item">
				<div class="testimonial-content">
					<div class="testimonial-quote">
						<span class="quote-icon">❝</span>
						<p><?php _e( '"Tecnica impeccabile e un occhio artistico incredibile. Marcello è riuscito a catturare perfettamente il significato del mio tatuaggio."', 'marcello-scavo' ); ?></p>
					</div>
					<div class="testimonial-author">
						<div class="author-avatar">
							<span class="dashicons dashicons-admin-users"></span>
						</div>
						<div class="author-info">
							<h4><?php _e( 'Anna Verdi', 'marcello-scavo' ); ?></h4>
							<span><?php _e( 'Artista', 'marcello-scavo' ); ?></span>
							<div class="testimonial-rating">★★★★★</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="portfolio-cta-section">
	<div class="portfolio-cta-bg"></div>
	<div class="portfolio-cta-overlay"></div>
	<div class="container">
				<?php if ( is_active_sidebar( 'taxonomy-portfolio-booking' ) ) : ?>
			<!-- Area widget per Bookly plugin o altri sistemi di prenotazione -->
			<div class="taxonomy-portfolio-booking-widget-area">
					<?php dynamic_sidebar( 'taxonomy-portfolio-booking' ); ?>
			</div>
		<?php elseif ( is_active_sidebar( 'taxonomy-portfolio-cta' ) ) : ?>
			<!-- Widget CTA personalizzabili -->
			<div class="taxonomy-portfolio-cta-widget-area">
				<?php dynamic_sidebar( 'taxonomy-portfolio-cta' ); ?>
			</div>
		<?php else : ?>
			<!-- Fallback content con prenotazione integrata -->
			<div class="portfolio-cta-content">
				<h2 class="cta-title"><?php _e( 'Prenota il tuo appuntamento ora', 'marcello-scavo' ); ?></h2>
				<div class="cta-divider"></div>
				<p class="cta-description">
					<?php _e( 'Trasforma la tua idea in arte. Prenota una consulenza gratuita e scopri come possiamo dare vita al tuo tatuaggio perfetto.', 'marcello-scavo' ); ?>
				</p>
				
				<!-- Booking prompt -->
				<div class="cta-booking-section">
					<div class="booking-prompt">
						<p><?php _e( '💡 Per attivare il sistema di prenotazione online, aggiungi il widget Bookly nell\'area "Prenotazioni Portfolio".', 'marcello-scavo' ); ?></p>
					</div>
				</div>
				
				<!-- Contact actions grid -->
				<div class="booking-actions">
					<div class="booking-action-card">
						<div class="booking-action-icon">
							<span class="dashicons dashicons-phone"></span>
						</div>
						<div class="booking-action-text">
							<h4><?php _e( 'Chiamaci', 'marcello-scavo' ); ?></h4>
							<p>+39 333 1234567</p>
						</div>
					</div>
					<div class="booking-action-card">
						<div class="booking-action-icon">
							<span class="dashicons dashicons-email"></span>
						</div>
						<div class="booking-action-text">
							<h4><?php _e( 'Scrivici', 'marcello-scavo' ); ?></h4>
							<p>info@marcelloscavo.it</p>
						</div>
					</div>
					<div class="booking-action-card">
						<div class="booking-action-icon">
							<span class="dashicons dashicons-location-alt"></span>
						</div>
						<div class="booking-action-text">
							<h4><?php _e( 'Contattaci', 'marcello-scavo' ); ?></h4>
							<p><?php _e( 'Vai alla sezione contatti', 'marcello-scavo' ); ?></p>
						</div>
					</div>
				</div>
				
				<!-- WhatsApp booking button -->
				<div class="cta-buttons">
					<?php
					// Numero WhatsApp personalizzabile tramite Customizer
					$whatsapp_number  = get_theme_mod( 'whatsapp_number', '393331234567' );
					$whatsapp_message = get_theme_mod( 'whatsapp_message', 'Ciao! Vorrei prenotare una consulenza per un tatuaggio.' );
					$whatsapp_url     = 'https://wa.me/' . $whatsapp_number . '?text=' . urlencode( $whatsapp_message );
					?>
					<a href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" class="btn btn-primary btn-large">
						<i class="fab fa-whatsapp"></i>
						<?php _e( 'Prenota Consulenza', 'marcello-scavo' ); ?>
					</a>
				</div>
				<div class="cta-features">
					<div class="cta-feature">
						<span class="feature-icon">🎨</span>
						<span><?php _e( 'Consulenza gratuita', 'marcello-scavo' ); ?></span>
					</div>
					<div class="cta-feature">
						<span class="feature-icon">✨</span>
						<span><?php _e( 'Design personalizzato', 'marcello-scavo' ); ?></span>
					</div>
					<div class="cta-feature">
						<span class="feature-icon">🏆</span>
						<span><?php _e( 'Qualità professionale', 'marcello-scavo' ); ?></span>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>


<section class="portfolio-why-section">
	<div class="container">
		<?php if ( is_active_sidebar( 'taxonomy-portfolio-why' ) ) : ?>
			<div class="taxonomy-portfolio-why-widget-area">
				<?php dynamic_sidebar( 'taxonomy-portfolio-why' ); ?>
			</div>
		<?php else : ?>
			<div class="portfolio-why-content">
				<div class="why-text-content">
					<h2 class="section-title"><?php _e( 'Perché Sceglierci?', 'marcello-scavo' ); ?></h2>
					<div class="section-divider"></div>
					<p class="why-description">
						<?php _e( 'Scegliere Marcello Scavo significa affidarsi a professionalità, esperienza e passione per l\'arte. Ogni cliente è unico e ogni progetto viene seguito con cura e attenzione ai dettagli.', 'marcello-scavo' ); ?>
					</p>
					<ul class="why-features-list">
						<li><span class="feature-bullet">✓</span> <?php _e( 'Materiali certificati e sicuri', 'marcello-scavo' ); ?></li>
						<li><span class="feature-bullet">✓</span> <?php _e( 'Studio accogliente e igienizzato', 'marcello-scavo' ); ?></li>
						<li><span class="feature-bullet">✓</span> <?php _e( 'Consulenza personalizzata', 'marcello-scavo' ); ?></li>
						<li><span class="feature-bullet">✓</span> <?php _e( 'Portfolio ricco e variegato', 'marcello-scavo' ); ?></li>
						<li><span class="feature-bullet">✓</span> <?php _e( 'Esperienza decennale nel settore', 'marcello-scavo' ); ?></li>
						<li><span class="feature-bullet">✓</span> <?php _e( 'Stili artistici diversificati', 'marcello-scavo' ); ?></li>
					</ul>
				</div>
				<div class="why-images-content">
					<div class="why-image-grid">
						<div class="why-image-item">
							<div class="why-placeholder-image">
								<span class="dashicons dashicons-format-image"></span>
							</div>
						</div>
						<div class="why-image-item">
							<div class="why-placeholder-image">
								<span class="dashicons dashicons-format-image"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<!-- Contact Section - Same style as homepage -->
<section id="contatti" class="section contact-section">
	<div class="container">
		<div class="section-header">
			<h2 class="section-title">Contattaci per informazioni</h2>
		</div>
		
		<div class="contact-content">
			<?php if ( is_active_sidebar( 'taxonomy-portfolio-contact' ) ) : ?>
				<!-- Widget personalizzabili -->
				<div class="contact-widgets">
					<?php dynamic_sidebar( 'taxonomy-portfolio-contact' ); ?>
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
		<?php if ( is_active_sidebar( 'location-map' ) ) : ?>
			<!-- Widget mappa personalizzabile -->
			<?php dynamic_sidebar( 'location-map' ); ?>
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
