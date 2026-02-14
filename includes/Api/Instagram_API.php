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
        } catch (\Exception $e) {
            // Log error if needed: error_log( $e->getMessage() );
            return new \WP_Error('api_error', $e->getMessage());
        }
    }

    private function fetch_from_api($limit)
    {
        $url = 'https://graph.instagram.com/me/media';
        $args = array(
            // Request children fields to better handle Carousels and deep thumbnails
            'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp,username,children{media_type,media_url,thumbnail_url}',
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

        // Process data
        $processed_data = array_map(function ($item) {
            $item['image_src'] = isset($item['media_url']) ? $item['media_url'] : '';

            // 1. VIDEO Handling
            if (isset($item['media_type']) && 'VIDEO' === $item['media_type']) {
                if (!empty($item['thumbnail_url'])) {
                    $item['image_src'] = $item['thumbnail_url'];
                }
            }

            // 2. CAROUSEL Handling
            // Sometimes Carousels don't have a top-level media_url or it might be buggy.
            if (isset($item['media_type']) && 'CAROUSEL_ALBUM' === $item['media_type']) {
                // If top-level matches video or is missing, try children
                if (isset($item['children']['data'][0])) {
                    $first_child = $item['children']['data'][0];
                    // If child is VIDEO, prioritize its thumbnail
                    if ('VIDEO' === $first_child['media_type'] && !empty($first_child['thumbnail_url'])) {
                        $item['image_src'] = $first_child['thumbnail_url'];
                    } elseif (!empty($first_child['media_url'])) {
                        // Otherwise use child media_url (image)
                        $item['image_src'] = $first_child['media_url'];
                    }
                }
            }

            // Filter out if no image source found (rare)
            if (empty($item['image_src'])) {
                return null;
            }

            return $item;
        }, $data['data']);

        // Remove nulls
        return array_values(array_filter($processed_data));
    }
}
