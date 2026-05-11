<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SQLController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/generate-sql', [SQLController::class, 'generate']);

