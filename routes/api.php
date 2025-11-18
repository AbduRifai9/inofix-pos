<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthApiController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthApiController::class, 'logout']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Product routes
    Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('api.products.store');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('api.products.show');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('api.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('api.products.destroy');

    // Customer routes
    Route::get('/customers', [CustomerApiController::class, 'index'])->name('api.customers.index');
    Route::post('/customers', [CustomerApiController::class, 'store'])->name('api.customers.store');
    Route::get('/customers/{customer}', [CustomerApiController::class, 'show'])->name('api.customers.show');
    Route::put('/customers/{customer}', [CustomerApiController::class, 'update'])->name('api.customers.update');
    Route::delete('/customers/{customer}', [CustomerApiController::class, 'destroy'])->name('api.customers.destroy');

    // Transaction routes
    Route::get('/transactions', [TransactionApiController::class, 'index'])->name('api.transactions.index');
    Route::post('/transactions', [TransactionApiController::class, 'store'])->name('api.transactions.store');
    Route::get('/transactions/{transaction}', [TransactionApiController::class, 'show'])->name('api.transactions.show');
});
