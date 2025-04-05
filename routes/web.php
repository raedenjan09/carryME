<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BagController;
use App\Http\Controllers\Admin\BagImageController;

// Welcome Route
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Admin Routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard', function () {
                return view('admin.dashboard');
            })->name('admin.dashboard');
            
            Route::resource('bags', BagController::class);
            Route::post('bags/import', [BagController::class, 'import'])->name('bags.import');
            Route::post('bags/{bag}/restore', [BagController::class, 'restore'])->name('bags.restore');
        });

        Route::delete('/bag-images/{bagImage}', [BagImageController::class, 'destroy'])->name('bag-images.destroy');
        Route::post('/bag-images/{bagImage}/make-primary', [BagImageController::class, 'makePrimary'])->name('bag-images.make-primary');
    });

    Route::resource('users', UserController::class);

    // User Routes
    Route::get('/home', function() {
        return view('home');
    })->name('home');
});

// Test middleware route
Route::get('/test-middleware', function() {
    return 'Middleware test route';
})->middleware('admin');
