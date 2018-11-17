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
        }, 99);
        add_action('wp_ajax_nopriv_owpwt--auth', function() {
            Curl::auth();
        }, 99);
    }

    /**
     * Register wordpress transfer routes
     */
    public static function transfer() {
        add_action('wp_ajax_owpwt--transfer', function() {
            Curl::transfer();
        }, 99);
        add_action('wp_ajax_nopriv_owpwt--transfer', function() {
            Curl::transfer();
        }, 99);
    }

    /**
     * Register wordpress items routes
     */
    public static function items() {
        add_action('wp_ajax_owpwt--items', function() {
            Curl::items();
        }, 99);
        add_action('wp_ajax_nopriv_owpwt--items', function() {
            Curl::items();
        }, 99);
    }

    /**
     * Register wordpress url routes
     */
    public static function url() {
        add_action('wp_ajax_owpwt--url', function() {
            Curl::url();
        }, 99);
        add_action('wp_ajax_nopriv_owpwt--url', function() {
            Curl::url();
        }, 99);
    }

    /**
     * Register wordpress complete file upload routes
     */
    public static function completeFileUpload() {
        add_action('wp_ajax_owpwt--complete-file-upload', function() {
            Curl::completeFileUpload();
        }, 99);
        add_action('wp_ajax_nopriv_owpwt--complete-file-upload', function() {
            Curl::completeFileUpload();
        }, 99);
    }

    /**
     * Register wordpress complete file upload routes
     */
    public static function finalizeTransfer() {
        add_action('wp_ajax_owpwt--finalize-transfer', function() {
            Curl::finalizeTransfer();
        }, 99);
        add_action('wp_ajax_nopriv_owpwt--finalize-transfer', function() {
            Curl::finalizeTransfer();
        }, 99);
    }
}
