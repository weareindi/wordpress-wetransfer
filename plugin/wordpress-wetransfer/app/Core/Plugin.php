<?php

namespace WeTransfer\Core;

/**
 *
 */
class Plugin {

    /**
     * [private description]
     * @var [type]
     */
    private static $name = 'WordPress WeTransfer';

    /**
     * [private description]
     * @var [type]
     */
    private static $slug = 'wordpress-wetransfer';

    /**
     * [private description]
     * @var [type]
     */
    private static $shortname = 'wordpresswetransfer';

    /**
     * [getName description]
     * @return [type] [description]
     */
    public static function getName() {
        return self::$name;
    }

    /**
     * [getSlug description]
     * @return [type] [description]
     */
    public static function getSlug() {
        return self::$slug;
    }

    /**
     * [getSlug description]
     * @return [type] [description]
     */
    public static function getShortname() {
        return self::$shortname;
    }
}
