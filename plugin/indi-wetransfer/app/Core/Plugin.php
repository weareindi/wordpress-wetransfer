<?php

namespace WeTransfer\Core;

/**
 * Plugin Details
 */
class Plugin {
    /**
     * Plugin Name
     */
    private static $name = 'Indi WeTransfer';

    /**
     * Plugin Slug
     */
    private static $slug = 'indi-wetransfer';

    /**
     * Plugin shortname
     */
    private static $shortname = 'indiwt';

    /**
     * Instance type
     */
    private static $instance = null;

    /**
     * Get plugin name
     */
    public static function getName() {
        return self::$name;
    }

    /**
     * Get plugin slug
     */
    public static function getSlug() {
        return self::$slug;
    }

    /**
     * Get plugin shortname
     */
    public static function getShortname() {
        return self::$shortname;
    }

    /**
     * Get plugin basename
     */
    public static function getBasename() {
        return 'indi-wetransfer/index.php';
    }

    /**
     * Get plugin shortcodes
     */
    public static function getShortcodes() {
        return [
            'wetransfer'
        ];
    }

    /**
     * Activation checks
     */
    public static function activation() {
        register_activation_hook(INDIWT_PLUGIN_PATH, function() {
            if (version_compare(PHP_VERSION, INDIWT_MIN_PHP, '<')) {
                deactivate_plugins(plugin_basename( __FILE__ ));
                wp_die(
                    '<p>'
                    . sprintf(__( self::getName() . ' can not be activated because it requires a PHP version greater than %1$s.<br>Your PHP version can be updated by your hosting company.', self::getSlug() ), INDIWT_MIN_PHP)
                    . '</p>
                    <a href="' . admin_url('plugins.php') . '">' . __( 'Go back', 'my_plugin' ) . '</a>'
                );
            }
        });
    }

    /**
     * Set instance type
     */
    public static function setInstanceType($type) {
        self::$instance = $type;
    }

    /**
     * Was this instance called via the shortcode or somewhere else?
     * @return [type] [description]
     */
    public static function getInstanceType() {
        return self::$instance;
    }
}
