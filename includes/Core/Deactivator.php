<?php

namespace WiwaTour\IGFeed\Core;

class Deactivator
{
    public static function deactivate()
    {
        // Remove transients/cache on deactivation
        delete_transient('wiwa_ig_feed_cache');
        flush_rewrite_rules();
    }
}
