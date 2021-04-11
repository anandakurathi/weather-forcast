<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = new DotEnv(__DIR__);
$dotenv->load();

// create DB connection
$dbConnection = (new \Src\Config\DatabaseConnector())->getConnection();
