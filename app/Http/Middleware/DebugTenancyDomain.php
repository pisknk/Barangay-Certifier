<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DebugTenancyDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log detailed information about the request
        $host = $request->getHost();
        $domain = $request->getHttpHost();
        $fullUrl = $request->fullUrl();
        
        Log::info('Tenancy Domain Debug', [
            'host' => $host,
            'domain' => $domain,
            'full_url' => $fullUrl,
            'request_headers' => $request->headers->all(),
        ]);
        
        return $next($request);
    }
} 