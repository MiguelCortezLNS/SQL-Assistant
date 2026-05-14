<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SQLController;
use App\Http\Controllers\LearningController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/generate-sql', [SQLController::class, 'generate']);

Route::get('/sql-learning', [LearningController::class, 'index']);
Route::post('/ask-sql-question', [LearningController::class, 'ask']);

