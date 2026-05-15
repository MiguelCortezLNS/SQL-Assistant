<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SQLController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// SQL Generator
Route::post('/generate-sql', [SQLController::class, 'generate']);

// SQL Learning
Route::get('/sql-learning', [LearningController::class, 'index']);
Route::post('/ask-sql-question', [LearningController::class, 'ask']);

// Chats (session-based, no login required)
Route::get('/chats', [ChatController::class, 'index']);
Route::post('/chats', [ChatController::class, 'store']);
Route::get('/chats/{chat}', [ChatController::class, 'show']);
Route::post('/chats/{chat}/messages', [ChatController::class, 'storeMessage']);
Route::delete('/chats/{chat}', [ChatController::class, 'destroy']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
