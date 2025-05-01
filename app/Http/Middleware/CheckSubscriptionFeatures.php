<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Facades\Tenancy;

class CheckSubscriptionFeatures
{
    // Define feature availability by subscription plan
    protected $planFeatures = [
        'Basic P399' => [
            'custom_page_headers' => true,
            'custom_theme' => false,
            'custom_paper_size' => false,
            'software_updates' => false,
        ],
        'Essentials P799' => [
            'custom_page_headers' => true,
            'custom_theme' => false,
            'custom_paper_size' => true,
            'software_updates' => false,
        ],
        'Ultimate P1299' => [
            'custom_page_headers' => true,
            'custom_theme' => true,
            'custom_paper_size' => true,
            'software_updates' => true,
        ],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature = null): Response
    {
        // Get current tenant
        $tenant = tenant();
        
        if (!$tenant) {
            Log::error('No tenant found in CheckSubscriptionFeatures middleware');
            return redirect()->route('tenant.dashboard')->with('error', 'Subscription verification error.');
        }
        
        // If no specific feature is required, continue
        if (!$feature) {
            return $next($request);
        }
        
        $tenantPlan = $tenant->subscription_plan ?? 'Basic P399';
        
        // Determine the plan by checking for partial case-insensitive matches
        $matchedPlan = 'Basic P399'; // Default to Basic if no match
        foreach ($this->planFeatures as $plan => $features) {
            // Check if tenant plan contains the plan name (case insensitive)
            if (stripos($tenantPlan, explode(' ', $plan)[0]) !== false) {
                $matchedPlan = $plan;
                Log::info("Matched tenant plan '{$tenantPlan}' to feature plan '{$plan}'");
                break;
            }
        }
        
        // Log the plan matching for debugging
        Log::info("Tenant plan check: Actual plan='{$tenantPlan}', Matched to='{$matchedPlan}', Feature='{$feature}'");
        
        // Check if the feature is available for this plan
        if (isset($this->planFeatures[$matchedPlan][$feature]) && $this->planFeatures[$matchedPlan][$feature]) {
            return $next($request);
        }
        
        // If AJAX request, return JSON error
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This feature is not available on your current subscription plan.'
            ], 403);
        }
        
        // Otherwise redirect with error message
        return redirect()->route('tenant.settings.index')->with('error', 
            'The requested feature is not available on your current ' . $tenantPlan . ' plan. Please upgrade to access this feature.');
    }
} 