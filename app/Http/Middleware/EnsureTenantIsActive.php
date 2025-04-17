<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $isActive = false;
        $tenantId = $tenant ? $tenant->id : null;
        
        if ($tenant) {
            // Check tenant model property
            $isActiveModel = $tenant->is_active;
            
            // Double-check via direct DB query to be sure - use central connection explicitly
            $centralConnection = config('tenancy.database.central_connection', 'mysql');
            $dbTenant = DB::connection($centralConnection)
                        ->table('tenants')
                        ->where('id', $tenant->id)
                        ->first();
            $isActiveDb = $dbTenant && $dbTenant->is_active;
            
            // Combine results - active if either says it's active
            $isActive = $isActiveModel || $isActiveDb;
            
            // Log any discrepancies for debugging
            if ($isActiveModel !== $isActiveDb) {
                Log::warning("Tenant {$tenant->id} has different active status values: Model={$isActiveModel}, DB={$isActiveDb}");
            }
        }
        
        // Check if tenant exists and is active
        if (!$tenant || !$isActive) {
            // If this is an API request, return JSON
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Tenant account is inactive',
                    'message' => 'This tenant account has been deactivated. Please contact the administrator for assistance.'
                ], 403);
            }
            
            // For web requests, show the nopay view
            $domain = $request->getHttpHost();
            
            return response()->view('tenant.nopay', [
                'domain' => $domain,
                'tenantId' => $tenantId ?? 'Unknown'
            ], 403);
        }
        
        return $next($request);
    }
} 