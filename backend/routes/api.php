<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('expenses', ExpenseController::class);
    Route::get('/expenses/export', [ExpenseController::class, 'export']);
    Route::get('/expenses/export-pdf', [ExpenseController::class, 'exportPdf']);
    Route::apiResource('groups', GroupController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
});
