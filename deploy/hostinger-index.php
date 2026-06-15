<?php

/**
 * Hostinger public_html index.php
 *
 * Place this file inside your Hostinger public_html folder.
 * It assumes your Laravel application is located at:
 *   /home/your-username/task-manager
 *
 * Adjust the path below to match your actual Laravel installation path.
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$laravelPath = __DIR__ . '/../task-manager';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $laravelPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $laravelPath . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once $laravelPath . '/bootstrap/app.php')
    ->handleRequest(Request::capture());
