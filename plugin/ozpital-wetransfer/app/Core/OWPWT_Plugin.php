<?php

namespace Ozpital\WPWeTransfer\Core;

/**
 * Plugin Details
 */
class OWPWT_Plugin {
    /**
     * Plugin Name
     */
    private static $name = 'Ozpital WPWeTransfer';

    /**
     * Plugin Slug
     */
    private static $slug = 'ozpital-wpwetransfer';

    /**
     * Plugin shortname
     */
    private static $shortname = 'owpwt';

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
        return 'ozpital-wetransfer/index.php';
    }

    /**
     * Activation checks
     */
    public static function activation() {
        register_activation_hook(OWPWT_PLUGIN_PATH, function() {
            if (version_compare(PHP_VERSION, OWPWT_MIN_PHP, '<')) {
                deactivate_plugins(plugin_basename( __FILE__ ));
                wp_die(
                    '<p>'
                    . sprintf(__( self::getName() . ' can not be activated because it requires a PHP version greater than %1$s.<br>Your PHP version can be updated by your hosting company.', self::getSlug() ), OWPWT_MIN_PHP)
                    . '</p>
                    <a href="' . admin_url('plugins.php') . '">' . __( 'Go back', 'my_plugin' ) . '</a>'
                );
            }
        });
    }
}
