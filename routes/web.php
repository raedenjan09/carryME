<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BagController;
use App\Http\Controllers\Admin\BagImageController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserAccountController;
use App\Models\Bag;

// Root Route (User Dashboard as Landing Page)
Route::get('/', [UserDashboardController::class, 'index'])->name('user.dashboard');

// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Logout Route
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Admin Routes
    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
        
        Route::resource('bags', BagController::class);
        Route::post('bags/import', [BagController::class, 'import'])->name('bags.import');
        Route::post('bags/{bag}/restore', [BagController::class, 'restore'])->name('bags.restore');
        Route::get('/admin/bags', [App\Http\Controllers\Admin\BagController::class, 'index'])->name('bags.index');

        Route::delete('/bag-images/{bagImage}', [BagImageController::class, 'destroy'])->name('bag-images.destroy');
        Route::post('/bag-images/{bagImage}/make-primary', [BagImageController::class, 'makePrimary'])->name('bag-images.make-primary');

        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('admin.users.status');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.role');
    });

    Route::resource('users', UserController::class);

    // User Routes
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard')->middleware('auth');
    Route::get('/account', [UserAccountController::class, 'index'])->name('user.account');
    Route::put('/account/profile', [UserAccountController::class, 'updateProfile'])->name('user.profile.update');

    Route::post('/products/{id}/review', [ProductController::class, 'addReview'])->name('products.review');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::put('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
});

// Middleware for active users
Route::middleware(['auth', 'active'])->group(function () {
    // Your protected routes here
});

// Test middleware route
Route::get('/test-middleware', function() {
    return 'Middleware test route';
})->middleware('admin');

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/category/{slug}', [ProductController::class, 'category'])->name('category.show');
Route::get('/promo/summer', [ProductController::class, 'summerSale'])->name('promo.summer');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
