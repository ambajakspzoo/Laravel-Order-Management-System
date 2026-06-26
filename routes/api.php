<?php

use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardApiController::class, 'index']);

Route::apiResource('customers', CustomerApiController::class)->only(['index', 'show', 'store', 'update']);
Route::apiResource('products', ProductApiController::class)->only(['index', 'show', 'store', 'update']);

Route::get('/orders', [OrderApiController::class, 'index']);
Route::post('/orders', [OrderApiController::class, 'store']);
Route::get('/orders/{order}', [OrderApiController::class, 'show']);
Route::post('/orders/{order}/items', [OrderApiController::class, 'addItem']);
Route::delete('/orders/{order}/items/{itemId}', [OrderApiController::class, 'removeItem']);
Route::post('/orders/{order}/submit', [OrderApiController::class, 'submit']);
Route::patch('/orders/{order}/status', [OrderApiController::class, 'updateStatus']);
Route::patch('/orders/{order}/pricing', [OrderApiController::class, 'updatePricing']);
