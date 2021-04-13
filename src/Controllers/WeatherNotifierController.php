<?php

namespace Src\Controllers;

use Src\Services\OpenWeatherService;
use Src\Services\RouteeService;

/**
 * Class WeatherNotifierController
 * @package Src\Controllers
 */
class WeatherNotifierController
{
    /**
     * @var RouteeService
     */
    public $routee;
    /**
     * @var DatabaseConnector
     */
    private $db;
    /**
     * @var string HTTP Request type
     */
    private $requestMethod;
    /**
     * @var OpenWeatherService
     */
    private $openWeather;

    public function __construct(
        $db,
        $requestMethod
    ) {
        $this->routee = new RouteeService($db);
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->openWeather = new OpenWeatherService;
    }

    /**
     * Process the http request based on the request method for now supporting GET
     */
    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                $response = $this->forecast();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    /**
     * Forecast the weather information to user
     * @return array
     */
    private function forecast()
    {
        // make a call to open weather and get temperature
        $temperature = $this->openWeather->getCurrentWeather();
        if (!$temperature) {
            return $this->unprocessableEntityResponse();
        }

        // Get the Authorisation token from Routee to send SMS
        $authToken = $this->routee->getAuthenticated($this->db);
        if (!$authToken) {
            return $this->unprocessableEntityResponse();
        }

        // prepare SMS message and call Routee
        $message = $this->smsMessage($temperature);
        $payload = json_encode([
            'body' => $message,
            'to' => getenv('REPORTING_MOBILE_NUMBER'),
            'from' => 'amdTelecom'
        ]);
        $smsDetails = $this->routee->sendSms($authToken, $payload);
        if (!$smsDetails) {
            return $this->unprocessableEntityResponse();
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $smsDetails;
        return $response;
    }

    /**
     * unprocessable Entity Response
     * @return array
     */
    private function unprocessableEntityResponse() : array
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Unprocessable Entity'
        ]);
        return $response;
    }

    /**
     * not Found Response
     * @return array
     */
    private function notFoundResponse() : array
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }

    /**
     * Generate the SMS message
     * @param  float  $temperature
     * @return string
     */
    private function smsMessage(float $temperature): string
    {
        $tempState = ((float)$temperature > 20) ? "more" : "less";
        return "Anand Kumar and Temperature $tempState than 20C. $temperature";
    }
}
