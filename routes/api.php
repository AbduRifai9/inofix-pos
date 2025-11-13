<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthApiController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthApiController::class, 'logout']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerApiController::class);
    Route::resource('transactions', TransactionApiController::class);
});
