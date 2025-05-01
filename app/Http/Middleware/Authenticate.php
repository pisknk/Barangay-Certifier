<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // If request is to admin-panel subdomain, always use the absolute URL
            if (str_contains($request->getHost(), 'admin-panel')) {
                return 'http://admin-panel.localhost:8000/login';
            }
            
            return route('login');
        }
        
        return null;
    }
} 