<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SsoProviderController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;
// TODO: postman cannot support put, patch if headers multipart/form-data, need to chnge when frontend were setup
// Auth
Route::prefix('auth')->group(function () {
  Route::post('register', [AuthController::class, 'register']);
  Route::post('login', [AuthController::class, 'login']);
  Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
  
  // googleoAuth
  Route::get('google', [SsoProviderController::class, 'googleLogin']);
  Route::get('google/redirect', [SsoProviderController::class, 'googleRedirect']);
});
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::match(['put', 'post'],'/me/update', [AuthController::class, 'update'])->middleware('auth:sanctum');

/************************************** ADMIN **********************************/
// users
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('admin')->group(function () {
  Route::apiResource('users', App\Http\Controllers\UserController::class);
});
