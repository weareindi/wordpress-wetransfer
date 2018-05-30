<?php

namespace WeTransfer\Services;

use WeTransfer\Services\ErrorService as Error;

class CurlService {
    public static $url = 'https://dev.wetransfer.com/v1/';

    public static function fetch(String $method, String $url, Array $headers = [], Array $data = []) {
        Error::apiKey();

        // Default headers for each curl request
        $defaultHeaders = [
            'Content-Type: application/json',
            'x-api-key: ' . get_option('api-key')
        ];

        // Initalise curl request
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, self::$url . $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headers));

        // If method is 'POST'
        if (strtolower($method) === 'post') {
            curl_setopt($curl, CURLOPT_POST, 1);
        }

        // Post data if required
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, JSON_NUMERIC_CHECK));
        }

        // Get response
        $curlReponse = curl_exec($curl);

        // Close curl connection
        curl_close($curl);

        // Return curl response
        return $curlReponse;
    }

    public static function auth() {
        Error::post();

        $url = 'authorize';

        $response = self::fetch('POST', $url);

        $object = json_decode($response);
        if (!$object->token) {
            Error::echo('Token request unsuccessful');
        }

        echo $response;
        die();
    }

    public static function transfer() {
        Error::post();
        Error::token();

        $url = 'transfers';

        $headers = [
            'Authorization: Bearer ' . $_POST['token']
        ];

        $data = [
            'name' => 'WordPress WeTransfer'
        ];

        echo self::fetch('POST', $url, $headers, $data);
        die();
    }

    public static function items() {
        Error::post();
        Error::token();
        Error::transferId();
        Error::items();

        $url = 'transfers/'. $_POST['transferId'] .'/items';

        $headers = [
            'Authorization: Bearer ' . $_POST['token']
        ];

        $data = [
            'items' => $_POST['items']
        ];

        echo self::fetch('POST', $url, $headers, $data);
        die();
    }

    public static function url() {
        Error::post();
        Error::token();
        Error::transferId();
        Error::partNumber();
        Error::multipartUploadId();

        $headers = [
            'Authorization: Bearer ' . $_POST['token']
        ];

        $url = ('files/'. $_POST['transferId'] .'/uploads/'. $_POST['partNumber'] .'/'. $_POST['multipartUploadId']);

        echo self::fetch('GET', $url, $headers);
        die();
    }

    public static function completeTransfer() {
        Error::post();
        Error::token();
        Error::transferId();

        $method = 'POST';

        $headers = [
            'Authorization: Bearer ' . $_POST['token']
        ];

        $url = ('files/'. $_POST['transferId'] .'/uploads/complete');

        echo self::fetch('POST', $url, $headers);
        die();
    }
}
