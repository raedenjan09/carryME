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
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\OrderController;
use App\Models\Bag;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\CustomVerifyEmail;
use App\Http\Controllers\AdminController;

// Root Route (User Dashboard as Landing Page)
Route::get('/', [UserDashboardController::class, 'index'])->name('user.dashboard');
Route::get('/user/dashboard', [UserDashboardController::class, 'index']);

// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Auth routes with verification
// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])
         ->name('verification.notice');
         
    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])
         ->middleware(['signed'])
         ->name('verification.verify');
         
    Route::post('/email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])
         ->middleware(['throttle:6,1'])
         ->name('verification.resend');
});

// Authentication Routes
Auth::routes(['verify' => false]);

// Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // User Routes
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index');
    Route::get('/account', [UserAccountController::class, 'index'])->name('user.account');
    Route::put('/account/profile', [UserAccountController::class, 'updateProfile'])
        ->name('user.profile.update');

    // Product Reviews
    Route::post('/products/{id}/review', [ProductController::class, 'addReview'])
        ->name('products.review');

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::put('/cart/{item}', [CartController::class, 'update'])->name('cart.update');

    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    // User Account Routes
    Route::get('/account', [UserAccountController::class, 'index'])->name('user.account');
    Route::get('/account/edit', [UserAccountController::class, 'edit'])->name('user.account.edit');
    Route::put('/account/update', [UserAccountController::class, 'update'])->name('user.account.update');
    Route::get('/account/orders', [UserAccountController::class, 'orders'])->name('user.account.orders');

    // Order Reviews
    Route::post('/orders/{order}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/orders/{order}/reviews', [ReviewController::class, 'store'])->name('user.reviews.store');

    // Account routes
    Route::get('/account', [UserAccountController::class, 'index'])->name('user.account');
    Route::get('/account/orders', [UserAccountController::class, 'orders'])->name('user.account.orders');
    Route::get('/account/reviews', [UserAccountController::class, 'reviews'])->name('user.account.reviews');

    // Review Update Route
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('user.reviews.update');
});

// User routes that require verification
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    // ...other user routes...
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Resource Routes
    Route::resource('orders', OrderController::class);
    Route::resource('users', UserController::class);
    Route::resource('bags', BagController::class);

    // Additional Routes
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
    Route::post('bags/import', [BagController::class, 'import'])->name('bags.import');
    Route::post('bags/{bag}/restore', [BagController::class, 'restore'])->name('bags.restore');
    Route::patch('/bags/{bag}/stock', [BagController::class, 'updateStock'])->name('bags.updateStock');
    Route::delete('/bag-images/{bagImage}', [BagImageController::class, 'destroy'])
        ->name('bag-images.destroy');
    Route::post('/bag-images/{bagImage}/make-primary', [BagImageController::class, 'makePrimary'])
        ->name('bag-images.make-primary');
});

// Admin routes that don't require verification
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    // ...other admin routes...
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    // ...other admin routes...
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
});

// Admin routes (no email verification required)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    // ...other admin routes
});

// User routes
Route::middleware(['auth', 'email.verify'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

// User routes that require verification
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    // ...other user routes...
});

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/category/{slug}', [ProductController::class, 'category'])->name('category.show');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Admin routes without email verification
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Bag routes with explicit naming
        Route::get('/bags', [BagController::class, 'index'])->name('bags.index');
        Route::get('/bags/create', [BagController::class, 'create'])->name('bags.create');
        Route::post('/bags', [BagController::class, 'store'])->name('bags.store');
        Route::get('/bags/{bag}/edit', [BagController::class, 'edit'])->name('bags.edit');
        Route::put('/bags/{bag}', [BagController::class, 'update'])->name('bags.update');
        Route::delete('/bags/{bag}', [BagController::class, 'destroy'])->name('bags.destroy');
Route::post('bags/import', [BagController::class, 'import'])->name('bags.import');
    });

    // User routes with email verification
    Route::middleware(['custom.verify'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // Other user routes...
    });
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // ... other routes ...
        Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Reviews Routes
    Route::controller(App\Http\Controllers\Admin\ReviewController::class)->group(function () {
        Route::get('reviews', 'index')->name('reviews.index');
        Route::get('reviews/stats', 'getStats')->name('reviews.stats');
        Route::delete('reviews/{review}', 'destroy')->name('reviews.destroy');
    });


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Orders Routes
    Route::controller(App\Http\Controllers\Admin\OrderController::class)->group(function () {
        Route::get('orders', 'index')->name('orders.index');
        Route::get('orders/{order}', 'show')->name('orders.show');
        Route::patch('orders/{order}/status', 'updateStatus')->name('orders.update-status');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    });
});


});