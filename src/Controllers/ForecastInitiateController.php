<?php


namespace Src\Controllers;


use Src\Model\Job;
use Src\Services\OpenWeatherService;
use Src\Services\RouteeService;

class ForecastInitiateController extends BaseController
{
    /**
     * @var DatabaseConnector
     */
    private $db;
    /**
     * @var string HTTP Request type
     */
    private $requestMethod;
    /**
     * @var Job
     */
    private $jobs;

    public function __construct(
        $db,
        $requestMethod
    ) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->jobs = new Job($db);
    }

    /**
     * Initiate the job
     * @return mixed|string
     */
    public function index()
    {
        for ($i = 0; $i <= 9; $i++) {
            $seconds = ($i * 10);
            $whenTo = $this->nextJob($seconds);
            $this->jobs->createJob($whenTo);
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'message' => 'Jobs Queued'
        ]);
        return $this->response($response);
    }

    /**
     * next job
     * @param  int  $minutes
     * @return false|string
     */
    public function nextJob(int $minutes)
    {
        return date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +$minutes minutes"));
    }
}
