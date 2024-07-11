<?php

use App\Http\Controllers\API\AuthorizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(AuthorizationController::class)->group(function () {
    Route::post('/auth/register', 'register');
    Route::post('/auth/login', 'login');
    Route::post('/auth/logout', 'logout')->middleware('auth:sanctum');
});
