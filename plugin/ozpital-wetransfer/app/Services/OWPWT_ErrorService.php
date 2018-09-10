<?php

namespace Ozpital\WPWeTransfer\Services;

use Ozpital\WPWeTransfer\Core\OWPWT_Plugin as Plugin;
use Ozpital\WPWeTransfer\Handlers\OWPWT_AjaxErrorHandler as AjaxErrorHandler;

/**
 * Error Service
 */
class OWPWT_ErrorService {
    public static function post() {
        if (!isset($_POST)) {
            AjaxErrorHandler::echo('No post data');
        }
    }

    public static function apiKey() {
        if (!get_option('api-key')) {
            AjaxErrorHandler::echo('API Key not set');
        }
    }

    public static function token() {
        if (!isset($_POST['token'])) {
            AjaxErrorHandler::echo('Token not supplied');
        }
    }

    public static function transferId() {
        if (!isset($_POST['transferId'])) {
            AjaxErrorHandler::echo('Transfer ID not supplied');
        }
    }

    public static function items() {
        if (!isset($_POST['items'])) {
            AjaxErrorHandler::echo('Items not supplied');
        }
    }

    public static function partNumber() {
        if (!isset($_POST['partNumber'])) {
            AjaxErrorHandler::echo('Part number not supplied');
        }
    }

    public static function multipartUploadId() {
        if (!isset($_POST['multipartUploadId'])) {
            AjaxErrorHandler::echo('Multipart Upload ID not supplied');
        }
    }

    public static function transfer() {
        if (!isset($_POST['transfer'])) {
            AjaxErrorHandler::echo('transfer not supplied');
        }
    }
}
