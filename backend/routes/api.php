<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\EffectController;
use App\Http\Controllers\FontController;
use App\Http\Controllers\FrameController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\OpeningAnimationController;
use App\Http\Controllers\PrayerController;
use App\Http\Controllers\SsoProviderController;
use App\Http\Controllers\ThemeController;
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

/************************************** PUBLIC **********************************/
// themes
Route::get('themes', [ThemeController::class, 'indexWithoutTrashed']);

// designs
Route::get('designs', [DesignController::class, 'indexWithoutTrashed']);

// musics
Route::get('musics', [MusicController::class, 'indexWithoutTrashed']);

// prayers
Route::get('prayers', [PrayerController::class, 'indexWithoutTrashed']);

// frames
Route::get('frames', [FrameController::class, 'indexWithoutTrashed']);

// fonts
Route::get('fonts', [FontController::class, 'indexWithoutTrashed']);

// Openings
Route::get('openings', [OpeningAnimationController::class, 'indexWithoutTrashed']);
Route::get('openings/{opening}', [OpeningAnimationController::class, 'show']);

// Effects
Route::get('effects', [EffectController::class, 'indexWithoutTrashed']);
Route::get('effects/{effect}', [EffectController::class, 'show']);

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
  Route::apiResource('musics', MusicController::class);
  Route::put('musics/{music}/restore', [MusicController::class, 'restore']);

  // frames
  Route::apiResource('frames', FrameController::class);
  Route::put('frames/{frame}/restore', [FrameController::class, 'restore']);

  // themes
  Route::apiResource('themes', ThemeController::class);
  Route::put('themes/{theme}/restore', [ThemeController::class, 'restore']);

  // designs
  Route::apiResource('designs', DesignController::class);
  Route::put('designs/{design}/restore', [DesignController::class, 'restore']);
});

/************************************** SUPER ADMIN **********************************/
Route::middleware(['auth:sanctum', 'is_super_admin'])->prefix('super-admin')->group(function () {
  // fonts
  Route::apiResource('fonts', FontController::class);
  Route::put('fonts/{font}/restore', [FontController::class, 'restore']);

  // openings
  Route::apiResource('openings', OpeningAnimationController::class);
  Route::put('openings/{opening}/restore', [OpeningAnimationController::class, 'restore']);

  // effects
  Route::apiResource('effects', EffectController::class);
  Route::put('effects/{effect}/restore', [EffectController::class, 'restore']);
});
