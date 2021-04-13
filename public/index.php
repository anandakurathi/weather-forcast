<?php

require "../bootstrap.php";

header("Access-Control-Allow-Origin: *");
//header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$requestMethod = $_SERVER["REQUEST_METHOD"];

$routeList = [
    'forecast/initiate' => '\Src\Controllers\ForecastInitiateController#index',
    'forecast' => '\Src\Controllers\WeatherNotifierController#index'
];
$requestUri = trim($_SERVER['REQUEST_URI'], '/');
$route = matchRoute($requestUri, $routeList);
list($className, $action) = explode('#', $route);

if (is_callable(array($className, $action))) {
    $controller = new $className($dbConnection, $requestMethod);
    $controller->$action();
} else {
    header("HTTP/1.1 404 Not Found");
    exit();
}
