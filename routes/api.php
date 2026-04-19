<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\TransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('transactions', TransactionController::class);
});
