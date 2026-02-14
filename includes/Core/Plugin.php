<?php

namespace WiwaTour\IGFeed\Core;

use WiwaTour\IGFeed\Admin\Settings;
use WiwaTour\IGFeed\Frontend\Shortcode;
use WiwaTour\IGFeed\Api\Rest_Controller;

class Plugin
{

    public function run()
    {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_api_hooks();
    }

    private function load_dependencies()
    {
    // Load other dependencies if needed
    }

    private function define_admin_hooks()
    {
        $plugin_settings = new Settings();
        add_action('admin_menu', [$plugin_settings, 'add_plugin_page']);
        add_action('admin_init', [$plugin_settings, 'register_settings']);
        add_action('admin_enqueue_scripts', [$plugin_settings, 'enqueue_styles']);
    }

    private function define_public_hooks()
    {
        $plugin_shortcode = new Shortcode();
        add_shortcode('wiwa_ig_feed', [$plugin_shortcode, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$plugin_shortcode, 'enqueue_assets']);
    }

    private function define_api_hooks()
    {
        $api_controller = new Rest_Controller();
        add_action('rest_api_init', [$api_controller, 'register_routes']);
    }
}
