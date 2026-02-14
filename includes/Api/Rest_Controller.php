<?php

namespace WiwaTour\IGFeed\Api;

use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Response;

class Rest_Controller extends WP_REST_Controller
{

    private $api;

    public function __construct()
    {
        $this->namespace = 'wiwa-ig/v1';
        $this->rest_base = 'feed';
        $this->api = new Instagram_API();
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
                array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_items'),
                'permission_callback' => '__return_true', // Public endpoint
                'args' => array(
                    'limit' => array(
                        'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
                    ),
                ),
            ),
        ));
    }

    public function get_items($request)
    {
        $limit = $request->get_param('limit');

        $data = $this->api->get_feed($limit);

        if (is_wp_error($data)) {
            return $data;
        }

        return new WP_REST_Response($data, 200);
    }
}
