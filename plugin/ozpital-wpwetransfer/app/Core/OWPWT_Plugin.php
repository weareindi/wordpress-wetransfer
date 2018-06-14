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
}
