<?php

use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\EnsureAccountActive;
use App\Http\Middleware\HandleImpersonation;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsReferent;
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
            'admin' => IsAdmin::class,
            'referent' => IsReferent::class,
            'account.active' => EnsureAccountActive::class,
        ]);
        // CheckMaintenanceMode runs first so it reads the REAL identity (maintenance
        // stays transparent for a superadmin). HandleImpersonation then applies the
        // effective-role override for downstream route middleware/policies.
        $middleware->web(append: [CheckMaintenanceMode::class, HandleImpersonation::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
