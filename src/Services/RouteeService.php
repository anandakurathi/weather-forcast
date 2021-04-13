<?php

namespace Src\Services;

use Src\Model\RouteeToken;

class RouteeService
{
    /**
     * @var RouteeToken
     */
    protected $routeeToken;
    /**
     * @var CurlService
     */
    protected $curl;
    /**
     * @var DatabaseConnector
     */
    protected $db;

    public function __construct(
        $db
    ) {
        $this->db = $db;
        $this->routeeToken = new RouteeToken($db);
        $this->curl = new CurlService();
    }

    /**
     * Get the token from DB as it has huge expiry time.
     * or generate it by calling the API
     * @return mixed|null
     */
    public function getAuthenticated()
    {
        $token = $this->routeeToken->getToken();
        if ($token) {
            return $token->token;
        }

        $url = getenv('ROUTEE_AUTH_URL').'oauth/token';
        $payload = "grant_type=client_credentials";
        $auth = $this->encodeString();
        $headers = $this->authRequestHeaders($auth);
        $response = $this->curl->makeRequest($url, 'POST', $headers, $payload);
        if ($response && isset($response['error'])) {
            return null;
        }

        $accessToken = ucfirst($response['token_type']).' '.$response['access_token'];
        $id = $this->routeeToken->insertToken($accessToken, $response['expires_in']);
        return $id ? $response['access_token'] : null;
    }

    /**
     * Send SMS
     * @param string $authToken
     * @param string $payload
     * @return mixed|null
     */
    public function sendSms(string $authToken, string $payload)
    {
        $headers = $this->smsRequestHeaders($authToken);
        $url = getenv('ROUTEE_CONNECT_URL').'sms';

        $response = $this->curl->makeRequest($url, 'POST', $headers, $payload);
        if ($response && (isset($response['error']) || isset($response['code']))) {
            return null;
        }
        return $response;
    }

    /**
     * prepare Un Encoded String
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

    /**
     * generate Authorisation request header for curl request
     * @param  string  $auth
     * @return array
     */
    private function authRequestHeaders(string $auth): array
    {
        return [
            "authorization: $auth",
            "content-type: application/x-www-form-urlencoded"
        ];
    }

    /**
     * generate send sms request header for curl request
     * @param  string  $auth
     * @return array
     */
    private function smsRequestHeaders(string $auth): array
    {
        return [
            "authorization: $auth",
            "content-type: application/json"
        ];
    }
}
