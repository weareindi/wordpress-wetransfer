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
            self::registerFooterScript();

            // Replace with HTML from template
            return file_get_contents(dirname(__DIR__) . '/Templates/OWPWT_Master.php');
        }, 99);
    }

    public static function registerFooterScript() {
        add_action('wp_footer', function() { ?>
            <?php if (!empty(get_option('success-script'))) { ?>
                <script type="application/javascript">
                    document.addEventListener('ozpital-wpwetransfer-success', function(event) {
                        <?php echo get_option('success-script'); ?>
                    });
                </script>
            <?php } ?>
        <?php }, 99);
    }
}
