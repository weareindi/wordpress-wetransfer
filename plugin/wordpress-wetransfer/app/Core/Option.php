<?php

namespace WeTransfer\Core;

use WeTransfer\Core\Plugin;

/**
 *
 */
class Option {

    /**
     * [public description]
     * @var [type]
     */
    public static $options;

    /**
     * [add description]
     * @param String $name  [description]
     * @param String $label [description]
     * @param String $type  [description]
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
     * [registerSetting description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function registerSetting($name) {
        add_action('admin_init', function() use ($name) {
            register_setting(Plugin::getSlug() . '-options', $name);
        });
    }

    /**
     * [getAll description]
     * @return [type] [description]
     */
    public static function getAll() {
        return self::$options;
    }

    /**
     * [registerApiKey description]
     * @return [type] [description]
     */
    public static function registerApiKey() {
        self::register('api-key', 'WeTransfer API Key', 'text');
    }
}
