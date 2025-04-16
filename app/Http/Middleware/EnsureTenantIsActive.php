<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the current tenant
        $tenant = tenant();
        
        // Check if tenant exists and is active
        if (!$tenant || !$tenant->is_active) {
            // If the tenant is not active, return a 403 forbidden response
            return response()->json([
                'error' => 'Tenant account is inactive',
                'message' => 'This tenant account has been deactivated. Please contact the administrator for assistance.'
            ], 403);
        }
        
        return $next($request);
    }
} 