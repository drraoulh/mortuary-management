<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'  => \App\Http\Middleware\RoleMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'staff' => \App\Http\Middleware\StaffMiddleware::class,
        ]);

        // An expired session must still be able to log out instead of hitting "419 Page Expired".
        $middleware->validateCsrfTokens(except: ['logout']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
