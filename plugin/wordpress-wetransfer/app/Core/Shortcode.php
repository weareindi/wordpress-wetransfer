<?php

namespace WeTransfer\Core;

class Shortcode {
    public static function register() {
        add_shortcode('wordpress-wetransfer', function() {
            return file_get_contents(dirname(__DIR__) . '/Templates/Master.php');
        });
    }
}
