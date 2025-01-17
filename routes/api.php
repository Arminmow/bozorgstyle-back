<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Middleware\JwtMiddleware;

Route::get('/products', [ProductController::class, 'index']);

Route::get('products/{id}', [ProductController::class, 'show'])->where('id', '[0-9]+');

Route::get('products/men', [ProductController::class, 'getMenProducts']);

Route::get('products/women', [ProductController::class, 'getWomenProducts']);

Route::post('/register', [AuthController::class, 'register']);

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('user', [AuthController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::get('/categories', [CategoryController::class, 'index']);         // Get all categories

Route::get('/categories/{id}', [CategoryController::class, 'show']);    // Get category with products

Route::post('/categories', [CategoryController::class, 'store']);       // Create a new category

Route::put('/categories/{id}', [CategoryController::class, 'update']);  // Update category

Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // Delete category

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return response()->json(['user' => $request->user()]);
// });

