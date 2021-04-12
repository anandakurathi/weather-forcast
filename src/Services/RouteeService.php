<?php

namespace Src\Services;

use Src\Model\RouteeToken;

class RouteeService
{
    protected $routeeToken;
    protected $curl;

    public function __construct(
        RouteeToken $routeeToken,
        CurlService $curlService
    ) {
        $this->routeeToken = $routeeToken;
        $this->curl = $curlService;
    }

    public function getAuthenticated()
    {
        $token = $this->routeeToken->getToken();
        if ($token) {
            return $token->token;
        }

        $url = getenv('ROUTEE_URL').'oauth/token';
        $payload = "grant_type=client_credentials";
        $auth = $this->encodeString();
        $response = $this->curl->makeRequest($url, $payload, $auth);

        print_r($response); exit;

    }

    public function sendSms()
    {

    }

    /**
     * Prepare the
     * @return string
     */
    private function prepareUnEncodedString()
    {
        return getenv('ROUTEE_APP_ID').':'.getenv('ROUTEE_APP_SECRET');
    }

    /**
     * Generate the Base64 encoded string for the access token
     * @return string
     */
    private function encodeString()
    {
        return 'Basic '.base64_encode($this->prepareUnEncodedString());
    }

}
