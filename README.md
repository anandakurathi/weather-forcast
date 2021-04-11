# weather-forcast
weather-forcast

As a User I want a mechanism capable to examine whether data and depending
on the temperature send a sms message to a specified number.

Repeat the above procedure every 10 minutes for 10 times and then stop.

### The tech stack using
PHP 7.2
MySQL 5.6


### Installation 
using Composer for dependency / package manger.
using "vlucas/phpdotenv" for environment variables handling
```
{
    "require": {
        "vlucas/phpdotenv": "^2.4"
    },
    "autoload": {
        "psr-4": {
            "Src\\": "src/"
        }
    }
}
```
install our dependencies
``composer install``

copy `.env.example` to `.env`

### Configure a Database for Your PHP REST API
```
mysql -uroot -p
CREATE DATABASE weather CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'weather_user'@'localhost' identified by 'weather_password';
GRANT ALL on weather.* to 'weather_user'@'localhost';
quit
```
