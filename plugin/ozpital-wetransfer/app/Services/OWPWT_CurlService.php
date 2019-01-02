<?php

namespace Ozpital\WPWeTransfer\Services;

use Ozpital\WPWeTransfer\Core\OWPWT_Option as Option;
use Ozpital\WPWeTransfer\Services\OWPWT_ErrorService as Error;

/**
 * Curse Service
 */
class OWPWT_CurlService {
    /**
     * Prepare default url prefix
     */
    public static $url = 'https://dev.wetransfer.com/v2/';

    /**
     * Fetch reponse from url
     * @param  String $method  Post/Pull/Get
     * @param  String $url     URL to fetch from
     * @param  array  $headers Desired headers to send with request
     * @param  array  $data    Desired data to send with request
     * @return String          Any response from URL
     */
    public static function fetch(String $method, String $url, Array $headers = [], Array $data = []) {
        Error::apiKey();

        // Default headers for each curl request
        $defaultHeaders = [
            'Content-Type: application/json',
            'x-api-key: ' . Option::getApiKey()
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

        // If method is 'PUT'
        if (strtolower($method) === 'put') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
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

    /**
     * Prepare WeTransfer AUTH request
     */
    public static function auth() {
        Error::post();

        $url = 'authorize';

        $response = self::fetch('POST', $url);

        echo $response;
        die();
    }

    /**
     * Prepare WeTransfer TRANSFER request
     */
    public static function transfer() {
        Error::post();
        Error::token();

        $url = 'transfers';

        $headers = [
            'Authorization: Bearer ' . $_POST['token']
        ];

        $data = [
            'message' => Option::getWeTransferMessage(),
            'files' => $_POST['files']
        ];

        echo self::fetch('POST', $url, $headers, $data);
        die();
    }

    /**
     * Prepare WeTransfer UPLOAD URL request
     */
    public static function url() {
        Error::post();
        Error::token();
        Error::transferId();
        Error::fileId();
        Error::partNumber();

        $headers = [
            'Authorization: Bearer ' . $_POST['token']
        ];

        $url = ('transfers/'. $_POST['transfer_id'] .'/files/'. $_POST['file_id'] .'/upload-url/'. $_POST['part_number']);

        echo self::fetch('GET', $url, $headers);
        die();
    }

    /**
     * Prepare WeTransfer Complete A File Upload request
     */
    public static function completeFileUpload() {
        Error::post();
        Error::token();
        Error::transferId();
        Error::fileId();
        Error::uploadedParts();

        $method = 'PUT';

        $headers = [
            'Authorization: Bearer ' . $_POST['token']
        ];

        $data = [
            'part_numbers' => $_POST['uploaded_parts'],
        ];

        $url = ('transfers/'. $_POST['transfer_id'] .'/files/'. $_POST['file_id'] .'/upload-complete');

        echo self::fetch($method, $url, $headers, $data);
        die();
    }

    /**
     * Prepare WeTransfer Complete A File Upload request
     */
    public static function finalizeTransfer() {
        Error::post();
        Error::token();
        Error::transferId();

        $method = 'PUT';

        $headers = [
            'Authorization: Bearer ' . $_POST['token']
        ];

        $url = ('transfers/'. $_POST['transfer_id'] .'/finalize');

        echo self::fetch($method, $url, $headers);
        die();
    }
}
