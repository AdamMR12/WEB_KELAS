<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Modul Kas Kelas (prefix /kas)
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/kas.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
            'level' => \App\Http\Middleware\CheckLevel::class,
            'kas'   => \App\Http\Middleware\KasAuth::class,
        ]);

        // User yang sudah login diarahkan ke dashboard-nya, bukan '/'
        $middleware->redirectUsersTo(fn () => route(auth()->user()->dashboardRoute()));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
