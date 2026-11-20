<?php

use App\Http\Controllers\ShortenerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Socialite;

// Web Routes for Frontend Views
Route::get('/', function () {
    return view('shortener.create');
})->name('shortener.create-form');

Route::middleware('auth')->group(function () {
    Route::get('/shortener/dashboard', function () {
        return view('shortener.dashboard');
    })->name('shortener.dashboard');
});

// Outh Login routes (socialite_blikk)

Route::get('/oauth/login', function () {
    return Socialite::driver('blikk')->redirect();
})->name('login');

Route::get('/oauth/callback', [UserController::class, 'login']);

Route::post('/oauth/logout', [UserController::class, 'logout'])->name('logout');

// Public Routes - Redirect by hash
Route::get('{hash}', [ShortenerController::class, 'redirect'])
    ->name('shortener.redirect')
    ->where('hash', config('shortener.hash.allowed'));

// API Routes - Public endpoint for creating shorteners
Route::post('/api/shorten', [ShortenerController::class, 'store'])
    ->name('shortener.store')
    ->middleware('throttle:30,1');

Route::get('/api/shorten', [ShortenerController::class, 'getByHash'])
    ->name('shortener.getByHash');

// Protected Routes - Require Authentication
Route::middleware('auth')->group(function () {
    Route::get('/api/shorteners', [ShortenerController::class, 'index'])
        ->name('shortener.index');

    Route::get('/api/shorteners/{shortener}', [ShortenerController::class, 'show'])
        ->name('shortener.show');

    Route::patch('/api/shorteners/{shortener}', [ShortenerController::class, 'update'])
        ->name('shortener.update');

    Route::put('/api/shorteners/{shortener}', [ShortenerController::class, 'update'])
        ->name('shortener.update');

    Route::delete('/api/shorteners/{shortener}', [ShortenerController::class, 'destroy'])
        ->name('shortener.destroy');
});
