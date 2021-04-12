<?php

namespace Src\Controller;

use Src\Services\RouteeService;

class WeatherNotifierController
{
    public $routee;
    private $db;
    private $requestMethod;

    public function __construct(
        RouteeService $routeeService,
        $db,
        $requestMethod
    ) {
        $this->routee = $routeeService;
        $this->db = $db;
        $this->requestMethod = $requestMethod;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                $this->forecast();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            return $response['body'];
        }

        return null;

    }

    private function forecast()
    {
        $this->routee->getAuthenticated($this->db);
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
