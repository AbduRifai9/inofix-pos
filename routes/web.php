<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::get('/products', [HomeController::class, 'products'])->name('products.index');
    Route::get('/customers', [HomeController::class, 'customers'])->name('customers.index');
    Route::get('/transactions', [HomeController::class, 'transactions'])->name('transactions.index');
});
