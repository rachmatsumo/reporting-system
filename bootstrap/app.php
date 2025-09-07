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
    ->withProviders([ 
        // 
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
   ->withExceptions(function ($exceptions): void {
       $exceptions->render(function (\Illuminate\Http\Request $request, $exception) {
        // 404
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return response()->view('errors.404', [], 404);
        }

        // 403
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
            return response()->view('errors.403', [], 403);
        }

        // 401
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
            return response()->view('errors.401', [], 401);
        }

        // 419
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return response()->view('errors.419', [], 419);
        }

        // Server error 500 (exception lain)
        return response()->view('errors.500', ['exception' => $exception], 500);
    });

    })->create();
