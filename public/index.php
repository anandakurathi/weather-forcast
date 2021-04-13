<?php

require "../bootstrap.php";

header("Access-Control-Allow-Origin: *");
//header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$requestMethod = $_SERVER["REQUEST_METHOD"];

$routeList = [
    'forecast/initiate' => '\Src\Controllers\ForecastInitiateController#index#GET',
    'forecast' => '\Src\Controllers\WeatherNotifierController#index#GET'
];

$requestUri = trim($_SERVER['REQUEST_URI'], '/');
if (!$requestUri) {
    echo "Welcome to Weather check";
    die();
}

$route = matchRoute($requestUri, $routeList);
if(!$route) {
    halt();
}

list($className, $action, $method) = explode('#', $route);
if(strtoupper($method) !== $requestMethod) {
    halt();
}

if (is_callable(array($className, $action))) {
    $controller = new $className($dbConnection, $requestMethod);
    $controller->$action();
} else {
    halt();
}
