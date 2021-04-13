<?php


namespace Src\Controllers;


class BaseController
{

    /**
     * not Found Response
     * @return mixed|string
     */
    public function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $this->response($response);
    }

    /**
     * un processable Entity Response
     * @return mixed|string
     */
    public function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Unprocessable Entity'
        ]);
        return $this->response($response);
    }

    /**
     * Response helper
     * @param  string  $response
     * @return mixed|string
     */
    public function response($response)
    {
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
}
