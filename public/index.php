<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Cek otomatis keberadaan folder 'vendor' di luar public (untuk Lokal)
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';
} 
// Jika tidak ada, gunakan struktur folder InfinityFree (cbt-core)
else {
    require __DIR__.'/cbt-core/vendor/autoload.php';
    /** @var Application $app */
    $app = require_once __DIR__.'/cbt-core/bootstrap/app.php';
}

$app->handleRequest(Request::capture());
