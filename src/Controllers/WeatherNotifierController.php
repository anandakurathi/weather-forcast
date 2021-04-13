<?php

namespace Src\Controllers;

use Src\Model\Job;
use Src\Services\OpenWeatherService;
use Src\Services\RouteeService;

/**
 * Class WeatherNotifierController
 * @package Src\Controllers
 */
class WeatherNotifierController extends BaseController
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

    /**
     * @var Job
     */
    private $jobs;

    public function __construct(
        $db,
        $requestMethod
    ) {
        $this->routee = new RouteeService($db);
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->openWeather = new OpenWeatherService;
        $this->jobs = new Job($db);
    }

    /**
     * Forecast the weather information to user
     * @return array
     */
    public function index()
    {
        $jobs = $this->jobs->getJob();
        if (!$jobs) {
            return $this->notFoundResponse();
        }

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
        $executedJobs = [];
        foreach ($jobs as $job) {
            // prepare SMS message and call Routee
            $message = $this->smsMessage($temperature);
            $payload = json_encode([
                'body' => $message,
                'to' => getenv('REPORTING_MOBILE_NUMBER'),
                'from' => 'amdTelecom'
            ]);
            $smsDetails = $this->routee->sendSms($authToken, $payload);
            if (!$smsDetails) {
                $this->jobs->updateJobById($job->id, $this->jobs::STATUS['ERROR']);
            }
            $this->jobs->updateJobById($job->id, $this->jobs::STATUS['DONE']);
            $executedJobs[] = $smsDetails;
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($executedJobs);
        return $this->response($response);
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
