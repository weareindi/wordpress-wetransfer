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
    public static function register(String $name, String $label, String $type) {
        if (!is_array(self::$options)) {
            self::$options = [];
        }

        array_push(self::$options, [
            'name' => $name,
            'label' => $label,
            'type' => $type
        ]);

        self::registerSetting($name);
    }

    /**
     * Regiser and assign options name to plugin
     */
    public static function registerSetting($name) {
        add_action('admin_init', function() use ($name) {
            register_setting(Plugin::getSlug() . '-options', $name);
        });
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
        self::register('api-key', 'WeTransfer API Key', 'text');
    }
}
