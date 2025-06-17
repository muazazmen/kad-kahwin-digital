<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FontController;
use App\Http\Controllers\FrameController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\PrayerController;
use App\Http\Controllers\SsoProviderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('auth')->group(function () {
  Route::post('register', [AuthController::class, 'register']);
  Route::post('login', [AuthController::class, 'login']);
  Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

  // reset password
  Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
  Route::post('reset-password', [AuthController::class, 'resetPassword']);
  
  // googleoAuth
  Route::get('google/signup', [SsoProviderController::class, 'googleSignUp']);
  Route::get('google/signin', [SsoProviderController::class, 'googleSignIn']);
  Route::get('google/redirect', [SsoProviderController::class, 'googleRedirect']);
  Route::get('google/connect', [SsoProviderController::class, 'connectGoogleAccount']);
});

/************************************** AUTHENTICATE USER **********************************/
Route::middleware('auth:sanctum')->group(function () {
  // profile
  Route::get('/me', [AuthController::class, 'me']);
  Route::post('/me/update', [AuthController::class, 'update']);
  Route::put('/me/update-password', [AuthController::class, 'updatePassword']);
  Route::delete('/me', [AuthController::class, 'destroy']);
});

/************************************** ADMIN **********************************/
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('admin')->group(function () {
  // users
  Route::apiResource('users', UserController::class);
  Route::put('users/{user}/restore', [UserController::class, 'restore']);

  // prayers
  Route::apiResource('prayers', PrayerController::class);
  Route::put('prayers/{prayer}/restore', [PrayerController::class, 'restore']);
  
  // musics
  // KIV: check if music update really need to be a POST request or can be a PUT request
  Route::apiResource('musics', MusicController::class);
  Route::put('musics/{music}/restore', [MusicController::class, 'restore']);

  // frames
  Route::apiResource('frames', FrameController::class);
  Route::put('frames/{frame}/restore', [FrameController::class, 'restore']);

});

/************************************** SUPER ADMIN **********************************/
Route::middleware(['auth:sanctum', 'is_super_admin'])->prefix('super-admin')->group(function () {
  // fonts
  Route::apiResource('fonts', FontController::class);
  Route::put('fonts/{font}/restore', [FontController::class, 'restore']);
});
