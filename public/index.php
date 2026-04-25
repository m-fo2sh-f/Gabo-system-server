<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use Illuminate\Http\Request;
define('LARAVEL_START', microtime(true));

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    echo "<h1>🚨 Error Found:</h1>";
    echo "<b>Message:</b> " . $e->getMessage() . "<br>";
    echo "<b>File:</b> " . $e->getFile() . " on line " . $e->getLine();
    exit;
}