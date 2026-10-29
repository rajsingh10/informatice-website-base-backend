<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Prepend ForceJsonResponse globally so admin/*, public/*, and api/* always treat requests as expecting JSON
        $middleware->prepend(\App\Http\Middleware\ForceJsonResponse::class);

        // Exclude stateless API / direct alias routes from CSRF
        $middleware->validateCsrfTokens(except: [
            'admin/*',
            'public/*',
            'api/*',
        ]);

        // Force JSON response for all /api/* routes
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Always return JSON responses for unauthenticated requests on api/admin/public routes
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('admin/*', 'public/*', 'api/*')) {
                if ($response->getStatusCode() === 401 || $e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated or invalid token. Please log in first.',
                    ], 401);
                }
            }
            return $response;
        });
    })->create();
