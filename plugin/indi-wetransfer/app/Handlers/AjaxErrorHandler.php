<?php

namespace WeTransfer\Handlers;

/**
 * AJAX Error Handler
 */
class AjaxErrorHandler {
    public static function echo(String $errorMessage) {
        echo json_encode([
            'success' => false,
            'message' => $errorMessage
        ]);
        die();
    }
}
