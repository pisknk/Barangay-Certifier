<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantAdminMiddleware
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
        // Check if the user is authenticated as a tenant user
        if (!Auth::guard('tenant')->check()) {
            return redirect()->route('tenant.login');
        }

        // Check if the authenticated user is an admin
        if (!Auth::guard('tenant')->user()->isAdmin()) {
            return redirect()->route('tenant.certificates.index')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
} 