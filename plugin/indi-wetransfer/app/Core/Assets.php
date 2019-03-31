<?php

namespace WeTransfer\Core;

use WeTransfer\Core\Plugin as Plugin;
use WeTransfer\Core\Options as Options;

/**
 * Assets
 */
class Assets {
    /**
     * Enqueue plugin styles
     */
    public static function styles() {
        add_action('wp_enqueue_scripts', function() {
            // Prepare path
            $pathCss = 'assets/css/core.css';

            // Enqueue style
            wp_enqueue_style(Plugin::getSlug() . '-css', INDIWT_URL . $pathCss, false, filemtime(INDIWT_DIR . $pathCss));
        });
    }

    /**
     * Enqueue and localise scripts
     */
    public static function scripts() {
        add_action('wp_enqueue_scripts', function() {
            // Prepare path
            $pathJs = 'assets/js/script.js';

            // Enqueue style
            wp_enqueue_script(Plugin::getSlug() . '-js', INDIWT_URL . $pathJs, null, filemtime(INDIWT_DIR . $pathJs), true);
        });
    }

    public static function defineLocalizedVariables() {
        add_action('wp_enqueue_scripts', function() {
            // Define available javascript variables
            $variables = [
                'pluginDir' => INDIWT_URL,
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'transferCompleteMessage' => Options::getMessagesTransferComplete(),
                'transferCompleteShowUrl' => Options::getMessagesShowUrl()
            ];
            wp_localize_script(Plugin::getSlug() . '-js', Plugin::getShortname(), $variables);
        });
    }
}
