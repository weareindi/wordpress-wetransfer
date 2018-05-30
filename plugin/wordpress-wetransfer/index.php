<?php

/*
Plugin Name: WordPress WeTransfer
Description: Upload to WeTransfer without leaving wordpress
Version: 0.0.1
Author: Laurence Archer
Author URI: https://ozpital.com
*/

require_once('autoload.php');

use WeTransfer\Core\Assets;
use WeTransfer\Core\Menu;
use WeTransfer\Core\Option;
use WeTransfer\Core\Shortcode;
use WeTransfer\Services\AjaxRouteService as Route;

// Define plugin root
define('WORDPRESS_WETRANSFER_DIR', __DIR__ . DIRECTORY_SEPARATOR);
define('WORDPRESS_WETRANSFER_URL', plugin_dir_url(__FILE__));

// Register Options
Option::registerApiKey();

// Register menu
Menu::register();

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
Route::completeTransfer();
