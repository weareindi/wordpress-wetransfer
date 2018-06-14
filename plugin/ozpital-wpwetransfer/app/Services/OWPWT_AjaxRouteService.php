<?php

namespace Ozpital\WPWeTransfer\Services;

use Ozpital\WPWeTransfer\Services\OWPWT_CurlService as Curl;

/**
 * AJAX Route Service
 */
class OWPWT_AjaxRouteService {
    /**
     * Register wordpress authentication routes
     */
    public static function auth() {
        add_action('wp_ajax_owpwt--auth', function() {
            Curl::auth();
        });
        add_action('wp_ajax_nopriv_owpwt--auth', function() {
            Curl::auth();
        });
    }

    /**
     * Register wordpress transfer routes
     */
    public static function transfer() {
        add_action('wp_ajax_owpwt--transfer', function() {
            Curl::transfer();
        });
        add_action('wp_ajax_nopriv_owpwt--transfer', function() {
            Curl::transfer();
        });
    }

    /**
     * Register wordpress items routes
     */
    public static function items() {
        add_action('wp_ajax_owpwt--items', function() {
            Curl::items();
        });
        add_action('wp_ajax_nopriv_owpwt--items', function() {
            Curl::items();
        });
    }

    /**
     * Register wordpress url routes
     */
    public static function url() {
        add_action('wp_ajax_owpwt--url', function() {
            Curl::url();
        });
        add_action('wp_ajax_nopriv_owpwt--url', function() {
            Curl::url();
        });
    }

    /**
     * Register wordpress complete trasnfer routes
     */
    public static function completeTransfer() {
        add_action('wp_ajax_owpwt--complete-transfer', function() {
            Curl::completeTransfer();
        });
        add_action('wp_ajax_nopriv_owpwt--complete-transfer', function() {
            Curl::completeTransfer();
        });
    }
}
