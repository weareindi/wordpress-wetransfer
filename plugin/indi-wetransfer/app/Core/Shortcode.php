<?php

namespace WeTransfer\Core;

use WeTransfer\Core\Plugin as Plugin;
use WeTransfer\Core\Options as Options;

/**
 * Shortcode
 */
class Shortcode {
    /**
     * Register shortcode to display WeTransfer uploader
     */
    public static function register() {
        foreach (Plugin::getShortcodes() as $shortcode) {
            add_shortcode($shortcode, function() {
                Plugin::setInstanceType('shortcode');

                self::registerFooterScript();

                // Replace with HTML from template
                return file_get_contents(dirname(__DIR__) . '/Templates/Master.php');
            });
        }
    }

    public static function registerFooterScript() {
        add_action('wp_footer', function() { ?>
            <?php if (!empty(Options::getEventsSuccessScript())) { ?>
                <script type="application/javascript">
                    document.addEventListener('wetransfer-success', function(event) {
                        <?php echo Options::getEventsSuccessScript(); ?>
                    });
                </script>
            <?php } ?>
        <?php });
    }
}
