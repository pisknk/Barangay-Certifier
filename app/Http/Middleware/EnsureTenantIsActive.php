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
        $isExpired = false;
        $tenantId = $tenant ? $tenant->id : null;
        
        if ($tenant) {
            // Check tenant model property
            $isActiveModel = $tenant->is_active;
            
            // Check for subscription expiration
            if ($tenant->valid_until) {
                $isExpired = now()->greaterThan($tenant->valid_until);
                
                // Log if subscription is expired
                if ($isExpired) {
                    Log::info("Tenant {$tenant->id} subscription expired on {$tenant->valid_until}");
                    
                    // If tenant is not already marked as expired, mark it now
                    if ($isActiveModel !== \App\Models\Tenant::EXPIRED) {
                        $tenant->markAsExpired(); // This preserves the valid_until date for future reactivation
                        Log::info("Tenant {$tenant->id} marked as expired during middleware check");
                        $isActiveModel = \App\Models\Tenant::EXPIRED;
                    }
                }
            }
            
            // Double-check via direct DB query to be sure - use central connection explicitly
            try {
                $centralConnection = config('tenancy.database.central_connection', 'mysql');
                $dbTenant = DB::connection($centralConnection)
                            ->table('tenants')
                            ->where('id', $tenant->id)
                            ->first();
                $isActiveDb = $dbTenant && $dbTenant->is_active;
                
                // Log any discrepancies for debugging
                if ($isActiveModel !== $isActiveDb) {
                    Log::warning("Tenant {$tenant->id} has different active status values: Model={$isActiveModel}, DB={$isActiveDb}");
                }
            } catch (\Exception $e) {
                // If we can't connect to the central database, log the error and continue
                // using only the model's status
                Log::error("Failed to check tenant status in central DB: " . $e->getMessage());
                $isActiveDb = $isActiveModel;
            }
            
            // Combine results - active if status is ACTIVE (1) AND not expired
            $isActive = ($isActiveModel == \App\Models\Tenant::ACTIVE) && !$isExpired;
        }
        
        // Check if tenant exists and is active
        if (!$tenant || !$isActive) {
            // Determine reason for denial
            $reason = 'Account deactivated';
            if (!$tenant) {
                $reason = 'Tenant not found';
            } elseif ($isExpired || $isActiveModel == \App\Models\Tenant::EXPIRED) {
                $reason = 'Subscription expired';
            }
            
            // If this is an API request, return JSON
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Tenant account is unavailable',
                    'reason' => $reason,
                    'message' => 'This tenant account is currently unavailable. Please contact the administrator for assistance.'
                ], 403);
            }
            
            // For web requests, show the appropriate view
            $domain = $request->getHttpHost();
            $viewData = [
                'domain' => $domain,
                'tenantId' => $tenantId ?? 'Unknown',
                'reason' => $reason
            ];
            
            if ($tenant && $tenant->valid_until) {
                $viewData['expirationDate'] = $tenant->valid_until->format('F j, Y');
            }
            
            // Choose view based on reason
            $view = $isExpired ? 'tenant.expired' : 'tenant.nopay';
            
            return response()->view($view, $viewData, 403);
        }
        
        return $next($request);
    }
} 