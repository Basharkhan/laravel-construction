<?php

use App\Http\Controllers\admin\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;

Route::post('authenticate', [AuthenticationController::class, 'authenticate']);

// protected route
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('logout', [AuthenticationController::class, 'logout']);
});