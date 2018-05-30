<?php

namespace WeTransfer\Services;

use WeTransfer\Services\CurlService as Curl;

class AjaxRouteService {
    public static function auth() {
        add_action('wp_ajax_wordpresswetransfer--auth', function() {
            Curl::auth();
        });
        add_action('wp_ajax_nopriv_wordpresswetransfer--auth', function() {
            Curl::auth();
        });
    }

    public static function transfer() {
        add_action('wp_ajax_wordpresswetransfer--transfer', function() {
            Curl::transfer();
        });
        add_action('wp_ajax_nopriv_wordpresswetransfer--transfer', function() {
            Curl::transfer();
        });
    }

    public static function items() {
        add_action('wp_ajax_wordpresswetransfer--items', function() {
            Curl::items();
        });
        add_action('wp_ajax_nopriv_wordpresswetransfer--items', function() {
            Curl::items();
        });
    }

    public static function url() {
        add_action('wp_ajax_wordpresswetransfer--url', function() {
            Curl::url();
        });
        add_action('wp_ajax_nopriv_wordpresswetransfer--url', function() {
            Curl::url();
        });
    }

    public static function completeTransfer() {
        add_action('wp_ajax_wordpresswetransfer--complete-transfer', function() {
            Curl::completeTransfer();
        });
        add_action('wp_ajax_nopriv_wordpresswetransfer--complete-transfer', function() {
            Curl::completeTransfer();
        });
    }
}
