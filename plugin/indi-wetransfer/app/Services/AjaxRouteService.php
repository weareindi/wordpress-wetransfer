<?php

namespace WeTransfer\Services;

use WeTransfer\Services\CurlService as Curl;

/**
 * AJAX Route Service
 */
class AjaxRouteService {
    /**
     * Register wordpress authentication routes
     */
    public static function auth() {
        $actions = [
            'wp_ajax_indiwt--auth',
            'wp_ajax_nopriv_indiwt--auth'
        ];

        foreach ($actions as $action) {
            add_action($action, function() {
                Curl::auth();
            });
        }
    }

    /**
     * Register wordpress transfer routes
     */
    public static function transfer() {
        $actions = [
            'wp_ajax_indiwt--transfer',
            'wp_ajax_nopriv_indiwt--transfer'
        ];

        foreach ($actions as $action) {
            add_action($action, function() {
                Curl::transfer();
            });
        }
    }

    /**
     * Register wordpress items routes
     */
    public static function items() {
        $actions = [
            'wp_ajax_indiwt--items',
            'wp_ajax_nopriv_indiwt--items'
        ];

        foreach ($actions as $action) {
            add_action($action, function() {
                Curl::items();
            });
        }
    }

    /**
     * Register wordpress url routes
     */
    public static function url() {
        $actions = [
            'wp_ajax_indiwt--url',
            'wp_ajax_nopriv_indiwt--url'
        ];

        foreach ($actions as $action) {
            add_action($action, function() {
                Curl::url();
            });
        }
    }

    /**
     * Register wordpress complete file upload routes
     */
    public static function completeFileUpload() {
        $actions = [
            'wp_ajax_indiwt--complete-file-upload',
            'wp_ajax_nopriv_indiwt--complete-file-upload'
        ];

        foreach ($actions as $action) {
            add_action($action, function() {
                Curl::completeFileUpload();
            });
        }
    }

    /**
     * Register wordpress complete file upload routes
     */
    public static function finalizeTransfer() {
        $actions = [
            'wp_ajax_indiwt--finalize-transfer',
            'wp_ajax_nopriv_indiwt--finalize-transfer'
        ];

        foreach ($actions as $action) {
            add_action($action, function() {
                Curl::finalizeTransfer();
            });
        }
    }
}
