<?php
// إجبار السيرفر على إظهار أي أخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

try {
    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    // استدعاء الكومبوزر
    require __DIR__.'/../vendor/autoload.php';

    // تشغيل لارافيل
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->handleRequest(Request::capture());

} catch (\Throwable $e) {
    // الكمين: لو حصل أي كراش، اطبعه هنا بدل الشاشة البيضا
    echo "<div style='font-family: Arial; padding: 20px; background: #ffebee; border: 2px solid #f44336; border-radius: 10px; direction: ltr;'>";
    echo "<h2 style='color:#d32f2f'>🚨 كمين فؤش: Laravel Fatal Crash 🚨</h2>";
    echo "<b>Message:</b> " . $e->getMessage() . "<br><br>";
    echo "<b>File:</b> " . $e->getFile() . " (Line: " . $e->getLine() . ")<br><br>";
    echo "<b>Trace:</b><pre style='background: #fff; padding: 10px; overflow: auto;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}