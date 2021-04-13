<?php


namespace Src\Services;

class OpenWeatherService
{
    /**
     * @var CurlService
     */
    protected $curl;

    public function __construct() {
        $this->curl = new CurlService();
    }

    /**
     * calling weather API to get current temperature in Thessaloniki
     * @return float
     */
    public function getCurrentWeather() : float
    {
        $payload = [
            'q' => getenv('OPEN_WEATHER_CITY'),
            'appid' => getenv('OPEN_WEATHER_KEY'),
            'units' => getenv('OPEN_WEATHER_UNITS')
        ];
        $queryString = http_build_query($payload);
        $url = getenv('OPEN_WEATHER_URL').'?'.$queryString;
        $weather = $this->curl->makeRequest($url, 'GET');
        if($weather &&  $weather['cod'] !== 200){
            return 0;
        }
        return $weather['main']['temp'];
    }

}
