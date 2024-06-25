<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AuthController;


Route::post('/team/store', [TeamController::class, 'store']);
Route::get('/team/index', [TeamController::class, 'index']);
Route::get('/team/datechart', [TeamController::class, 'datechart']);
Route::put('/team/update', [TeamController::class, 'update']);
Route::delete('/team/destroy', [TeamController::class, 'destroy']);
Route::get('/team/search', [TeamController::class, 'search']);
Route::get('/team/test', [TeamController::class, 'test']);
Route::get('/team/{id}', [TeamController::class, 'show']);



//auth

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'store']);
Route::post('logout', [AuthController::class, 'logout']);


