<?php
/**
 * The template for displaying portfolio archive
 *
 * @package MarcelloScavoTattoo
 */

get_header(); ?>

<div class="portfolio-archive">
	<!-- Archive Header -->
	<section class="archive-header section" style="background: linear-gradient(135deg, var(--primary-blue), var(--dark-gray)); color: var(--white); padding-top: calc(var(--spacing-xl) + 80px);">
		<div class="container">
			<div class="text-center">
				<h1 class="archive-title"><?php _e( 'Portfolio Completo', 'marcello-scavo' ); ?></h1>
				<p class="archive-subtitle"><?php _e( 'Una collezione dei miei lavori più significativi', 'marcello-scavo' ); ?></p>
				
				<!-- Portfolio Stats -->
				<div class="portfolio-stats">
					<?php
					$total_portfolio = wp_count_posts( 'portfolio' );
					$total_published = $total_portfolio->publish;
					?>
					<div class="stat-item">
						<span class="stat-number"><?php echo esc_html( $total_published ); ?></span>
						<span class="stat-label"><?php _e( 'Progetti Completati', 'marcello-scavo' ); ?></span>
					</div>
					
					<?php
					// Count different project types
					$tattoo_count = new WP_Query(
						array(
							'post_type'      => 'portfolio',
							'meta_query'     => array(
								array(
									'key'     => '_portfolio_project_type',
									'value'   => 'tattoo',
									'compare' => '=',
								),
							),
							'posts_per_page' => -1,
						)
					);
					?>
					<div class="stat-item">
						<span class="stat-number"><?php echo $tattoo_count->found_posts; ?></span>
						<span class="stat-label"><?php _e( 'Tatuaggi', 'marcello-scavo' ); ?></span>
					</div>
					
					<?php
					$illustration_count = new WP_Query(
						array(
							'post_type'      => 'portfolio',
							'meta_query'     => array(
								array(
									'key'     => '_portfolio_project_type',
									'value'   => 'illustration',
									'compare' => '=',
								),
							),
							'posts_per_page' => -1,
						)
					);
					?>
					<div class="stat-item">
						<span class="stat-number"><?php echo $illustration_count->found_posts; ?></span>
						<span class="stat-label"><?php _e( 'Illustrazioni', 'marcello-scavo' ); ?></span>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<!-- Portfolio Filter and Content -->
	<section class="portfolio-content section">
		<div class="container">
			<!-- Advanced Filter -->
			<div class="portfolio-filters">
				<div class="filter-row">
					<!-- Type Filter -->
					<div class="filter-group">
						<label for="type-filter"><?php _e( 'Tipo:', 'marcello-scavo' ); ?></label>
						<select id="type-filter" class="filter-select">
							<option value=""><?php _e( 'Tutti i Tipi', 'marcello-scavo' ); ?></option>
							<option value="tattoo"><?php _e( 'Tatuaggi', 'marcello-scavo' ); ?></option>
							<option value="illustration"><?php _e( 'Illustrazioni', 'marcello-scavo' ); ?></option>
							<option value="graphic_design"><?php _e( 'Grafica', 'marcello-scavo' ); ?></option>
						</select>
					</div>
					
					<!-- Location Filter -->
					<div class="filter-group">
						<label for="location-filter"><?php _e( 'Luogo:', 'marcello-scavo' ); ?></label>
						<select id="location-filter" class="filter-select">
							<option value=""><?php _e( 'Tutti i Luoghi', 'marcello-scavo' ); ?></option>
							<option value="milano"><?php _e( 'Milano', 'marcello-scavo' ); ?></option>
							<option value="messina"><?php _e( 'Messina', 'marcello-scavo' ); ?></option>
							<option value="altro"><?php _e( 'Altro', 'marcello-scavo' ); ?></option>
						</select>
					</div>
					
					<!-- Category Filter -->
					<div class="filter-group">
						<label for="category-filter"><?php _e( 'Categoria:', 'marcello-scavo' ); ?></label>
						<select id="category-filter" class="filter-select">
							<option value=""><?php _e( 'Tutte le Categorie', 'marcello-scavo' ); ?></option>
							<?php
							$categories = get_terms(
								array(
									'taxonomy'   => 'portfolio_category',
									'hide_empty' => true,
								)
							);

							if ( $categories && ! is_wp_error( $categories ) ) :
								foreach ( $categories as $category ) :
									?>
									<option value="<?php echo esc_attr( $category->slug ); ?>"><?php echo esc_html( $category->name ); ?></option>
									<?php
								endforeach;
							endif;
							?>
						</select>
					</div>
					
					<!-- Sort Filter -->
					<div class="filter-group">
						<label for="sort-filter"><?php _e( 'Ordina per:', 'marcello-scavo' ); ?></label>
						<select id="sort-filter" class="filter-select">
							<option value="date_desc"><?php _e( 'Più Recenti', 'marcello-scavo' ); ?></option>
							<option value="date_asc"><?php _e( 'Più Vecchi', 'marcello-scavo' ); ?></option>
							<option value="title_asc"><?php _e( 'Titolo A-Z', 'marcello-scavo' ); ?></option>
							<option value="title_desc"><?php _e( 'Titolo Z-A', 'marcello-scavo' ); ?></option>
						</select>
					</div>
					
					<!-- Clear Filters -->
					<div class="filter-group">
						<button id="clear-filters" class="btn btn-secondary">
							<i class="fas fa-times"></i> <?php _e( 'Pulisci Filtri', 'marcello-scavo' ); ?>
						</button>
					</div>
				</div>
			</div>
			
			<!-- Results Count -->
			<div class="results-info">
				<span id="results-count"><?php echo $wp_query->found_posts; ?></span> <?php _e( 'progetti trovati', 'marcello-scavo' ); ?>
			</div>
			
			<!-- Portfolio Grid -->
			<div id="portfolio-grid" class="portfolio-grid">
				<?php if ( have_posts() ) : ?>
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<?php
						$project_type     = get_post_meta( get_the_ID(), '_portfolio_project_type', true );
						$project_location = get_post_meta( get_the_ID(), '_portfolio_project_location', true );
						$project_date     = get_post_meta( get_the_ID(), '_portfolio_project_date', true );
						$client_name      = get_post_meta( get_the_ID(), '_portfolio_client_name', true );

						$categories     = get_the_terms( get_the_ID(), 'portfolio_category' );
						$category_slugs = array();
						if ( $categories && ! is_wp_error( $categories ) ) {
							foreach ( $categories as $category ) {
								$category_slugs[] = $category->slug;
							}
						}
						?>
						
						<div class="portfolio-item fade-in-up" 
							data-type="<?php echo esc_attr( $project_type ); ?>"
							data-location="<?php echo esc_attr( $project_location ); ?>"
							data-categories="<?php echo esc_attr( implode( ',', $category_slugs ) ); ?>"
							data-title="<?php echo esc_attr( get_the_title() ); ?>"
							data-date="<?php echo esc_attr( $project_date ); ?>">
							
							<div class="portfolio-image">
								<?php if ( has_post_thumbnail() ) : ?>
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail( 'portfolio-thumb' ); ?>
									</a>
								<?php else : ?>
									<a href="<?php the_permalink(); ?>">
										<img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder-portfolio.jpg" alt="<?php the_title(); ?>">
									</a>
								<?php endif; ?>
								
								<div class="portfolio-overlay">
									<div class="overlay-content">
										<h3><?php the_title(); ?></h3>
										
										<div class="portfolio-meta">
											<?php if ( $client_name ) : ?>
												<p class="client-name"><?php echo esc_html( $client_name ); ?></p>
											<?php endif; ?>
											
											<?php if ( $project_location ) : ?>
												<p class="location">
													<i class="fas fa-map-marker-alt"></i> 
													<?php echo esc_html( ucfirst( $project_location ) ); ?>
												</p>
											<?php endif; ?>
											
											<?php if ( $project_date ) : ?>
												<p class="date">
													<i class="fas fa-calendar"></i> 
													<?php echo esc_html( date( 'F Y', strtotime( $project_date ) ) ); ?>
												</p>
											<?php endif; ?>
										</div>
										
										<div class="portfolio-actions">
											<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
												<i class="fas fa-eye"></i> <?php _e( 'Visualizza', 'marcello-scavo' ); ?>
											</a>
											
											<?php if ( $project_type === 'tattoo' ) : ?>
												<a href="#booking" class="btn btn-secondary btn-sm">
													<i class="fas fa-calendar-plus"></i> <?php _e( 'Prenota', 'marcello-scavo' ); ?>
												</a>
											<?php endif; ?>
										</div>
										
										<!-- Type Badge -->
										<div class="type-badge type-<?php echo esc_attr( $project_type ); ?>">
											<?php
											$type_labels = array(
												'tattoo' => __( 'Tatuaggio', 'marcello-scavo' ),
												'illustration' => __( 'Illustrazione', 'marcello-scavo' ),
												'graphic_design' => __( 'Grafica', 'marcello-scavo' ),
											);
											echo esc_html( $type_labels[ $project_type ] ?? $project_type );
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
					<?php endwhile; ?>
				<?php else : ?>
					<div class="no-portfolio">
						<div class="no-results">
							<i class="fas fa-search"></i>
							<h3><?php _e( 'Nessun progetto trovato', 'marcello-scavo' ); ?></h3>
							<p><?php _e( 'Prova a modificare i filtri di ricerca o torna più tardi per vedere nuovi progetti.', 'marcello-scavo' ); ?></p>
						</div>
					</div>
				<?php endif; ?>
			</div>
			
			<!-- Load More Button -->
			<?php if ( $wp_query->max_num_pages > 1 ) : ?>
				<div class="load-more-container text-center">
					<button id="load-more-portfolio" class="btn btn-primary" data-page="1" data-max="<?php echo $wp_query->max_num_pages; ?>">
						<i class="fas fa-plus"></i> <?php _e( 'Carica Altri Progetti', 'marcello-scavo' ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>
	</section>
	
	<!-- Call to Action -->
	<section class="portfolio-cta section" style="background: linear-gradient(135deg, var(--primary-blue), var(--dark-gray)); color: var(--white);">
		<div class="container text-center">
			<h2><?php _e( 'Ti piace quello che vedi?', 'marcello-scavo' ); ?></h2>
			<p><?php _e( 'Iniziamo a creare insieme il tuo prossimo tatuaggio unico.', 'marcello-scavo' ); ?></p>
			
			<div class="cta-buttons">
				<a href="#booking" class="btn btn-primary btn-lg">
					<i class="fas fa-calendar-plus"></i> <?php _e( 'Prenota Consultazione', 'marcello-scavo' ); ?>
				</a>
				<a href="#contact" class="btn btn-secondary btn-lg">
					<i class="fas fa-envelope"></i> <?php _e( 'Contattami', 'marcello-scavo' ); ?>
				</a>
			</div>
		</div>
	</section>
</div>

<style>
/* Portfolio Archive Styles */
.portfolio-archive {
	padding-top: 0;
}

.archive-header {
	text-align: center;
}

.archive-title {
	font-size: 3.5rem;
	margin-bottom: var(--spacing-sm);
	background: linear-gradient(45deg, var(--primary-gold), var(--secondary-gold));
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
}

.archive-subtitle {
	font-size: 1.3rem;
	margin-bottom: var(--spacing-lg);
	color: var(--light-blue);
}

.portfolio-stats {
	display: flex;
	justify-content: center;
	gap: var(--spacing-lg);
	margin-top: var(--spacing-lg);
}

.stat-item {
	text-align: center;
}

.stat-number {
	display: block;
	font-size: 2.5rem;
	font-weight: 700;
	color: var(--primary-gold);
	margin-bottom: var(--spacing-xs);
}

.stat-label {
	font-size: 0.9rem;
	color: var(--light-blue);
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.portfolio-filters {
	background: var(--white);
	padding: var(--spacing-md);
	border-radius: var(--border-radius-lg);
	box-shadow: var(--shadow-sm);
	margin-bottom: var(--spacing-lg);
}

.filter-row {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: var(--spacing-md);
	align-items: end;
}

.filter-group label {
	display: block;
	margin-bottom: var(--spacing-xs);
	font-weight: 600;
	color: var(--dark-gray);
	font-size: 0.9rem;
}

.filter-select {
	width: 100%;
	padding: var(--spacing-xs) var(--spacing-sm);
	border: 2px solid var(--light-gray);
	border-radius: var(--border-radius);
	font-size: 0.9rem;
	background: var(--white);
	cursor: pointer;
	transition: border-color 0.3s ease;
}

.filter-select:focus {
	outline: none;
	border-color: var(--primary-blue);
}

.results-info {
	margin-bottom: var(--spacing-md);
	color: var(--medium-gray);
	font-weight: 500;
}

.portfolio-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
	gap: var(--spacing-md);
	margin-bottom: var(--spacing-lg);
}

.portfolio-item {
	position: relative;
	border-radius: var(--border-radius-lg);
	overflow: hidden;
	box-shadow: var(--shadow-sm);
	transition: all 0.3s ease;
}

.portfolio-item:hover {
	transform: translateY(-5px);
	box-shadow: var(--shadow-lg);
}

.portfolio-image {
	position: relative;
	overflow: hidden;
}

.portfolio-image img {
	width: 100%;
	height: 300px;
	object-fit: cover;
	transition: transform 0.3s ease;
}

.portfolio-item:hover .portfolio-image img {
	transform: scale(1.1);
}

.portfolio-overlay {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: linear-gradient(to bottom, 
		rgba(30, 58, 138, 0) 0%, 
		rgba(30, 58, 138, 0.7) 60%, 
		rgba(30, 58, 138, 0.95) 100%
	);
	color: var(--white);
	padding: var(--spacing-md);
	display: flex;
	flex-direction: column;
	justify-content: flex-end;
	opacity: 0;
	transition: opacity 0.3s ease;
}

.portfolio-item:hover .portfolio-overlay {
	opacity: 1;
}

.overlay-content h3 {
	color: var(--white);
	margin-bottom: var(--spacing-sm);
	font-size: 1.3rem;
}

.portfolio-meta {
	margin-bottom: var(--spacing-md);
}

.portfolio-meta p {
	margin-bottom: var(--spacing-xs);
	font-size: 0.9rem;
	opacity: 0.9;
}

.portfolio-meta i {
	margin-right: var(--spacing-xs);
	color: var(--primary-gold);
}

.portfolio-actions {
	display: flex;
	gap: var(--spacing-xs);
}

.type-badge {
	position: absolute;
	top: var(--spacing-sm);
	right: var(--spacing-sm);
	padding: var(--spacing-xs) var(--spacing-sm);
	border-radius: var(--border-radius);
	font-size: 0.8rem;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.type-tattoo {
	background: var(--primary-gold);
	color: var(--white);
}

.type-illustration {
	background: var(--primary-blue);
	color: var(--white);
}

.type-graphic_design {
	background: var(--secondary-blue);
	color: var(--white);
}

.no-portfolio {
	grid-column: 1 / -1;
	text-align: center;
	padding: var(--spacing-xl);
}

.no-results i {
	font-size: 4rem;
	color: var(--medium-gray);
	margin-bottom: var(--spacing-md);
}

.no-results h3 {
	color: var(--dark-gray);
	margin-bottom: var(--spacing-sm);
}

.load-more-container {
	margin-top: var(--spacing-lg);
}

.portfolio-cta {
	text-align: center;
}

.portfolio-cta h2 {
	color: var(--primary-gold);
	font-size: 2.5rem;
	margin-bottom: var(--spacing-sm);
}

.cta-buttons {
	display: flex;
	justify-content: center;
	gap: var(--spacing-md);
	margin-top: var(--spacing-lg);
}

@media (max-width: 768px) {
	.archive-title {
		font-size: 2.5rem;
	}
	
	.portfolio-stats {
		flex-direction: column;
		gap: var(--spacing-md);
	}
	
	.filter-row {
		grid-template-columns: 1fr;
	}
	
	.portfolio-grid {
		grid-template-columns: 1fr;
	}
	
	.portfolio-overlay {
		opacity: 1;
		background: linear-gradient(to bottom, 
			transparent 0%, 
			rgba(30, 58, 138, 0.9) 70%
		);
	}
	
	.cta-buttons {
		flex-direction: column;
		align-items: center;
	}
}

/* Loading states for AJAX */
.portfolio-grid.loading {
	opacity: 0.6;
	pointer-events: none;
}

.filter-select.loading {
	background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEwIDNWN00xMCAxN1YxM00zIDEwSDdNMTcgMTBIMTMiIHN0cm9rZT0iIzk5OTk5OSIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KPGF0aW1hdGVUcmFuc2Zvcm0gYXR0cmlidXRlTmFtZT0idHJhbnNmb3JtIiB0eXBlPSJyb3RhdGUiIGR1cj0iMXMiIHZhbHVlcz0iMCAxMCAxMDszNjAgMTAgMTAiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIi8+Cjwvc3ZnPg==');
	background-repeat: no-repeat;
	background-position: right 10px center;
	background-size: 20px;
}
</style>

<script>
jQuery(document).ready(function($) {
	// Portfolio filtering
	let currentFilters = {
		type: '',
		location: '',
		category: '',
		sort: 'date_desc'
	};
	
	// Filter change handlers
	$('#type-filter, #location-filter, #category-filter, #sort-filter').on('change', function() {
		const filterType = $(this).attr('id').replace('-filter', '');
		currentFilters[filterType] = $(this).val();
		filterPortfolio();
	});
	
	// Clear filters
	$('#clear-filters').on('click', function() {
		$('#type-filter, #location-filter, #category-filter').val('');
		$('#sort-filter').val('date_desc');
		currentFilters = {
			type: '',
			location: '',
			category: '',
			sort: 'date_desc'
		};
		filterPortfolio();
	});
	
	function filterPortfolio() {
		const $grid = $('#portfolio-grid');
		const $items = $grid.find('.portfolio-item');
		let visibleCount = 0;
		
		$grid.addClass('loading');
		
		$items.each(function() {
			const $item = $(this);
			let visible = true;
			
			// Type filter
			if (currentFilters.type && $item.data('type') !== currentFilters.type) {
				visible = false;
			}
			
			// Location filter
			if (currentFilters.location && $item.data('location') !== currentFilters.location) {
				visible = false;
			}
			
			// Category filter
			if (currentFilters.category) {
				const categories = $item.data('categories').toString().split(',');
				if (!categories.includes(currentFilters.category)) {
					visible = false;
				}
			}
			
			if (visible) {
				$item.show();
				visibleCount++;
			} else {
				$item.hide();
			}
		});
		
		// Sort visible items
		if (currentFilters.sort) {
			sortPortfolioItems();
		}
		
		// Update results count
		$('#results-count').text(visibleCount);
		
		setTimeout(function() {
			$grid.removeClass('loading');
		}, 300);
	}
	
	function sortPortfolioItems() {
		const $grid = $('#portfolio-grid');
		const $items = $grid.find('.portfolio-item:visible');
		
		$items.sort(function(a, b) {
			const $a = $(a);
			const $b = $(b);
			
			switch (currentFilters.sort) {
				case 'date_desc':
					return new Date($b.data('date')) - new Date($a.data('date'));
				case 'date_asc':
					return new Date($a.data('date')) - new Date($b.data('date'));
				case 'title_asc':
					return $a.data('title').localeCompare($b.data('title'));
				case 'title_desc':
					return $b.data('title').localeCompare($a.data('title'));
				default:
					return 0;
			}
		});
		
		$grid.append($items);
	}
	
	// Load more functionality (if needed)
	$('#load-more-portfolio').on('click', function() {
		const $button = $(this);
		const page = parseInt($button.data('page')) + 1;
		const maxPages = parseInt($button.data('max'));
		
		if (page > maxPages) {
			return;
		}
		
		$button.addClass('loading').prop('disabled', true);
		$button.html('<i class="fas fa-spinner fa-spin"></i> Caricamento...');
		
		// AJAX load more (implement as needed)
		// This would require additional PHP AJAX handler
		
		setTimeout(function() {
			$button.removeClass('loading').prop('disabled', false);
			$button.html('<i class="fas fa-plus"></i> Carica Altri Progetti');
			$button.data('page', page);
			
			if (page >= maxPages) {
				$button.hide();
			}
		}, 1000);
	});
});
</script>

<?php get_footer(); ?>
