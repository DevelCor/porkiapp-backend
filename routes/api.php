<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PigController;
use App\Http\Controllers\PigTreatmentController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // User and Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users/me', [UserController::class, 'show']);

    // Farm routes
    Route::post('/farms', [FarmController::class, 'store']);
    Route::post('/farms/join', [FarmController::class, 'join']);


    // pigs routes
    //create pig
    Route::post('/pig/create', [PigController::class, 'store']);
    //get all pigs
    Route::get('/pig', [PigController::class, 'index']);
    //get pig by id
    Route::get('/pig/{id}', [PigController::class, 'show']);
    //update pig
    Route::put('/pig/update/{id}', [PigController::class, 'update']);
    //delete pig
    Route::delete('/pig/delete/{id}', [PigController::class, 'destroy']);

    Route::apiResource('events', EventController::class);
    Route::put('events/{id}/mark-inactive', [EventController::class, 'setEventInactive']);
    
    // Treatment routes
    Route::get('/pig/{id}/treatments', [PigTreatmentController::class, 'getTreatmentsByPigId']);
    Route::post('/treatments', [PigTreatmentController::class, 'store']);
    Route::put('/treatments/{id}', [PigTreatmentController::class, 'update']);
});
