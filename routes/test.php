<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;

Route::get('/test-middleware', function() {
    return 'Middleware test route';
})->middleware('admin');
