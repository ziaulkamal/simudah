<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HideHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next): Response
    {
        $response = $next($request);

        $headersToRemove = [
            'X-Powered-By',
            'Server',
            'X-Laravel',
            'X-PHP-Version',
        ];

        foreach ($headersToRemove as $header) {
            $response->headers->remove($header);
        }

        return $response;
    }
}
