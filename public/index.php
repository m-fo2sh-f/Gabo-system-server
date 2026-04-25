<?php
// إجبار السيرفر على إظهار أي أخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));


    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    // استدعاء الكومبوزر
    require __DIR__.'/../vendor/autoload.php';

    // تشغيل لارافيل
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->handleRequest(Request::capture());
