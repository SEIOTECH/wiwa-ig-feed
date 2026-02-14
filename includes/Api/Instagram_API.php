<?php

namespace WiwaTour\IGFeed\Api;

class Instagram_API
{

    private $access_token;
    private $limit;
    private $cache_time;

    public function __construct()
    {
        $options = get_option('wiwa_tour_ig_options');
        $this->access_token = isset($options['access_token']) ? $options['access_token'] : '';
        $this->limit = isset($options['post_limit']) ? $options['post_limit'] : 12;
        $this->cache_time = isset($options['cache_time']) ? $options['cache_time'] : 60;
    }

    public function get_feed($limit_override = null)
    {
        if (empty($this->access_token)) {
            return new \WP_Error('no_token', 'Instagram Access Token is missing.');
        }

        $limit = $limit_override ? intval($limit_override) : $this->limit;
        $transient_key = 'wiwa_ig_feed_cache_' . $limit;

        $cached_data = get_transient($transient_key);

        if (false !== $cached_data) {
            return $cached_data;
        }

        try {
            $data = $this->fetch_from_api($limit);

            if (!is_wp_error($data) && !empty($data)) {
                set_transient($transient_key, $data, $this->cache_time * MINUTE_IN_SECONDS);
            }

            return $data;
        }
        catch (\Exception $e) {
            // Log error if needed: error_log( $e->getMessage() );
            // Return cached data if available (stale cache), otherwise empty array
            // Since we already checked valid cache above, this is a hard failure scenario.
            return new \WP_Error('api_error', $e->getMessage());
        }
    }

    private function fetch_from_api($limit)
    {
        $url = 'https://graph.instagram.com/me/media';
        $args = array(
            'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp,username',
            'access_token' => $this->access_token,
            'limit' => $limit,
        );

        $request_url = add_query_arg($args, $url);

        $response = wp_remote_get($request_url, array('timeout' => 15));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            return new \WP_Error('instagram_api_error', $data['error']['message']);
        }

        if (!isset($data['data'])) {
            return [];
        }

        // Process data to ensure video thumbnails are always available in a standard field
        $processed_data = array_map(function ($item) {
            $item['image_src'] = $item['media_url']; // Default for IMAGE and CAROUSEL_ALBUM

            // CRITICAL: Fix for video thumbnails
            if (isset($item['media_type']) && 'VIDEO' === $item['media_type']) {
                if (isset($item['thumbnail_url'])) {
                    $item['image_src'] = $item['thumbnail_url'];
                }
                else {
                // Fallback if Instagram doesn't return thumbnail (rare but possible)
                // We might leave it as media_url but it won't render in an <img> tag.
                // Ideally we could use a default placeholder or try to use media_url if it's a cover frame.
                // For now, adhere to requesting thumbnail_url.
                }
            }

            return $item;
        }, $data['data']);

        return $processed_data;
    }
}
