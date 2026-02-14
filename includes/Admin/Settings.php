<?php

namespace WiwaTour\IGFeed\Admin;

class Settings
{

    private $option_name = 'wiwa_tour_ig_options';

    public function add_plugin_page() {
		$hook = add_options_page(
			'Wiwa Instagram Feed',
			'Instagram Feed',
			'manage_options',
			'wiwa-tour-ig-feed',
			[ $this, 'create_admin_page' ]
		);
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
	}

    public function create_admin_page() {
		?>
		<div class="wiwa-admin-wrap">
            <div class="wiwa-admin-header">
                <h1><span class="dashicons dashicons-camera"></span> Wiwa Tour Instagram Feed</h1>
                <p>Configura la integración con la API de Instagram para mostrar tu feed profesionalmente.</p>
            </div>
            <div class="wiwa-admin-content">
                <div class="wiwa-admin-main">
                    <div class="wiwa-admin-card">
                        <form method="post" action="options.php">
                            <?php
                            settings_fields( 'wiwa_tour_ig_option_group' );
                            do_settings_sections( 'wiwa-tour-ig-feed-admin' );
                            submit_button( 'Guardar Configuración', 'primary large' );
                            ?>
                        </form>
                    </div>
                </div>
                <div class="wiwa-admin-sidebar">
                    <div class="wiwa-admin-card wiwa-widget">
                        <h3><span class="dashicons dashicons-shortcode"></span> Shortcode</h3>
                        <p>Copia y pega este shortcode en cualquier página o post:</p>
                        <div class="wiwa-code-block">
                            <code>[wiwa_ig_feed]</code>
                            <button type="button" class="copy-btn" onclick="navigator.clipboard.writeText('[wiwa_ig_feed]'); alert('Copiado!');"><span class="dashicons dashicons-clipboard"></span></button>
                        </div>
                        <p class="description">Parámetros opcionales:</p>
                        <code>[wiwa_ig_feed limit="6"]</code>
                    </div>

                    <div class="wiwa-admin-card wiwa-widget">
                        <h3><span class="dashicons dashicons-book"></span> Instrucciones</h3>
                        <ol class="wiwa-instructions-list">
                            <li>Genera un <strong>Access Token</strong> de Instagram válido.</li>
                            <li>Pégalo en el campo de configuración.</li>
                            <li>Define cuántos posts deseas mostrar.</li>
                            <li>Selecciona si deseas abrir la imagen en un <strong>Modal</strong> o ir a <strong>Instagram</strong>.</li>
                            <li>Usa el shortcode donde quieras mostrar el feed.</li>
                        </ol>
                        <div class="wiwa-alert">
                            <span class="dashicons dashicons-info"></span> El feed se actualiza cada <strong><?php echo esc_html( get_option( 'wiwa_tour_ig_options' )['cache_time'] ?? 60 ); ?> minutos</strong> para optimizar velocidad.
                        </div>
                    </div>
                </div>
            </div>
            <div class="wiwa-admin-footer">
                <p>Desarrollado por el equipo de tecnología de Wiwa Tour.</p>
            </div>
		</div>
		<?php
	}

    public function register_settings()
    {
        register_setting(
            'wiwa_tour_ig_option_group',
            $this->option_name,
        [$this, 'sanitize']
        );

        add_settings_section(
            'wiwa_tour_ig_setting_section',
            'API Settings',
            null,
            'wiwa-tour-ig-feed-admin'
        );

        add_settings_field(
            'access_token',
            'Instagram Access Token',
        [$this, 'access_token_callback'],
            'wiwa-tour-ig-feed-admin',
            'wiwa_tour_ig_setting_section'
        );

        add_settings_field(
            'post_limit',
            'Post Limit',
        [$this, 'post_limit_callback'],
            'wiwa-tour-ig-feed-admin',
            'wiwa_tour_ig_setting_section'
        );

        add_settings_field(
            'display_mode',
            'Display Mode',
        [$this, 'display_mode_callback'],
            'wiwa-tour-ig-feed-admin',
            'wiwa_tour_ig_setting_section'
        );

        add_settings_field(
            'cache_time',
            'Cache Time (Minutes)',
        [$this, 'cache_time_callback'],
            'wiwa-tour-ig-feed-admin',
            'wiwa_tour_ig_setting_section'
        );
    }

    public function sanitize($input)
    {
        $new_input = [];
        if (isset($input['access_token'])) {
            $new_input['access_token'] = sanitize_text_field($input['access_token']);
        }
        if (isset($input['post_limit'])) {
            $new_input['post_limit'] = absint($input['post_limit']);
        }
        if (isset($input['display_mode'])) {
            $new_input['display_mode'] = sanitize_text_field($input['display_mode']);
        }
        if (isset($input['cache_time'])) {
            $new_input['cache_time'] = absint($input['cache_time']);
        }

        return $new_input;
    }

    public function access_token_callback()
    {
        $options = get_option($this->option_name);
        printf(
            '<input type="text" id="access_token" name="%s[access_token]" value="%s" class="regular-text" />',
            $this->option_name,
            isset($options['access_token']) ? esc_attr($options['access_token']) : ''
        );
    }

    public function post_limit_callback()
    {
        $options = get_option($this->option_name);
        printf(
            '<input type="number" id="post_limit" name="%s[post_limit]" value="%s" class="small-text" />',
            $this->option_name,
            isset($options['post_limit']) ? esc_attr($options['post_limit']) : '12'
        );
    }

    public function display_mode_callback()
    {
        $options = get_option($this->option_name);
        $value = isset($options['display_mode']) ? $options['display_mode'] : 'lightbox';
?>
        <select name="<?php echo $this->option_name; ?>[display_mode]">
            <option value="lightbox" <?php selected($value, 'lightbox'); ?>>Lightbox (Modal)</option>
            <option value="external" <?php selected($value, 'external'); ?>>Link External (Instagram)</option>
        </select>
        <?php
    }

    public function cache_time_callback()
    {
        $options = get_option($this->option_name);
        printf(
            '<input type="number" id="cache_time" name="%s[cache_time]" value="%s" class="small-text" />',
            $this->option_name,
            isset($options['cache_time']) ? esc_attr($options['cache_time']) : '60'
        );
    }

    public function enqueue_styles( $hook ) {
        if ( 'settings_page_wiwa-tour-ig-feed' !== $hook ) {
            return;
        }
        wp_enqueue_style( 'wiwa-admin-style', WIWA_IG_PLUGIN_URL . 'assets/css/admin.css', [], WIWA_IG_VERSION );
    }
}
