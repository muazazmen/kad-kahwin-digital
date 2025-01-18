<?php

use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('roles', RoleController::class);
Route::delete('roles/{role}', [RoleController::class, 'delete']);
Route::put('roles/{role}/restore', [RoleController::class, 'restore']);