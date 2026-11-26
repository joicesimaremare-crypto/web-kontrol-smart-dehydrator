<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DeviceController::class, 'index']);
Route::get('/status', [DeviceController::class, 'status']);
Route::post('/control', [DeviceController::class, 'control']);