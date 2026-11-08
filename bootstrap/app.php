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

        // Agar Laravel mengenali HTTPS dari Vercel
        $middleware->trustProxies(at: '*');

        // Konfigurasi CSRF yang sudah ada
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
