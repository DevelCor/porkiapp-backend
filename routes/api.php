<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PigController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // User and Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users/me', [UserController::class, 'show']);

    // Farm routes
    Route::post('/farms', [FarmController::class, 'store']);
    Route::post('/farms/join', [FarmController::class, 'join']);


    // Farm routes
    Route::post('/pig/create', [PigController::class, 'store']);
    Route::get('/pig', [PigController::class, 'index']);
    //get pig by id
    Route::get('/pig/{id}', [PigController::class, 'show']);
    //update pig
    Route::put('/pig/update/{id}', [PigController::class, 'update']);
});
