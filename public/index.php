<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer auto-loader...
require __DIR__.'/../vendor/autoload.php';

// Run The Application...
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
