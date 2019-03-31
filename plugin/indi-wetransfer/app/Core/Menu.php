<?php

namespace WeTransfer\Core;

use WeTransfer\Core\Plugin as Plugin;
use WeTransfer\Core\Options as Options;
use WeTransfer\Core\CF7 as CF7;

/**
 * Menu
 */
class Menu {
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
     * Regiser Plugin List Settings Link
     */
    public static function registerPluginLink() {
        add_action('plugin_action_links_' . Plugin::getBasename(), function($links) {
            $links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=indi-wetransfer') ) .'">Settings</a>';
            return $links;
        });
    }

    /**
     * Admin page HTML
     */
    public static function html() {
        ?>
        <div class="wrap">
            <h1><?php echo Plugin::getName(); ?></h1>

            <?php
                $tabs = [
                    [
                        'id' => 'messages',
                        'label' => 'Messages',
                        'active' => (!isset($_GET['tab']) || empty($_GET['tab'])) || (isset($_GET['tab']) && !empty($_GET['tab']) && $_GET['tab'] === 'messages') ? true : false
                    ],
                    [
                        'id' => 'events',
                        'label' => 'Events',
                        'active' => isset($_GET['tab']) && !empty($_GET['tab']) && $_GET['tab'] === 'events' ? true : false
                    ],
                    [
                        'id' => 'api',
                        'label' => 'API',
                        'active' => isset($_GET['tab']) && !empty($_GET['tab']) && $_GET['tab'] === 'api' ? true : false
                    ]
                ];

                // Is CF7 Active? Show the CF7 Tab
                if (CF7::isActive()) {
                    $tabs[] = [
                        'id' => 'cf7',
                        'label' => 'Contact Form 7',
                        'active' => isset($_GET['tab']) && !empty($_GET['tab']) && $_GET['tab'] === 'cf7' ? true : false
                    ];
                }
            ?>

            <h2 class="nav-tab-wrapper">
                <?php foreach ($tabs as $tab) { ?>
                    <a class="nav-tab <?php echo $tab['active'] ? 'nav-tab-active' : ''; ?>" href="?page=<?php echo Plugin::getSlug(); ?>&tab=<?php echo $tab['id']; ?>"><?php echo $tab['label']; ?></a>
                <?php } ?>
            </h2>

            <?php
                foreach ($tabs as $tab) {
                    if (!$tab['active']) {
                        continue;
                    }

                    echo self::getTabContent($tab['id']);
                    break;
                }
            ?>
        </div>
        <?php
    }

    public static function getTabContent($tabID) {
        if ($tabID === 'events') {
            self::getTabContentEvents();
        }

        if ($tabID === 'api') {
            self::getTabContentApi();
        }

        if ($tabID === 'messages') {
            self::getTabContentMessages();
        }

        if ($tabID === 'cf7') {
            self::getTabContentCf7();
        }
    }

    public static function getTabContentMessages() {
        ?>
            <form method="post" action="options.php">
                <?php settings_fields(Plugin::getSlug() . '-messages'); ?>
                <?php do_settings_sections(Plugin::getSlug() . '-messages'); ?>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Transfer Complete Message</th>
                        <td>
                            <input type="text" name="messages--transfer-complete" value="<?php echo esc_attr(Options::getMessagesTransferComplete()); ?>" class="regular-text" />
                            <p class="description">This message appears on your site when the file has uploaded</p>
                        </td>
                    </tr>
                </table>

                <hr>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Show Transfer URL</th>
                        <td>
                            <input type="checkbox" name="messages--show-url" value="true" <?php echo Options::getMessagesShowUrl() ? 'checked' : ''; ?>/>
                        </td>
                    </tr>
                </table>

                <hr>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">WeTransfer Message</th>
                        <td>
                            <input type="text" name="messages--wetransfer" value="<?php echo esc_attr(Options::getMessagesWeTransferMessage()); ?>" class="regular-text" />
                            <p class="description">This message appears on WeTransfer alongside the uploaded file.</p>
                        </td>
                    </tr>
                </table>

                <hr>
                <?php submit_button(); ?>
            </form>
        <?php
    }

    public static function getTabContentEvents() {
        ?>
            <form method="post" action="options.php">
                <?php settings_fields(Plugin::getSlug() . '-events'); ?>
                <?php do_settings_sections(Plugin::getSlug() . '-events'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">On Success Event</th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">indi-wetransfer-success event listener</legend>
                                <p>Javascript in this textarea will fire on successful transfer</p>
                                <p><textarea name="events--success-script" rows="10" cols="50" class="large-text code"><?php echo esc_attr(Options::getEventsSuccessScript()); ?></textarea></p>
                                <p class="description">Alternatively; you can listen for the `indi-wetransfer-success` event in your own script. eg:<br>
<pre><code>document.addEventListener('indi-wetransfer-success', function(event) {
console.log(event);
});</code></pre>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                </table>

                <hr>
                <?php submit_button(); ?>
            </form>
        <?php
    }

    public static function getTabContentApi() {
        ?>
            <form method="post" action="options.php">
                <?php settings_fields(Plugin::getSlug() . '-wetransfer'); ?>
                <?php do_settings_sections(Plugin::getSlug() . '-wetransfer'); ?>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">WeTransfer API Key</th>
                        <td>
                            <?php if (!empty(getenv('WETRANSFER_API_KEY')) || defined('WETRANSFER_API_KEY')) { ?>
                                <p><strong>The `WETRANSFER_API_KEY` is currently being defined elsewhere in your code.</strong></p>
                            <?php } else { ?>
                                <input type="text" name="wetransfer--api-key" value="<?php echo esc_attr(Options::getWeTransferApiKey()); ?>" class="regular-text" />
                                <p class="description">Alternatively; WETRANSFER_API_KEY can be defined in your config or set as an environment variable.</p>
                                <p class="description">Need an API key? Visit: <a href="https://developers.wetransfer.com/" target="_blank">https://developers.wetransfer.com/</a></p>
                            <?php } ?>
                        </td>
                    </tr>
                </table>

                <hr>
                <?php submit_button(); ?>
            </form>
        <?php
    }

    public static function getTabContentCf7() {
        ?>
            <form method="post" action="options.php">
                <?php settings_fields(Plugin::getSlug() . '-cf7'); ?>
                <?php do_settings_sections(Plugin::getSlug() . '-cf7'); ?>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Transfer Complete Message</th>
                        <td>
                            <input type="text" name="cf7--transfer-complete" value="<?php echo esc_attr(Options::getCf7TransferComplete()); ?>" class="regular-text" />
                            <p class="description">This message appears on your site when the file has uploaded</p>
                        </td>
                    </tr>
                </table>

                <hr>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Show Transfer URL</th>
                        <td>
                            <input type="checkbox" name="cf7--show-url" value="true" <?php echo esc_attr(Options::getCf7ShowUrl()) ? 'checked' : ''; ?>/>
                        </td>
                    </tr>
                </table>

                <hr>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">WeTransfer Message</th>
                        <td>
                            <input type="text" name="cf7--wetransfer" value="<?php echo esc_attr(Options::getCf7WeTransferMessage()); ?>" class="regular-text" />
                            <p class="description">This message appears on WeTransfer alongside the uploaded file.</p>
                        </td>
                    </tr>
                </table>

                <hr>
                <?php submit_button(); ?>
            </form>
        <?php
    }
}
