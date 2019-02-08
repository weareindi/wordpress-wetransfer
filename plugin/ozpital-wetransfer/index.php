<?php

/*
Plugin Name: Ozpital WPWeTransfer
Description: Upload to WeTransfer without leaving wordpress
Version: 0.2.1
Author: Laurence Archer
Author URI: https://ozpital.com
*/

require_once('autoload.php');

use Ozpital\WPWeTransfer\Core\OWPWT_Assets as Assets;
use Ozpital\WPWeTransfer\Core\OWPWT_Menu as Menu;
use Ozpital\WPWeTransfer\Core\OWPWT_Option as Option;
use Ozpital\WPWeTransfer\Core\OWPWT_Plugin as Plugin;
use Ozpital\WPWeTransfer\Core\OWPWT_Shortcode as Shortcode;
use Ozpital\WPWeTransfer\Services\OWPWT_AjaxRouteService as Route;

// Define plugin root
define('OWPWT_DIR', __DIR__ . DIRECTORY_SEPARATOR);
define('OWPWT_PLUGIN_PATH', OWPWT_DIR . 'index.php');
define('OWPWT_URL', plugin_dir_url(__FILE__));
define('OWPWT_MIN_PHP', '7.0');

// Includes
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if (is_admin()) {
    // Register activation checks
    Plugin::activation();
}

// Test if plugin active or in mu-plugins
$pluginActive = (is_plugin_active(Plugin::getBasename()) || file_exists(WPMU_PLUGIN_DIR . DIRECTORY_SEPARATOR . Plugin::getBasename())) ? true : false;

if ($pluginActive && is_admin()) {
    // Register Options
    Option::registerApiKey();
    Option::registerTransferCompleteMessage();
    Option::registerTransferCompleteShowUrl();
    Option::registerWeTransferMessage();
    Option::registerSuccessScript();

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

    // Register Ajax Functions
    Route::auth();
    Route::transfer();
    Route::items();
    Route::url();
    Route::completeFileUpload();
    Route::finalizeTransfer();
}
