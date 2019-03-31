<?php

/*
Plugin Name: Indi WeTransfer
Description: Upload to WeTransfer without leaving wordpress
Version: 1.0.0
Author: Laurence Archer
Author URI: https://weareindi.co.uk
*/

require_once('autoload.php');

use WeTransfer\Core\Assets as Assets;
use WeTransfer\Core\CF7 as CF7;
use WeTransfer\Core\Menu as Menu;
use WeTransfer\Core\Options as Options;
use WeTransfer\Core\Plugin as Plugin;
use WeTransfer\Core\Shortcode as Shortcode;
use WeTransfer\Services\AjaxRouteService as Route;

// Define plugin root
define('INDIWT_DIR', __DIR__ . '/');
define('INDIWT_PLUGIN_PATH', INDIWT_DIR . 'index.php');
define('INDIWT_URL', plugin_dir_url(__FILE__));
define('INDIWT_MIN_PHP', '7.0');

// Includes
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if (is_admin()) {
    // Register activation checks
    Plugin::activation();
}

// Test if plugin active or in mu-plugins
$pluginActive = (is_plugin_active(Plugin::getBasename()) || file_exists(WPMU_PLUGIN_DIR . '/' . Plugin::getBasename())) ? true : false;

if ($pluginActive && is_admin()) {
    // Register Options
    Options::register();

    // Register menu
    Menu::register();
    Menu::registerPluginLink();
}

if ($pluginActive) {
    // Register shortcode
    Shortcode::register();

    // Register Styles
    Assets::styles();

    // Register Scripts
    Assets::scripts();
    Assets::defineLocalizedVariables();

    // Register Ajax Functions
    Route::auth();
    Route::transfer();
    Route::items();
    Route::url();
    Route::completeFileUpload();
    Route::finalizeTransfer();
}

// Is Contact Form 7 active
if (CF7::isActive()) {
    // Load CF7 stuff
    CF7::register();
    CF7::defineLocalizedVariables();
}
