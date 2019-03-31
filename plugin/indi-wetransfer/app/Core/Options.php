<?php

namespace WeTransfer\Core;

use WeTransfer\Core\Plugin as Plugin;
use WeTransfer\Core\CF7 as CF7;

/**
 * Option
 */
class Options {
    /**
     * Prepare $options variable to be populated
     */
    public static $options;

    /**
     * Register all options
     */
    public static function register() {
        add_action('admin_init', function() {
            register_setting(Plugin::getSlug() . '-messages', 'messages--transfer-complete', ['type' => 'string']);
            register_setting(Plugin::getSlug() . '-messages', 'messages--show-url', ['type' => 'boolean']);
            register_setting(Plugin::getSlug() . '-messages', 'messages--wetransfer', ['type' => 'string']);
            register_setting(Plugin::getSlug() . '-events', 'events--success-script', ['type' => 'string']);
            register_setting(Plugin::getSlug() . '-wetransfer', 'wetransfer--api-key', ['type' => 'string']);

            if (CF7::isActive()) {
                register_setting(Plugin::getSlug() . '-cf7', 'cf7--transfer-complete', ['type' => 'string']);
                register_setting(Plugin::getSlug() . '-cf7', 'cf7--show-url', ['type' => 'boolean']);
                register_setting(Plugin::getSlug() . '-cf7', 'cf7--wetransfer', ['type' => 'string']);
            }
        });
    }

    public static function getMessagesTransferComplete() {
        if (empty(get_option('messages--transfer-complete'))) {
            return 'Transfer Successful';
        }

        return get_option('messages--transfer-complete');
    }

    public static function getMessagesShowUrl() {
        if (empty(get_option('messages--show-url')) || !get_option('messages--show-url')) {
            return false;
        }

        return get_option('messages--show-url');
    }

    public static function getMessagesWeTransferMessage() {
        if (empty(get_option('messages--wetransfer'))) {
            return 'Transferred via the Indi WeTransfer plugin for WordPress';
        }

        return get_option('messages--wetransfer');
    }

    public static function getEventsSuccessScript() {
        if (empty(get_option('events--success-script'))) {
            return '';
        }

        return get_option('events--success-script');
    }

    public static function getWeTransferApiKey() {
        if (defined('WETRANSFER_API_KEY')) {
            return WETRANSFER_API_KEY;
        }

        if (!empty(getenv('WETRANSFER_API_KEY'))) {
            return getenv('WETRANSFER_API_KEY');
        }

        if (!empty(get_option('wetransfer--api-key'))) {
            return get_option('wetransfer--api-key');
        }

        return false;
    }

    public static function getCf7TransferComplete() {
        if (empty(get_option('cf7--transfer-complete'))) {
            return 'Transfer Successful';
        }

        return get_option('cf7--transfer-complete');
    }

    public static function getCf7ShowUrl() {
        if (empty(get_option('cf7--show-url')) || !get_option('cf7--show-url')) {
            return false;
        }

        return get_option('cf7--show-url');
    }

    public static function getCf7WeTransferMessage() {
        if (empty(get_option('cf7--wetransfer'))) {
            return 'Transferred via the Indi WeTransfer plugin for WordPress';
        }

        return get_option('cf7--wetransfer');
    }
}
