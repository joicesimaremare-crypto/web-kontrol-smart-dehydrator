<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::get('/device/command', [DeviceController::class, 'command']);
Route::post('/device/report', [DeviceController::class, 'report']);