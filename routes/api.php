<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserManagementController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\GeminiController;

Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])
    ->name('passport.token');


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:api')->get('/user', [AuthController::class, 'user']);

//public,show products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/new', [ProductController::class, 'hotProduct']); 

//for admin/staff to control products 
Route::middleware(['auth:api', 'role:admin,staff'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::patch('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);    
});

//for admin 
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/accounts', [UserManagementController::class, 'getAllAccounts']); 
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
    Route::patch('/users/{id}/assign-staff', [UserManagementController::class, 'assignStaff']);
    Route::patch('/users/{id}/assign-user', [UserManagementController::class, 'assignUser']);

});

//carts
Route::middleware('auth:api')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
});

//Orders
Route::middleware('auth:api')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/verify', [OrderController::class, 'verifyPayment']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
});

//admin/staff order conformation(payment conf) and  Update delivery status
Route::middleware(['auth:api', 'role:admin,staff'])->group(function () {
    Route::get('/admin/orders', [OrderController::class, 'GetAllOrders']);
    Route::patch('/orders/{id}/status', [OrderController::class,'updateStatus']);
    Route::patch('/orders/{id}/delivery-status', [OrderController::class, 'DeliveryStatus']);
});

// Route::middleware(['auth:api', 'role:admin,staff'])->get('/admin/orders', [OrderController::class, 'GetAllOrders']);
//dashboard 
Route::middleware(['auth:api', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);       
    Route::get('/dashboard/stockstat', [DashboardController::class, 'lowStock']); 
    Route::post('/dashboard/salestat', [DashboardController::class, 'salesStats']); 
});

//gemini 
Route::post('/gemini', [GeminiController::class, 'generate']);