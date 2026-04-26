<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\V1\JobTitleController;
use App\Http\Controllers\Api\V1\TaskTypeController;
use App\Http\Controllers\Api\V1\DashboardController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\VerifyCronKey;
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('jobtitles', JobTitleController::class)->only(['index','store','destroy']);;
    Route::apiResource('tasktypes', TaskTypeController::class)->only(['index','store','destroy']);;
    Route::get('dashboard', [DashboardController::class, 'index']);
    
});


// روت لتشغيل المهام المجدولة من الخارج
Route::get('/v1/ping', function () {
    try {
        // استعلام خفيف جداً يثبت إن الداتا بيز صاحية بدون استهلاك موارد
        DB::select('SELECT 1'); 
        
        return response()->json([
            'status' => '🚨alive', 
            'database' => 'connected',
            'time' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error', 
            'database' => 'disconnected',
            'error' => $e->getMessage()
        ], 500);
    }
});
Route::prefix('v1/cron')->middleware(VerifyCronKey::class)->group(function () {

        // أ. روت مراجعة الديون (يضرب من Cron-job الساعة 1 بليل)
        Route::get('/late-payments', function () {
            Artisan::call('app:check-late-payments');
            return response()->json(['message' => '🔴Late payments checked!', 'output' => Artisan::output()]);
        });

        Route::get('/telegram-summary', function () {
            Artisan::call('app:send-daily-summary');
            return response()->json(['message' => '💰Telegram summary sent!', 'output' => Artisan::output()]);
        });
        Route::get('/backup-db', function () {
            Artisan::call('app:backup-db');
            return response()->json(['message' => '📦Database backup triggered!', 'output' => Artisan::output()]);
        });
    });