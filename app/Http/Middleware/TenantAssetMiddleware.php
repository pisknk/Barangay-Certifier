<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class TenantAssetMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Force scheme (http/https) to match the main app URL
        URL::forceScheme(parse_url(config('app.url'), PHP_URL_SCHEME) ?: 'http');
        
        // Store the current URL as the intended URL to return to after login
        if (!$request->is('login') && !$request->is('api/*') && $request->isMethod('get')) {
            session()->put('url.intended', url()->current());
        }
        
        return $next($request);
    }
} 