<?php
/**
 * Template Name: Portfolio
 * Description: Pagina portfolio personalizzata con layout avanzato e aree widget.
 *
 * @package MarcelloScavoTattoo
 */

get_header(); ?>

<section class="portfolio-hero">
	<div class="portfolio-hero-bg"></div>
	<div class="portfolio-hero-overlay"></div>
	<div class="container">
		<div class="portfolio-hero-content">
			<h1 class="portfolio-hero-title">
				<?php echo get_theme_mod( 'portfolio_hero_title', 'Benvenuto nel mio mondo' ); ?>
			</h1>
			<div class="portfolio-hero-divider"></div>
			<p class="portfolio-hero-desc">
				<?php echo get_theme_mod( 'portfolio_hero_desc', 'Qui trovi la mia selezione di tatuaggi e opere d\'arte realizzate con passione e creatività.' ); ?>
			</p>
			<div class="portfolio-hero-cta">
				<a href="#galleria" class="btn btn-outline-light btn-hero"><?php _e( 'Esplora la Galleria', 'marcello-scavo' ); ?></a>
			</div>
		</div>
	</div>
	<div class="portfolio-hero-scroll-indicator">
		<div class="scroll-arrow"></div>
	</div>
</section>

<section id="galleria" class="portfolio-gallery-section">
	<div class="container">
		<div class="section-header">
			<h2 class="section-title">Galleria d'Arte</h2>
			<div class="section-divider"></div>
			<p class="section-subtitle">
				<?php echo get_theme_mod( 'portfolio_gallery_desc', 'Scorri la selezione delle mie opere, dalla pittura al tatuaggio.' ); ?>
			</p>
		</div>
		
		<!-- Verifica prima il widget slider, poi quello normale -->
		<?php if ( is_active_sidebar( 'portfolio-gallery-slider' ) ) : ?>
			<div class="portfolio-gallery-slider-area">
				<?php dynamic_sidebar( 'portfolio-gallery-slider' ); ?>
			</div>
		<?php elseif ( is_active_sidebar( 'portfolio-gallery' ) ) : ?>
			<div class="portfolio-gallery-widget-area">
				<?php dynamic_sidebar( 'portfolio-gallery' ); ?>
			</div>
		<?php else : ?>
			<div class="portfolio-gallery-fallback">
				<div class="portfolio-showcase-grid">
					<?php
					// Mostra alcuni elementi del portfolio se non ci sono widget configurati
					$portfolio_query = new WP_Query(
						array(
							'post_type'      => 'portfolio',
							'posts_per_page' => 6,
							'meta_query'     => array(
								array(
									'key'     => '_thumbnail_id',
									'compare' => 'EXISTS',
								),
							),
						)
					);

					if ( $portfolio_query->have_posts() ) :
						while ( $portfolio_query->have_posts() ) :
							$portfolio_query->the_post();
							$image_url = get_the_post_thumbnail_url( get_the_ID(), 'portfolio-thumb' );
							?>
						<div class="portfolio-showcase-item">
							<div class="portfolio-showcase-image">
								<?php if ( $image_url ) : ?>
									<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
								<?php endif; ?>
								<div class="portfolio-showcase-overlay">
									<div class="portfolio-showcase-content">
										<h4><?php the_title(); ?></h4>
										<a href="<?php the_permalink(); ?>" class="portfolio-showcase-link"><?php _e( 'Visualizza', 'marcello-scavo' ); ?></a>
									</div>
								</div>
							</div>
						</div>
							<?php
						endwhile;
						wp_reset_postdata();
					else :
						?>
						<div class="portfolio-gallery-placeholder">
							<?php for ( $i = 0;$i < 6;$i++ ) : ?>
								<div class="placeholder-item">
									<span class="dashicons dashicons-format-image"></span>
								</div>
							<?php endfor; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="portfolio-latest-section">
	<div class="container">
		<div class="section-header">
			<h2 class="section-title">Scopri i miei tatuaggi recenti</h2>
			<div class="section-divider"></div>
			<p class="section-subtitle">
				<?php echo get_theme_mod( 'portfolio_latest_desc', 'Una selezione dei lavori più recenti, tra tradizione e innovazione.' ); ?>
			</p>
		</div>
		
		<?php if ( is_active_sidebar( 'portfolio-latest' ) ) : ?>
			<div class="portfolio-latest-widget-area">
				<?php dynamic_sidebar( 'portfolio-latest' ); ?>
			</div>
		<?php else : ?>
			<div class="portfolio-latest-grid">
				<?php
				// Mostra tatuaggi recenti dal portfolio
				$recent_query = new WP_Query(
					array(
						'post_type'      => 'portfolio',
						'posts_per_page' => 4,
						'meta_query'     => array(
							array(
								'key'     => '_thumbnail_id',
								'compare' => 'EXISTS',
							),
						),
						'orderby'        => 'date',
						'order'          => 'DESC',
					)
				);

				if ( $recent_query->have_posts() ) :
					while ( $recent_query->have_posts() ) :
						$recent_query->the_post();
						$image_url  = get_the_post_thumbnail_url( get_the_ID(), 'portfolio-large' );
						$categories = get_the_terms( get_the_ID(), 'portfolio_category' );
						?>
					<div class="portfolio-latest-item">
						<div class="portfolio-latest-image">
							<?php if ( $image_url ) : ?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
							<?php endif; ?>
							<div class="portfolio-latest-overlay">
								<div class="portfolio-latest-content">
									<div class="portfolio-latest-categories">
										<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
											<?php foreach ( $categories as $category ) : ?>
												<span class="portfolio-category-badge"><?php echo esc_html( $category->name ); ?></span>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>
									<h3 class="portfolio-latest-title"><?php the_title(); ?></h3>
									<?php if ( has_excerpt() ) : ?>
										<p class="portfolio-latest-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></p>
									<?php endif; ?>
									<div class="portfolio-latest-meta">
										<span class="portfolio-latest-date"><?php echo get_the_date( 'd M Y' ); ?></span>
									</div>
									<a href="<?php the_permalink(); ?>" class="portfolio-latest-link"><?php _e( 'Scopri di più', 'marcello-scavo' ); ?></a>
								</div>
							</div>
						</div>
					</div>
						<?php
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<!-- Fallback con placeholder -->
					<div class="portfolio-latest-fallback">
						<?php for ( $i = 0;$i < 4;$i++ ) : ?>
							<div class="portfolio-latest-placeholder">
								<div class="placeholder-image">
									<span class="dashicons dashicons-format-image"></span>
								</div>
								<div class="placeholder-content">
									<h3>Tatuaggio Recente <?php echo $i + 1; ?></h3>
									<p>Descrizione del lavoro realizzato di recente con tecniche innovative.</p>
									<span class="placeholder-date"><?php echo date( 'd M Y', strtotime( '-' . $i . ' days' ) ); ?></span>
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
		<div class="section-header">
			<h2 class="section-title">Cosa pensano i nostri clienti</h2>
			<div class="section-divider"></div>
			<p class="section-subtitle">Le parole di chi ha vissuto l'esperienza del tatuaggio con noi</p>
		</div>
		
		<?php if ( is_active_sidebar( 'portfolio-testimonials' ) ) : ?>
			<div class="portfolio-testimonials-widget-area">
				<?php dynamic_sidebar( 'portfolio-testimonials' ); ?>
			</div>
		<?php else : ?>
			<div class="testimonials-grid">
				<div class="testimonial-item">
					<div class="testimonial-content">
						<div class="testimonial-quote">
							<span class="quote-icon">❝</span>
							<p>"Esperienza straordinaria! Marcello ha saputo trasformare la mia idea in un'opera d'arte sulla pelle. Professionalità e creatività ai massimi livelli."</p>
						</div>
						<div class="testimonial-author">
							<div class="author-avatar">
								<span class="dashicons dashicons-admin-users"></span>
							</div>
							<div class="author-info">
								<h4>Maria Rossi</h4>
								<span>Cliente soddisfatta</span>
								<div class="testimonial-rating">
									★★★★★
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="testimonial-item">
					<div class="testimonial-content">
						<div class="testimonial-quote">
							<span class="quote-icon">❝</span>
							<p>"Ambiente accogliente, massima igiene e un artista che sa davvero ascoltare. Il mio tatuaggio ha superato ogni aspettativa!"</p>
						</div>
						<div class="testimonial-author">
							<div class="author-avatar">
								<span class="dashicons dashicons-admin-users"></span>
							</div>
							<div class="author-info">
								<h4>Luca Bianchi</h4>
								<span>Cliente fidelizzato</span>
								<div class="testimonial-rating">
									★★★★★
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="testimonial-item">
					<div class="testimonial-content">
						<div class="testimonial-quote">
							<span class="quote-icon">❝</span>
							<p>"Tecnica impeccabile e un occhio artistico incredibile. Marcello è riuscito a catturare perfettamente il significato del mio tatuaggio."</p>
						</div>
						<div class="testimonial-author">
							<div class="author-avatar">
								<span class="dashicons dashicons-admin-users"></span>
							</div>
							<div class="author-info">
								<h4>Anna Verdi</h4>
								<span>Artista</span>
								<div class="testimonial-rating">
									★★★★★
								</div>
							</div>
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
		<?php if ( is_active_sidebar( 'portfolio-cta' ) ) : ?>
			<div class="portfolio-cta-widget-area">
				<?php dynamic_sidebar( 'portfolio-cta' ); ?>
			</div>
		<?php else : ?>
			<div class="portfolio-cta-content">
				<h2 class="cta-title"><?php echo get_theme_mod( 'portfolio_cta_title', 'Prenota il tuo appuntamento ora' ); ?></h2>
				<div class="cta-divider"></div>
				<p class="cta-description">
					<?php echo get_theme_mod( 'portfolio_cta_desc', 'Trasforma la tua idea in arte. Prenota una consulenza gratuita e scopri come possiamo dare vita al tuo tatuaggio perfetto.' ); ?>
				</p>
				<div class="cta-buttons">
					<a href="#contatti" class="btn btn-primary btn-large"><?php _e( 'Prenota Consulenza', 'marcello-scavo' ); ?></a>
					<a href="<?php echo esc_url( get_permalink( get_page_by_title( 'Portfolio' ) ) ); ?>" class="btn btn-outline btn-large"><?php _e( 'Vedi Portfolio', 'marcello-scavo' ); ?></a>
				</div>
				<div class="cta-features">
					<div class="cta-feature">
						<span class="feature-icon">🎨</span>
						<span>Consulenza gratuita</span>
					</div>
					<div class="cta-feature">
						<span class="feature-icon">✨</span>
						<span>Design personalizzato</span>
					</div>
					<div class="cta-feature">
						<span class="feature-icon">🏆</span>
						<span>Esperienza garantita</span>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>


<section class="portfolio-why-section" style="padding: 40px 0;">
	<div class="container" style="display: flex; flex-wrap: wrap; gap: 40px; align-items: flex-start;">
		<div style="flex:1 1 320px; min-width:260px;">
			<h2 class="section-title" style="color: var(--secondary-color);">Perché Sceglierci?</h2>
			<p style="color: var(--medium-gray); margin-bottom: 16px;">Scegliere Marcello Scavo significa affidarsi a professionalità, esperienza e passione per l’arte. Ogni cliente è unico e ogni progetto viene seguito con cura e attenzione ai dettagli.</p>
			<ul style="color: var(--secondary-color); list-style: disc inside; margin-bottom: 16px;">
				<li>Materiali certificati e sicuri</li>
				<li>Studio accogliente e igienizzato</li>
				<li>Consulenza personalizzata</li>
				<li>Portfolio ricco e variegato</li>
			</ul>
		</div>
		<div style="flex:1 1 260px; min-width:180px; display: flex; gap: 16px;">
			<div style="flex:1;background:#f3f3f3;border-radius:12px;min-height:120px;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:2em;">
				<span class="dashicons dashicons-format-image"></span>
			</div>
			<div style="flex:1;background:#f3f3f3;border-radius:12px;min-height:120px;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:2em;">
				<span class="dashicons dashicons-format-image"></span>
			</div>
		</div>
	</div>
</section>

<section class="portfolio-contact-section" style="padding: 40px 0; background: var(--light-gray);">
	<div class="container" style="display: flex; flex-wrap: wrap; gap: 40px; align-items: flex-start;">
		<div style="flex:1 1 320px; min-width:260px;">
			<h2 class="section-title" style="color: var(--secondary-color);">Contattaci</h2>
			<p style="color: var(--medium-gray); margin-bottom: 12px;">Scrivici per informazioni, preventivi o per prenotare una consulenza personalizzata.</p>
			<ul style="color: var(--secondary-color); list-style: none; padding:0; margin-bottom: 12px;">
				<li><strong>Email:</strong> info@marcelloscavo.it</li>
				<li><strong>Telefono:</strong> +39 333 1234567</li>
				<li><strong>Studio:</strong> Via Roma 123, Milano</li>
			</ul>
			<div style="margin-bottom:8px;">
				<a href="#" style="color:var(--secondary-color);margin-right:12px;"><span class="dashicons dashicons-facebook"></span></a>
				<a href="#" style="color:var(--secondary-color);margin-right:12px;"><span class="dashicons dashicons-instagram"></span></a>
				<a href="#" style="color:var(--secondary-color);"><span class="dashicons dashicons-email"></span></a>
			</div>
		</div>
		<div style="flex:1 1 320px; min-width:260px;">
			<iframe src="https://www.openstreetmap.org/export/embed.html?bbox=9.189982,45.464211,9.191982,45.466211&amp;layer=mapnik" style="width:100%;height:220px;border-radius:12px;border:1px solid #e0e0e0;"></iframe>
		</div>
	</div>
</section>

<?php get_footer(); ?>
