<?php

namespace Ozpital\WPWeTransfer\Core;

use Ozpital\WPWeTransfer\Core\OWPWT_Plugin as Plugin;

/**
 * Option
 */
class OWPWT_Option {
    /**
     * Prepare $options variable to be populated
     */
    public static $options;

    /**
     * Register an option
     * @param  String $name  Name of option
     * @param  String $label Label of option to display
     * @param  String $type  Type of input field to use
     */
    public static function register(String $name) {
        if (!is_array(self::$options)) {
            self::$options = [];
        }

        array_push(self::$options, [
            'name' => $name
        ]);

        self::registerSetting($name);
    }

    /**
     * Regiser and assign options name to plugin
     */
    public static function registerSetting($name) {
        add_action('admin_init', function() use ($name) {
            register_setting(Plugin::getSlug() . '-options', $name);
        }, 99);
    }

    /**
     * Get all prepared options
     */
    public static function getAll() {
        return self::$options;
    }

    /**
     * Register API Key Option
     */
    public static function registerApiKey() {
        self::register('api-key');
    }

    /**
     * Register API Key Option
     */
    public static function getApiKey() {
        $api_key = false;
        if (get_option('api-key')) {
            $api_key = get_option('api-key');
        }
        if (!empty(getenv('WETRANSFER_API_KEY'))) {
            $api_key = getenv('WETRANSFER_API_KEY');
        }
        if (defined('WETRANSFER_API_KEY')) {
            $api_key = WETRANSFER_API_KEY;
        }

        return $api_key;
    }

    /**
     * Register API Key Option
     */
    public static function registerSuccessScript() {
        self::register('success-script');
    }

    /**
     * [registerTransferCompleteMessage description]
     * @return [type] [description]
     */
    public static function registerTransferCompleteMessage() {
        self::register('transfer-complete-message');
    }

    /**
     * [registerTransferCompleteMessage description]
     * @return [type] [description]
     */
    public static function getTransferCompleteMessage() {
        $message = '';

        if (get_option('transfer-complete-message')) {
            $message = get_option('transfer-complete-message');
        }

        return $message;
    }

    /**
     * [registerTransferCompleteShowUrl description]
     * @return [type] [description]
     */
    public static function registerTransferCompleteShowUrl() {
        self::register('transfer-complete-show-url');
    }

    /**
     * [registerTransferCompleteShowUrl description]
     * @return [type] [description]
     */
    public static function getTransferCompleteShowUrl() {
        $show = 'true';

        if (get_option('transfer-complete-show-url') || get_option('transfer-complete-show-url') !== null) {
            $show = get_option('transfer-complete-show-url') === "1" ? 'true' : 'false';
        }

        return $show;
    }

    /**
     * Register WeTransfer Message Option
     */
    public static function registerWeTransferMessage() {
        self::register('wetransfer-message');
    }

    /**
     * Get WeTransfer Message Option
     */
    public static function getWeTransferMessage() {
        $message = 'WordPress WeTransfer';

        if (get_option('wetransfer-message')) {
            $message = get_option('wetransfer-message');
        }

        return $message;
    }
}
