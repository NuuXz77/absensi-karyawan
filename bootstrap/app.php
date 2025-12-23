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
            'role' => \App\Http\Middleware\RoleCheck::class,
            'role.url' => \App\Http\Middleware\RoleBasedUrlRedirect::class,
        ]);
        
        // Tambahkan middleware global untuk auto-redirect berdasarkan role
        $middleware->web(append: [
            \App\Http\Middleware\RoleBasedUrlRedirect::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
