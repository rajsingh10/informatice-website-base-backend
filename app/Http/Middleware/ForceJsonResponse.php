<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force all API and Admin/Public alias routes to always return JSON.
 * This prevents Laravel from returning HTML redirect or error pages
 * when the client does not send Accept: application/json in Postman or cURL.
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin/*', 'admin', 'public/*', 'public', 'api/*', 'api')) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
