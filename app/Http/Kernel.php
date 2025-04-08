<?php
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'verified' => \App\Http\Middleware\CustomVerifyEmail::class,
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];

protected $middlewareGroups = [
    'web' => [
        // ... other middlewares
        \App\Http\Middleware\Authenticate::class,
    ],
];