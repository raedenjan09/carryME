protected $routeMiddleware = [
    // ...existing middleware...
    'auth' => \App\Http\Middleware\Authenticate::class,
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    // ...existing middleware...
];