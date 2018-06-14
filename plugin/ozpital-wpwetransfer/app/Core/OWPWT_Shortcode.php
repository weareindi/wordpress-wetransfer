<?php

namespace Ozpital\WPWeTransfer\Core;

use Ozpital\WPWeTransfer\Core\OWPWT_Plugin as Plugin;

/**
 * Shortcode
 */
class OWPWT_Shortcode {
    /**
     * Register shortcode to display WeTransfer uploader
     */
    public static function register() {
        add_shortcode(Plugin::getSlug(), function() {
            // Replace with HTML from template
            return file_get_contents(dirname(__DIR__) . '/Templates/OWPWT_Master.php');
        });
    }
}
