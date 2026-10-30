<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});

// Endpoint Health Check
Route::get('/health', [OrderController::class, 'health']);

Route::post('/orders', [OrderController::class, 'store']);

// Pindahkan rute spesifik 'user' ke ATAS rute parameter '{id}'
Route::get('/orders/user/{userId}', [OrderController::class, 'getByUser']);
Route::get('/orders/{id}', [OrderController::class, 'show']);

Route::patch('/orders/{id}/pay', [OrderController::class, 'pay']);