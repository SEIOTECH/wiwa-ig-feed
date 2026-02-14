<?php

namespace WiwaTour\IGFeed\Frontend;

use WiwaTour\IGFeed\Api\Instagram_API;

class Shortcode
{

    public function enqueue_assets()
    {
        wp_register_style('wiwa-ig-style', WIWA_IG_PLUGIN_URL . 'assets/css/style.css', [], WIWA_IG_VERSION);
        wp_register_script('wiwa-ig-script', WIWA_IG_PLUGIN_URL . 'assets/js/script.js', [], WIWA_IG_VERSION, true);
    }

    public function render_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'limit' => null,
        ), $atts, 'wiwa_ig_feed');

        wp_enqueue_style('wiwa-ig-style');
        wp_enqueue_script('wiwa-ig-script');

        $options = get_option('wiwa_tour_ig_options');
        $display_mode = isset($options['display_mode']) ? $options['display_mode'] : 'lightbox';

        $api = new Instagram_API();
        $feed = $api->get_feed($atts['limit']);

        if (is_wp_error($feed)) {
            return '<div class="wiwa-ig-error">Error loading feed.</div>';
        }

        if (empty($feed)) {
            return '<div class="wiwa-ig-empty">No posts found.</div>';
        }

        ob_start();
        ?>
        <div class="wiwa-ig-carousel-container">
            <div class="wiwa-ig-carousel" id="wiwa-ig-carousel">
                <?php foreach ($feed as $post): ?>
                    <?php
                    $image_src = esc_url($post['image_src']);
                    $link = esc_url($post['permalink']);
                    $caption = isset($post['caption']) ? esc_attr($post['caption']) : '';
                    $media_type = isset($post['media_type']) ? $post['media_type'] : 'IMAGE';
                    $media_url = isset($post['media_url']) ? esc_url($post['media_url']) : $image_src;
                    $is_video = 'VIDEO' === $media_type;
                    ?>
                    <div class="wiwa-ig-item">
                        <a href="<?php echo $link; ?>" class="wiwa-ig-link" data-mode="<?php echo esc_attr($display_mode); ?>"
                            data-src="<?php echo $image_src; ?>" data-media-type="<?php echo esc_attr($media_type); ?>"
                            data-media-url="<?php echo $media_url; ?>" target="_blank" rel="noopener noreferrer">

                            <div class="wiwa-ig-media-wrapper">
                                <img src="<?php echo $image_src; ?>" alt="<?php echo $caption; ?>" loading="lazy">
                                <?php if ($is_video): ?>
                                    <div class="wiwa-ig-video-icon">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="10" fill="rgba(0,0,0,0.5)" />
                                            <path d="M10 8L16 12L10 16V8Z" fill="white" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="wiwa-ig-nav prev" aria-label="Previous">&#10094;</button>
            <button class="wiwa-ig-nav next" aria-label="Next">&#10095;</button>
        </div>

        <!-- Lightbox Markup -->
        <div id="wiwa-lightbox" class="wiwa-lightbox">
            <div class="wiwa-lightbox-content">
                <span class="wiwa-lightbox-close">&times;</span>
                <div id="wiwa-lightbox-media"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
