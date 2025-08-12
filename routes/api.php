<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use App\Http\Controllers\API\AuthController;

// Issue Passport token
Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])
    ->name('passport.token');

// Auth routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected route
Route::middleware('auth:api')->get('/user', [AuthController::class, 'user']);
