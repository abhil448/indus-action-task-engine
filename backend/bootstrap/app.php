<?php

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
        // Apply ForceJsonResponse middleware to all API routes
        // $middleware->api(prepend: [
        //     ForceJsonResponse::class,
        // ]);
    })
   ->withExceptions(function (Exceptions $exceptions) {
        // Intercept all API exceptions and render uniform JSON
        // $exceptions->render(function (Throwable $e, Request $request) {
        //     if ($request->is('api/*') || $request->wantsJson()) {
                
        //         // Get appropriate HTTP status code
        //         $statusCode = method_exists($e, 'getStatusCode') 
        //             ? $e->getStatusCode() 
        //             : ($e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500);

        //         return response()->json([
        //             'success' => false,
        //             'message' => $e->getMessage() ?: 'Server Error',
        //             'error_code' => class_basename($e),
        //             // Show detailed exception traces only when APP_DEBUG is true
        //             'debug' => config('app.debug') ? [
        //                 'exception' => get_class($e),
        //                 'file' => $e->getFile(),
        //                 'line' => $e->getLine(),
        //                 'trace' => collect($e->getTrace())->take(5),
        //             ] : null,
        //         ], $statusCode);
        //     }
        // });
    })->create();   
