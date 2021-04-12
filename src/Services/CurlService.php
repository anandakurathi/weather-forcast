<?php


namespace Src\Services;


class CurlService
{
    /**
     * generate header for curl request
     * @param string $auth
     * @return array
     */
    private function requestHeaders(string $auth): array
    {
        return [
            "authorization: $auth",
            "content-type: application/x-www-form-urlencoded"
        ];
    }

    /**
     * @param  string  $url
     * @param string $payload
     * @param string $auth
     * @return mixed|null
     */
    public function makeRequest(string $url, string $payload, string $auth)
    {
        $headers = $this->requestHeaders($auth);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            error_log("cURL Error #:".$err);
            return null;
        } else {
            if ($this->isJson($response)) {
                return json_decode($response);
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
