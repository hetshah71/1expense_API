<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('expenses.index');
    return view('welcome');
});


Route::resource('expenses', ExpenseController::class);
Route::resource('groups', GroupController::class);
