<?php

namespace WeTransfer\Services;

use WeTransfer\Core\Options as Options;
use WeTransfer\Core\Plugin as Plugin;
use WeTransfer\Handlers\AjaxErrorHandler as AjaxErrorHandler;

/**
 * Error Service
 */
class ErrorService {
    public static function post() {
        if (!isset($_POST)) {
            AjaxErrorHandler::echo('No post data');
        }
    }

    public static function apiKey() {
        if (!Options::getWeTransferApiKey()) {
            AjaxErrorHandler::echo('API Key not set');
        }
    }

    public static function token() {
        if (!isset($_POST['token'])) {
            AjaxErrorHandler::echo('Token not supplied');
        }
    }

    public static function transferId() {
        if (!isset($_POST['transfer_id'])) {
            AjaxErrorHandler::echo('Transfer ID not supplied');
        }
    }

    public static function items() {
        if (!isset($_POST['items'])) {
            AjaxErrorHandler::echo('Items not supplied');
        }
    }

    public static function partNumber() {
        if (!isset($_POST['part_number'])) {
            AjaxErrorHandler::echo('Part number not supplied');
        }
    }

    public static function uploadedParts() {
        if (!isset($_POST['uploaded_parts'])) {
            AjaxErrorHandler::echo('Uploaded parts not supplied');
        }
    }

    public static function fileId() {
        if (!isset($_POST['file_id'])) {
            AjaxErrorHandler::echo('File ID not supplied');
        }
    }

    public static function transfer() {
        if (!isset($_POST['transfer'])) {
            AjaxErrorHandler::echo('transfer not supplied');
        }
    }
}
