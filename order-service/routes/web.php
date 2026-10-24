<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});

// Endpoint Health Check di web.php
Route::get('/health', [OrderController::class, 'health']);

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::get('/orders/user/{userId}', [OrderController::class, 'getByUser']);
Route::patch('/orders/{id}/pay', [OrderController::class, 'pay']);