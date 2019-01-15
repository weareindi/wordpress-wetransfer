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
        }, 99);
    }

    /**
     * Regiser Plugin List Settings Link
     */
    public static function registerPluginLink() {
        add_action('plugin_action_links_' . Plugin::getBasename(), function($links) {
            $links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=ozpital-wpwetransfer') ) .'">Settings</a>';
            return $links;
        }, 99);
    }

    /**
     * Admin page HTML
     */
    public static function html() {
        ?>
        <div class="wrap">
            <h1><?php echo Plugin::getName(); ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields(Plugin::getSlug() . '-options'); ?>
                <?php do_settings_sections(Plugin::getSlug() . '-options'); ?>
                    <?php foreach (Option::getAll() as $option) { ?>
                        <?php if ($option['name'] === 'api-key') { ?>
                        <hr>
                        <?php $api_key_defined = !empty(getenv('WETRANSFER_API_KEY')) || defined('WETRANSFER_API_KEY') ? true : false; ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">WeTransfer API Key</th>
                                <td>
                                    <?php if ($api_key_defined) { ?>
                                        <p><strong>The `WETRANSFER_API_KEY` is currently being defined elsewhere in your code.</strong></p>
                                    <?php } else { ?>
                                        <input type="text" name="<?php echo $option['name']; ?>" value="<?php echo esc_attr(get_option($option['name'])); ?>" class="regular-text" />
                                        <p class="description">Alternatively; WETRANSFER_API_KEY can be defined in your config or set as an environment variable.</p>
                                        <p class="description">Need an API key? Visit: <a href="https://developers.wetransfer.com/" target="_blank">https://developers.wetransfer.com/</a></p>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                        <?php continue; ?>
                        <?php } ?>

                        <?php if ($option['name'] === 'transfer-complete-message') { ?>
                        <hr>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">Transfer Complete Message</th>
                                <td>
                                    <input type="text" name="<?php echo $option['name']; ?>" value="<?php echo (get_option($option['name']) !== null) ? get_option($option['name']) : 'You\'re done!'; ?>" class="regular-text" />
                                    <p class="description">This message appears on your site when the file has uploaded</p>
                                </td>
                            </tr>
                        </table>
                        <?php continue; ?>
                        <?php } ?>

                        <?php if ($option['name'] === 'transfer-complete-show-url') { ?>
                        <hr>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">Show Transfer URL</th>
                                <td>
                                    <input type="checkbox" name="<?php echo $option['name']; ?>" value="1" <?php echo (get_option($option['name']) == '1' || get_option($option['name']) === null) ? 'checked' : ''; ?>/>
                                </td>
                            </tr>
                        </table>
                        <?php continue; ?>
                        <?php } ?>

                        <?php if ($option['name'] === 'wetransfer-message') { ?>
                        <hr>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">WeTransfer Message</th>
                                <td>
                                    <input type="text" name="<?php echo $option['name']; ?>" value="<?php echo (get_option($option['name']) !== null) ? get_option($option['name']) : 'WordPress WeTransfer'; ?>" class="regular-text" />
                                    <p class="description">This message appears on WeTransfer alongside the uploaded file.</p>
                                </td>
                            </tr>
                        </table>
                        <?php continue; ?>
                        <?php } ?>

                        <?php if ($option['name'] === 'success-script') { ?>
                        <hr>
                        <table class="form-table">
                            <tr>
                                <th scope="row">On Success Event</th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text">ozpital-wpwetransfer-success event listener</legend>
                                        <p>Javascript in this textarea will fire on successful transfer</p>
                                        <p><textarea name="<?php echo $option['name']; ?>" rows="10" cols="50" class="large-text code"><?php echo !empty(esc_attr(get_option($option['name']))) ? esc_attr(get_option($option['name'])) : ''; ?></textarea></p>
                                        <p class="description">Alternatively; you can listen for the `ozpital-wpwetransfer-success` event in your own script. eg:<br>
    <pre><code>document.addEventListener('ozpital-wpwetransfer-success', function(event) {
        console.log(event);
    });</code></pre>
                                        </p>
                                        <hr>
                                        <h3>Contact Form 7</h3>
                                        <p>A common request for this plugin is to populate a Contact Form 7 field with the url generated by a successful transfer. Clicking <a href="javascript:populateSuccessEvent();">here</a> will append some code to the above success event that you can tweak to populate your desired form field.</p>
                                        <script>
                                            populateSuccessEvent = function() {
                                                var successEventEditor = document.querySelector('textarea[name="success-script"]');

                                                var successEventPopulateFormScript = "// Get the CF7 field that we want to populate with a WeTransfer URL" + '\n';
                                                successEventPopulateFormScript += "var urlInputField = document.getElementById('wetransfer_url');" + '\n\n';
                                                successEventPopulateFormScript += "// Check that the CF7 field exists on the current page" + '\n';
                                                successEventPopulateFormScript += "if (urlInputField) {" + '\n';
                                                successEventPopulateFormScript += "    // Populate the CF& field with the URL" + '\n';
                                                successEventPopulateFormScript += "    urlInputField.value = event.detail.url;" + '\n';
                                                successEventPopulateFormScript += "}" + '\n';

                                                if (successEventEditor.value.length > 0) {
                                                    successEventEditor.value = successEventEditor.value + '\n\n' + successEventPopulateFormScript;
                                                } else {
                                                    successEventEditor.value = successEventPopulateFormScript;
                                                }
                                            }
                                        </script>
                                    </fieldset>
                                </td>
                            </tr>
                        </table>
                        <?php continue; ?>
                        <?php } ?>

                    <?php } ?>
                <hr>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
