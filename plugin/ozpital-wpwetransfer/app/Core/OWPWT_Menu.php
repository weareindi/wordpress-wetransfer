<?php

namespace Ozpital\WPWeTransfer\Core;

use Ozpital\WPWeTransfer\Core\OWPWT_Plugin as Plugin;
use Ozpital\WPWeTransfer\Core\OWPWT_Option as Option;

/**
 * Menu
 */
class OWPWT_Menu {
    /**
     * Regiser Admin Menu
     */
    public static function register() {
        add_action('admin_menu', function() {
            add_options_page(
                Plugin::getName(),
                Plugin::getName(),
                'manage_options',
                Plugin::getSlug(),
                function() {
                    return self::html();
                }
            );
        });
    }

    /**
     * Admin page HTML
     */
    public static function html() {
        ?>
        <div class="wrap">
            <h1><?php echo Plugin::getName(); ?></h1>

            <hr>

            <form method="post" action="options.php">
                <?php settings_fields(Plugin::getSlug() . '-options'); ?>
                <?php do_settings_sections(Plugin::getSlug() . '-options'); ?>

                <table class="form-table">
                    <?php foreach (Option::getAll() as $option) { ?>
                        <tr valign="top">
                            <th scope="row"><?php echo $option['label']; ?></th>
                            <td><input type="<?php echo $option['type']; ?>" name="<?php echo $option['name']; ?>" value="<?php echo esc_attr(get_option($option['name'])); ?>" /></td>
                        </tr>
                    <?php } ?>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
