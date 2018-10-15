<?php

namespace Ozpital\WPWeTransfer\Handlers;

/**
 * AJAX Error Handler
 */
class OWPWT_AjaxErrorHandler {
    public static function echo(String $errorMessage) {
        echo json_encode([
            'success' => false,
            'message' => $errorMessage
        ]);
        die();
    }
}
