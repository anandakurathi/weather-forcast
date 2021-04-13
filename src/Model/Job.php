<?php


namespace Src\Model;


class Job
{
    /**
     * @var string DB Table name
     */
    protected $table = 'jobs';

    private $db = null;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function createJob()
    {

    }

    public function getJob()
    {

    }
}
