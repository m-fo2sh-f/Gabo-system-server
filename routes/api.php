<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Auth\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('transactions', TransactionController::class);
    
});
