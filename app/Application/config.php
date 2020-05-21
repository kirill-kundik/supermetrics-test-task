<?php

// Http Url
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('HTTP_URL', '/' . substr_replace(trim($_SERVER['REQUEST_URI'], '/'), '', 0, strlen($scriptName)));

// Define Path Application
define('SCRIPT', str_replace('\\', '/', rtrim(__DIR__ . "/../", '/')) . '/');
define('SYSTEM', SCRIPT . 'System/');
define('CONTROLLERS', SCRIPT . 'Application/Controllers/');
define('MODELS', SCRIPT . 'Application/Models/');
define('VIEWS', SCRIPT . 'Application/Views/');

// Config Database
define('DATABASE', [
    'Port' => getenv('DB_PORT'),
    'Host' => getenv('DB_HOST'),
    'Driver' => getenv('DB_DRIVER'),
    'Name' => getenv('DB_NAME'),
    'User' => getenv('DB_USER'),
    'Pass' => getenv('DB_PASS'),
    'Prefix' => getenv('DB_PREFIX')
]);

// DB_PREFIX
define('DB_PREFIX', getenv('DB_PREFIX'));