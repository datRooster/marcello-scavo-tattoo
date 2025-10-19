<?php

/**
 * 3D Gallery Hero Widget per categorie Portfolio
 * Crea una stanza 3D virtuale con quadri alle pareti
 */

class Marcello_Scavo_3D_Gallery_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'marcello_3d_gallery',
            __('🎨 3D Gallery Hero', 'marcello-scavo-tattoo'),
            array(
                'description' => __('Mostra una galleria 3D interattiva per categorie Illustrazioni e Disegni', 'marcello-scavo-tattoo')
            )
        );
    }

    public function widget($args, $instance)
    {
        // Verifica se siamo in una pagina categoria portfolio
        if (!is_tax('portfolio_category')) {
            return;
        }

        $current_term = get_queried_object();
        $category_slug = $current_term->slug;

        // Solo per le categorie specifiche (illustrazioni, disegni, quadri, arte)
        $target_categories = array('illustrazioni', 'disegni', 'quadri', 'arte', 'paintings', 'drawings');

        if (!in_array($category_slug, $target_categories)) {
            return;
        }

        // Ottieni le immagini della categoria corrente
        $gallery_posts = get_posts(array(
            'post_type' => 'portfolio',
            'posts_per_page' => 8,
            'tax_query' => array(
                array(
                    'taxonomy' => 'portfolio_category',
                    'field'    => 'term_id',
                    'terms'    => $current_term->term_id,
                )
            ),
            'meta_query' => array(
                array(
                    'key' => '_thumbnail_id',
                    'compare' => 'EXISTS'
                )
            )
        ));

        $title = isset($instance['title']) ? $instance['title'] : $current_term->name;
        $subtitle = isset($instance['subtitle']) ? $instance['subtitle'] : $current_term->description;
        $room_style = isset($instance['room_style']) ? $instance['room_style'] : 'modern';
        $enable_parallax = isset($instance['enable_parallax']) ? $instance['enable_parallax'] : true;
        $enable_3d = isset($instance['enable_3d']) ? $instance['enable_3d'] : true;

        echo $args['before_widget'];
?>

        <div class="hero-3d-gallery" data-room-style="<?php echo esc_attr($room_style); ?>" data-category="<?php echo esc_attr($category_slug); ?>">
            <!-- Loading Screen -->
            <div class="gallery-loading">
                <div class="loading-spinner"></div>
                <p><?php _e('Caricamento galleria 3D...', 'marcello-scavo-tattoo'); ?></p>
            </div>

            <!-- 3D Canvas Container -->
            <?php if ($enable_3d) : ?>
                <div class="canvas-3d-container">
                    <canvas id="gallery-3d-canvas"></canvas>

                    <!-- 3D Controls Overlay -->
                    <div class="gallery-3d-controls">
                        <div class="control-hint">
                            <i class="fas fa-mouse-pointer"></i>
                            <span><?php _e('Trascina per guardare intorno', 'marcello-scavo-tattoo'); ?></span>
                        </div>
                        <div class="control-hint">
                            <i class="fas fa-search-plus"></i>
                            <span><?php _e('Scroll per zoom', 'marcello-scavo-tattoo'); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Parallax Hero Section -->
            <div class="hero-3d-content <?php echo $enable_3d ? 'with-3d' : 'parallax-only'; ?>">

                <!-- Parallax Background Layers -->
                <?php if ($enable_parallax) : ?>
                    <div class="parallax-layers">
                        <div class="parallax-layer" data-speed="0.1">
                            <div class="room-back-wall"></div>
                        </div>
                        <div class="parallax-layer" data-speed="0.3">
                            <div class="room-side-walls"></div>
                        </div>
                        <div class="parallax-layer" data-speed="0.5">
                            <div class="room-floor"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Gallery Frames Overlay - DISABILITATO per test 3D skeleton -->
                <div class="gallery-frames-container" style="display: none;">
                    <?php
                    /*
                    // Temporaneamente disabilitato per testare skeleton 3D
                    $frame_positions = array(
                        array('left' => '15%', 'top' => '20%', 'size' => 'large'),
                        array('left' => '60%', 'top' => '15%', 'size' => 'medium'),
                        array('left' => '35%', 'top' => '40%', 'size' => 'small'),
                        array('left' => '75%', 'top' => '45%', 'size' => 'medium'),
                        array('left' => '10%', 'top' => '60%', 'size' => 'small'),
                        array('left' => '50%', 'top' => '70%', 'size' => 'large'),
                    );

                    foreach ($gallery_posts as $index => $post) :
                        if ($index >= count($frame_positions)) break;
                        $position = $frame_positions[$index];
                        $image_url = get_the_post_thumbnail_url($post->ID, 'large');
                    ?>
                        <div class="gallery-frame frame-<?php echo esc_attr($position['size']); ?>"
                            style="left: <?php echo $position['left']; ?>; top: <?php echo $position['top']; ?>;"
                            data-parallax-speed="<?php echo 0.8 + ($index * 0.1); ?>">
                            <div class="frame-border">
                                <img src="<?php echo esc_url($image_url); ?>"
                                    alt="<?php echo esc_attr($post->post_title); ?>"
                                    loading="lazy">
                                <div class="frame-glass-effect"></div>
                            </div>
                            <div class="frame-spotlight"></div>
                        </div>
                    <?php endforeach; */
                    ?>
                </div>

                <!-- Hero Text Content -->
                <div class="hero-text-content">
                    <div class="container">
                        <div class="hero-content-inner">
                            <div class="hero-label">
                                <span class="gallery-icon">🎨</span>
                                <span data-translatable="true" data-original-text="GALLERIA VIRTUALE">
                                    <?php _e('GALLERIA VIRTUALE', 'marcello-scavo-tattoo'); ?>
                                </span>
                            </div>

                            <h1 class="hero-title">
                                <span data-translatable="true" data-original-text="<?php echo esc_attr($title); ?>">
                                    <?php echo esc_html($title); ?>
                                </span>
                            </h1>

                            <p class="hero-description">
                                <span data-translatable="true" data-original-text="<?php echo esc_attr($subtitle); ?>">
                                    <?php echo esc_html($subtitle); ?>
                                </span>
                            </p>

                            <div class="hero-actions">
                                <button class="btn btn-gold btn-3d" id="toggle-3d-mode">
                                    <i class="fas fa-cube"></i>
                                    <span data-translatable="true" data-original-text="Esplora in 3D">
                                        <?php _e('Esplora in 3D', 'marcello-scavo-tattoo'); ?>
                                    </span>
                                </button>

                                <button class="btn btn-outline-light btn-gallery-tour" id="start-gallery-tour">
                                    <i class="fas fa-route"></i>
                                    <span data-translatable="true" data-original-text="Tour Guidato">
                                        <?php _e('Tour Guidato', 'marcello-scavo-tattoo'); ?>
                                    </span>
                                </button>
                            </div>

                            <!-- Gallery Stats -->
                            <div class="gallery-stats">
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo count($gallery_posts); ?></span>
                                    <span class="stat-label"><?php _e('Opere Esposte', 'marcello-scavo-tattoo'); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number">360°</span>
                                    <span class="stat-label"><?php _e('Esperienza', 'marcello-scavo-tattoo'); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number">HD</span>
                                    <span class="stat-label"><?php _e('Qualità', 'marcello-scavo-tattoo'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ambient Lighting Effects -->
                <div class="ambient-lighting">
                    <div class="light-beam light-1"></div>
                    <div class="light-beam light-2"></div>
                    <div class="light-beam light-3"></div>
                </div>

                <!-- Audio Controls (Optional) -->
                <div class="gallery-audio-controls">
                    <button id="toggle-ambient-sound" class="audio-toggle">
                        <i class="fas fa-volume-up"></i>
                        <span><?php _e('Suoni Ambientali', 'marcello-scavo-tattoo'); ?></span>
                    </button>
                </div>
            </div>

            <!-- Gallery Data for JS -->
            <script type="application/json" id="gallery-data">
                <?php
                $images_data = array();
                foreach ($gallery_posts as $post) {
                    $images_data[] = array(
                        'id' => $post->ID,
                        'title' => $post->post_title,
                        'url' => get_the_post_thumbnail_url($post->ID, 'full'),
                        'medium' => get_the_post_thumbnail_url($post->ID, 'medium'),
                        'permalink' => get_permalink($post->ID)
                    );
                }

                $gallery_data = array(
                    'category' => $category_slug,
                    'images' => $images_data,
                    'settings' => array(
                        'roomStyle' => $room_style,
                        'enableParallax' => $enable_parallax,
                        'enable3D' => $enable_3d
                    )
                );

                echo json_encode($gallery_data, JSON_PRETTY_PRINT);
                ?>
            </script>
        </div>

    <?php
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $subtitle = isset($instance['subtitle']) ? $instance['subtitle'] : '';
        $room_style = isset($instance['room_style']) ? $instance['room_style'] : 'modern';
        $enable_parallax = isset($instance['enable_parallax']) ? $instance['enable_parallax'] : true;
        $enable_3d = isset($instance['enable_3d']) ? $instance['enable_3d'] : true;
    ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titolo:', 'marcello-scavo-tattoo'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                name="<?php echo $this->get_field_name('title'); ?>" type="text"
                value="<?php echo esc_attr($title); ?>"
                placeholder="<?php _e('Usa titolo categoria automatico', 'marcello-scavo-tattoo'); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('Sottotitolo:', 'marcello-scavo-tattoo'); ?></label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>"
                name="<?php echo $this->get_field_name('subtitle'); ?>" rows="3"
                placeholder="<?php _e('Usa descrizione categoria automatica', 'marcello-scavo-tattoo'); ?>"><?php echo esc_textarea($subtitle); ?></textarea>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('room_style'); ?>"><?php _e('Stile Stanza:', 'marcello-scavo-tattoo'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('room_style'); ?>"
                name="<?php echo $this->get_field_name('room_style'); ?>">
                <option value="modern" <?php selected($room_style, 'modern'); ?>>
                    <?php _e('Moderno (Bianco/Minimalista)', 'marcello-scavo-tattoo'); ?>
                </option>
                <option value="classic" <?php selected($room_style, 'classic'); ?>>
                    <?php _e('Classico (Legno/Tradizionale)', 'marcello-scavo-tattoo'); ?>
                </option>
                <option value="industrial" <?php selected($room_style, 'industrial'); ?>>
                    <?php _e('Industriale (Mattoni/Metallo)', 'marcello-scavo-tattoo'); ?>
                </option>
                <option value="artistic" <?php selected($room_style, 'artistic'); ?>>
                    <?php _e('Artistico (Colorato/Creativo)', 'marcello-scavo-tattoo'); ?>
                </option>
            </select>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($enable_3d); ?>
                id="<?php echo $this->get_field_id('enable_3d'); ?>"
                name="<?php echo $this->get_field_name('enable_3d'); ?>" />
            <label for="<?php echo $this->get_field_id('enable_3d'); ?>">
                <?php _e('Abilita rendering 3D (Three.js)', 'marcello-scavo-tattoo'); ?>
            </label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($enable_parallax); ?>
                id="<?php echo $this->get_field_id('enable_parallax'); ?>"
                name="<?php echo $this->get_field_name('enable_parallax'); ?>" />
            <label for="<?php echo $this->get_field_id('enable_parallax'); ?>">
                <?php _e('Abilita effetti parallax', 'marcello-scavo-tattoo'); ?>
            </label>
        </p>

        <p style="font-size: 11px; color: #666;">
            <strong><?php _e('Nota:', 'marcello-scavo-tattoo'); ?></strong>
            <?php _e('Questo widget funziona solo nelle pagine categoria portfolio per "Illustrazioni", "Disegni", "Quadri" o "Arte".', 'marcello-scavo-tattoo'); ?>
        </p>
<?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['subtitle'] = (!empty($new_instance['subtitle'])) ? sanitize_textarea_field($new_instance['subtitle']) : '';
        $instance['room_style'] = (!empty($new_instance['room_style'])) ? sanitize_text_field($new_instance['room_style']) : 'modern';
        $instance['enable_parallax'] = !empty($new_instance['enable_parallax']);
        $instance['enable_3d'] = !empty($new_instance['enable_3d']);
        return $instance;
    }
}

// Registra il widget
function marcello_scavo_register_3d_gallery_widget()
{
    register_widget('Marcello_Scavo_3D_Gallery_Widget');
}
add_action('widgets_init', 'marcello_scavo_register_3d_gallery_widget');
