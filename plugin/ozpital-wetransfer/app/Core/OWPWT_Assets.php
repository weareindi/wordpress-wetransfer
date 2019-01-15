<?php

namespace Ozpital\WPWeTransfer\Core;

use Ozpital\WPWeTransfer\Core\OWPWT_Plugin as Plugin;
use Ozpital\WPWeTransfer\Core\OWPWT_Option as Option;

/**
 * Assets
 */
class OWPWT_Assets {
    /**
     * Enqueue plugin styles
     */
    public static function styles() {
        add_action('wp_enqueue_scripts', function() {
            // Prepare path
            $pathCss = 'assets/css/core.css';

            // Enqueue style
            wp_enqueue_style(Plugin::getSlug() . '-css', OWPWT_URL . $pathCss, false, filemtime(OWPWT_DIR . $pathCss));
        }, 99);
    }

    /**
     * Enqueue and localise scripts
     */
    public static function scripts() {
        add_action('wp_enqueue_scripts', function() {
            // Prepare path
            $pathJs = 'assets/js/script.js';

            // Enqueue style
            wp_enqueue_script(Plugin::getSlug() . '-js', OWPWT_URL . $pathJs, null, filemtime(OWPWT_DIR . $pathJs), true);
            wp_localize_script(Plugin::getSlug() . '-js', Plugin::getShortname(), [
                'pluginDir' => OWPWT_URL,
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'transferCompleteMessage' => Option::getTransferCompleteMessage(),
                'transferCompleteShowUrl' => Option::getTransferCompleteShowUrl()
            ]);
        }, 99);
    }
}
