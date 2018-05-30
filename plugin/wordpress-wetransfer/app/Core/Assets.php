<?php

namespace WeTransfer\Core;

use WeTransfer\Core\Plugin;

/**
 *
 */
class Assets {

    /**
     * [registerStyles description]
     * @return [type] [description]
     */
    public static function styles() {
        add_action('wp_enqueue_scripts', function() {
            // Prepare path
            $pathCss = 'assets/css/core.css';

            // Enqueue style
            wp_enqueue_style(Plugin::getSlug() . '-css', WORDPRESS_WETRANSFER_URL . $pathCss, false, filemtime(WORDPRESS_WETRANSFER_DIR . $pathCss));
        });
    }

    /**
     * [registerScripts description]
     * @return [type] [description]
     */
    public static function scripts() {
        add_action('wp_enqueue_scripts', function() {
            // Prepare path
            $pathJs = 'assets/js/script.js';

            // Enqueue style
            wp_enqueue_script(Plugin::getSlug() . '-js', WORDPRESS_WETRANSFER_URL . $pathJs, null, filemtime(WORDPRESS_WETRANSFER_DIR . $pathJs), true);
            wp_localize_script(Plugin::getSlug() . '-js', Plugin::getShortname(), [
                'pluginDir' => WORDPRESS_WETRANSFER_URL,
                'ajaxUrl' => admin_url('admin-ajax.php')
            ]);
        });
    }
}
