<?php


namespace Src\Services;


class CurlService
{
    /**
     * Make a request to third-party using defined parameters.
     * @param  string  $url
     * @param  string  $requestMethod
     * @param  array  $headers
     * @param  string|nulls  $payload
     * @return mixed|null
     */
    public function makeRequest(
        string $url,
        $requestMethod = 'POST',
        $headers = [],
        string $payload = null
    ) {
        $curlParams = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $requestMethod,
        ];

        if ($headers) {
            $curlParams [CURLOPT_HTTPHEADER] = $headers;
        }

        if ('GET' !== strtoupper($requestMethod)) {
            $curlParams [CURLOPT_POSTFIELDS] = $payload;
        }

        $curl = curl_init();
        curl_setopt_array($curl, $curlParams);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            error_log("cURL Error #:".$err);
            return null;
        } else {
            if ($this->isJson($response)) {
                return json_decode($response, true);
            }
            return null;
        }
    }

    /**
     * Check the string is JSON or not
     *
     * @param $string
     * @return bool
     */
    public function isJson($string): bool
    {
        if ($string) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }
        return true;
    }
}
