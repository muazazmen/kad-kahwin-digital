<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/me', [App\Http\Controllers\AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('auth/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('auth/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('auth/logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');

// Roles
Route::apiResource('roles', RoleController::class);
Route::delete('roles/{role}', [RoleController::class, 'delete']);
Route::put('roles/{role}/restore', [RoleController::class, 'restore']);

// TODO: Migrate table according to prisma.schema