<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/pos', [HomeController::class, 'pos'])->name('pos');
    Route::get('/products', [HomeController::class, 'products'])->name('products.index');
    Route::get('/customers', [HomeController::class, 'customers'])->name('customers.index');
    Route::get('/transactions', [HomeController::class, 'transactions'])->name('transactions.index');
});
