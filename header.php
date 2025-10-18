<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?php bloginfo('description'); ?>">
	<meta name="author" content="Marcello Scavo">
	<meta name="keywords" content="tatuaggio, tattoo, Marcello Scavo, Milano, Messina, illustrazione, arte, design">

	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?php echo esc_url(home_url('/')); ?>">
	<meta property="og:title" content="<?php echo wp_get_document_title(); ?>">
	<meta property="og:description" content="<?php bloginfo('description'); ?>">
	<meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/assets/images/og-image.jpg">

	<!-- Twitter -->
	<meta property="twitter:card" content="summary_large_image">
	<meta property="twitter:url" content="<?php echo esc_url(home_url('/')); ?>">
	<meta property="twitter:title" content="<?php echo wp_get_document_title(); ?>">"
	<meta property="twitter:description" content="<?php bloginfo('description'); ?>">
	<meta property="twitter:image" content="<?php echo get_template_directory_uri(); ?>/assets/images/og-image.jpg">

	<!-- Favicon -->
	<link rel="icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.ico">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/assets/images/apple-touch-icon.png">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<div id="page" class="site">
		<header class="site-header">
			<div class="container">
				<div class="header-content">
					<!-- Logo/Brand -->
					<div class="site-branding">
						<?php if (has_custom_logo()) : ?>
							<div class="site-logo-img">
								<?php the_custom_logo(); ?>
							</div>
						<?php else : ?>
							<a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
								<?php bloginfo('name'); ?>
							</a>
						<?php endif; ?>
					</div>

					<!-- Main Navigation -->
					<nav class="main-navigation" role="navigation" aria-label="<?php _e('Menu Principale', 'marcello-scavo-tattoo'); ?>">
						<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
							<span class="menu-toggle-text"><?php _e('Menu', 'marcello-scavo-tattoo'); ?></span>
							<div class="hamburger">
								<span></span>
								<span></span>
								<span></span>
							</div>
						</button>

						<?php
						wp_nav_menu(array(
							'theme_location' => 'primary',
							'menu_id'        => 'primary-menu',
							'menu_class'     => 'primary-menu',
							'container'      => false,
							'fallback_cb'    => 'marcello_scavo_default_menu',
						));
						?>
					</nav>

					<!-- Header Actions -->
					<div class="header-actions">
						<!-- Language switcher rimosso - sistema in sviluppo, disponibile solo in backend -->
					</div>
				</div>
			</div>
		</header>

		<main id="primary" class="site-main">

			<?php
			/**
			 * Default menu fallback
			 */
			function marcello_scavo_default_menu()
			{
			?>
				<ul id="primary-menu" class="primary-menu">
					<li class="menu-item">
						<a href="<?php echo esc_url(home_url('/')); ?>">
							<span data-translatable="true" data-original-text="Home">Home</span>
						</a>
					</li>
					<li class="menu-item">
						<a href="#about">
							<span data-translatable="true" data-original-text="Chi Sono">Chi Sono</span>
						</a>
					</li>
					<?php
					$portfolio_cats = get_terms(array(
						'taxonomy' => 'portfolio_category',
						'hide_empty' => true,
					));
					if (!empty($portfolio_cats) && !is_wp_error($portfolio_cats)) {
						foreach ($portfolio_cats as $cat) {
							echo '<li class="menu-item"><a href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a></li>';
						}
					}
					?>
					<li class="menu-item">
						<a href="#contact">
							<span data-translatable="true" data-original-text="Contatti">Contatti</span>
						</a>
					</li>
				</ul>
			<?php
			}
			?>