<?php

use App\Http\Middleware\EnsureFrontendUser;
use App\Http\Middleware\CanonicalUrl;
use App\Http\Middleware\MinifyHtml;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prependToGroup('web', CanonicalUrl::class);
        $middleware->appendToGroup('web', MaintenanceMode::class);
        $middleware->appendToGroup('web', MinifyHtml::class);
        $middleware->appendToGroup('web', SecurityHeaders::class);
        $middleware->alias([
            'frontend.user' => EnsureFrontendUser::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/payment/webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
