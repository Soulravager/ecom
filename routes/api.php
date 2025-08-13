<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\API\ProductController;

// Issue Passport token
Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])
    ->name('passport.token');

// Auth routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);


// Protected route
Route::middleware('auth:api')->get('/user', [AuthController::class, 'user']);



// Public routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Protected routes (requires login)
Route::middleware(['auth:api', 'role:admin,staff'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});
    