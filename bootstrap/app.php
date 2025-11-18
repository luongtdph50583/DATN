<?php

  use Illuminate\Foundation\Application;
  use Illuminate\Foundation\Configuration\Exceptions;
  use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/web.php',     // admin hoặc route hiện có
            __DIR__ . '/../routes/client.php',  // thêm route client ở đây
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Đăng ký middleware cho route
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'club_manager' => \App\Http\Middleware\CheckClubManager::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
