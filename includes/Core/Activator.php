<?php

namespace WiwaTour\IGFeed\Core;

class Activator
{
    public static function activate()
    {
        // Flush rewrite rules if we add custom post types or endpoints
        flush_rewrite_rules();

        // Set default options if they don't exist
        if (false === get_option('wiwa_tour_ig_options')) {
            $defaults = [
                'access_token' => '',
                'post_limit' => 12,
                'display_mode' => 'lightbox', // or 'external'
                'cache_time' => 60, // minutes
            ];
            add_option('wiwa_tour_ig_options', $defaults);
        }
    }
}
