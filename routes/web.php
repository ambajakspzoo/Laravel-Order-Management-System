<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/new', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{order}/add-item', [OrderController::class, 'addItem'])->name('orders.add-item');
Route::post('/orders/{order}/submit', [OrderController::class, 'submit'])->name('orders.submit');
Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/new', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/new', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
