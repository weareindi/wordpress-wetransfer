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
     * Register wordpress complete file upload routes
     */
    public static function completeFileUpload() {
        add_action('wp_ajax_owpwt--complete-file-upload', function() {
            Curl::completeFileUpload();
        });
        add_action('wp_ajax_nopriv_owpwt--complete-file-upload', function() {
            Curl::completeFileUpload();
        });
    }

    /**
     * Register wordpress complete file upload routes
     */
    public static function finalizeTransfer() {
        add_action('wp_ajax_owpwt--finalize-transfer', function() {
            Curl::finalizeTransfer();
        });
        add_action('wp_ajax_nopriv_owpwt--finalize-transfer', function() {
            Curl::finalizeTransfer();
        });
    }
}
