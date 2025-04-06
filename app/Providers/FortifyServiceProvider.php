<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Fortify::authenticateUsing(function ($request) {
            // Custom authentication logic
        });

        // Redirect to User Dashboard after login
        Fortify::redirects('login', '/user/dashboard');
    }
}