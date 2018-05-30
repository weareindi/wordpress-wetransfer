<?php

namespace WeTransfer\Handlers;

class AjaxErrorHandler {
    public static function echo(String $errorMessage) {
        echo json_encode([
            'success' => false,
            'message' => $errorMessage
        ]);
        die();
    }
}
