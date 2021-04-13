<?php
require 'vendor/autoload.php';
// set the time zone
date_default_timezone_set("Europe/Athens");
use Dotenv\Dotenv;

$dotenv = new DotEnv(__DIR__);
$dotenv->load();

// create DB connection
$dbConnection = (new \Src\Config\DatabaseConnector())->getConnection();

function matchRoute($route, $routeList)
{
    return (array_key_exists($route, $routeList)) ? $routeList[$route] : null;
}
