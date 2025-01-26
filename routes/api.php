<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\CheckRole;

// Public Routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show'])->where('id', '[0-9]+');
Route::get('products/men', [ProductController::class, 'getMenProducts']);
Route::get('products/women', [ProductController::class, 'getWomenProducts']);
Route::get('/categories', [CategoryController::class, 'index']);         // Get all categories
Route::get('/categories/{id}', [CategoryController::class, 'show']);    // Get category with products

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes for authenticated users (JWT protected)
Route::middleware([JwtMiddleware::class])->group(function () {
    
    // User-related routes
    Route::get('user', [AuthController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Cart-related routes
    Route::post('cart/add', [CartController::class, 'addToCart']);
    Route::get('cart', [CartController::class, 'view']);
});

// Admin Routes (protected by role check)
Route::middleware([JwtMiddleware::class, CheckRole::class.':admin'])->group(function () {
    
    // Product-related routes
    Route::post('/product-images/add', [ProductImageController::class, 'store']);
    Route::post('/products/add', [ProductController::class, 'store']);
    Route::put('/products/update/{id}', [ProductController::class, 'update']);
    
    // Category Routes
    Route::post('/categories', [CategoryController::class, 'store']);       // Create a new category
    Route::put('/categories/{id}', [CategoryController::class, 'update']);  // Update category
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // Delete category

    // Admin Dashboard Route
    Route::get('/admin-dashboard', function () {
        return view('admin.dashboard');
    });
});
