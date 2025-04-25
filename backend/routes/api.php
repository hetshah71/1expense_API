<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;




Route::apiResource('expenses', ExpenseController::class);
Route::apiResource('groups', GroupController::class);
