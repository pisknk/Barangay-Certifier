<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // We'll handle authentication at the route level instead
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        // First, log all tenant statuses to debug the issue
        $allTenantsForDebug = Tenant::select('id', 'name', 'is_active', 'subscription_plan')->get();
        foreach ($allTenantsForDebug as $tenant) {
            Log::info("Tenant {$tenant->id} ({$tenant->name}) has is_active status: {$tenant->is_active} and plan: {$tenant->subscription_plan}");
        }
        
        // Get counts of tenants with is_active EXACTLY equal to 1 (ACTIVE/SUBSCRIBED)
        // Try both integer and string comparison to catch potential type issues
        $activeTenantsCount = DB::table('tenants')
            ->where(function($query) {
                $query->where('is_active', '=', 1)
                      ->orWhere('is_active', '=', '1');
            })
            ->count();
        
        Log::info("Active tenants count (is_active=1 or '1'): {$activeTenantsCount}");
        
        // Calculate total income based on subscription plans of active tenants
        $totalIncome = 0;
        $totalRevenue = 0;
        
        // For better performance, fetch only active tenants for income calculation
        // Using a subquery to ensure we get all active tenants regardless of type
        $activeTenantsData = DB::table('tenants')
            ->where(function($query) {
                $query->where('is_active', '=', 1)
                      ->orWhere('is_active', '=', '1');
            })
            ->get();
        
        $allTenants = DB::table('tenants')->get();
        
        // Calculate income from active tenants only (is_active = 1)
        foreach ($activeTenantsData as $tenant) {
            if (strpos($tenant->subscription_plan, 'Basic') !== false) {
                $totalIncome += 399;
                Log::info("Adding 399 to income for active tenant {$tenant->id} with Basic plan");
            } elseif (strpos($tenant->subscription_plan, 'Essentials') !== false) {
                $totalIncome += 799;
                Log::info("Adding 799 to income for active tenant {$tenant->id} with Essentials plan");
            } elseif (strpos($tenant->subscription_plan, 'Ultimate') !== false) {
                $totalIncome += 1299;
                Log::info("Adding 1299 to income for active tenant {$tenant->id} with Ultimate plan");
            }
        }
        
        // Calculate total revenue from all tenants regardless of status
        foreach ($allTenants as $tenant) {
            if (strpos($tenant->subscription_plan, 'Basic') !== false) {
                $totalRevenue += 399;
            } elseif (strpos($tenant->subscription_plan, 'Essentials') !== false) {
                $totalRevenue += 799;
            } elseif (strpos($tenant->subscription_plan, 'Ultimate') !== false) {
                $totalRevenue += 1299;
            }
        }
        
        // Log final calculations for debugging
        Log::info("Dashboard stats: Active tenants: {$activeTenantsCount}, Current income: {$totalIncome}, Total revenue: {$totalRevenue}");
        
        return view('admin.admindash', [
            'activeTenants' => $activeTenantsCount,
            'totalIncome' => $totalIncome,
            'totalRevenue' => $totalRevenue
        ]);
    }

    /**
     * Show the list of tenants.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function tenants()
    {
        // Get tenants with their associated domains using a join
        $tenants = Tenant::select('tenants.*')
            ->leftJoin('domains', 'domains.tenant_id', '=', 'tenants.id')
            ->addSelect(DB::raw('domains.domain as domain_name'))
            ->get();

        return view('admin.tenantlist', compact('tenants'));
    }

    /**
     * Show domains status.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function domains()
    {
        // Get all tenants
        $tenants = Tenant::all();
        
        // Get all domains with tenant information
        $domains = Domain::with('tenant')->get()->map(function ($domain) {
            $tenant = $domain->tenant;
            return [
                'id' => $tenant ? $tenant->id : 'N/A',
                'domain' => $domain->domain,
                'tenant_name' => $tenant ? $tenant->name : 'N/A',
                'subscription_plan' => $tenant ? $tenant->subscription_plan : 'N/A',
                'is_active' => $tenant ? $tenant->is_active : false,
                'valid_until' => $tenant ? $tenant->valid_until : null,
                'status' => $tenant && $tenant->is_active ? 'active' : 'inactive'
            ];
        });
        
        return view('admin.domainstats', compact('domains'));
    }

    /**
     * Show the form for editing the specified tenant.
     *
     * @param  string  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id)
    {
        // Query directly from the DB to ensure up-to-date data
        $rawTenant = DB::table('tenants')->where('id', $id)->first();
        
        if (!$rawTenant) {
            abort(404, 'Tenant not found');
        }
        
        // Get the Eloquent model
        $tenant = Tenant::findOrFail($id);
        
        // Ensure the is_active property is properly cast for the view
        $tenant->is_active = (int)$rawTenant->is_active;
        
        return view('admin.modifytenant', compact('tenant'));
    }

    /**
     * Update the specified tenant in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subscription_plan' => 'nullable|string|in:Basic P399,Essentials P799,Ultimate P1299',
        ]);

        $tenant = Tenant::findOrFail($id);
        $tenant->name = $request->name;
        $tenant->email = $request->email;
        
        // Handle subscription plan update if provided
        if ($request->has('subscription_plan')) {
            $tenant->subscription_plan = $request->subscription_plan;
            
            // Update valid_until based on the plan
            $now = now();
            if ($request->subscription_plan == 'Basic P399') {
                $tenant->valid_until = $now->addMonth();
            } elseif ($request->subscription_plan == 'Essentials P799') {
                $tenant->valid_until = $now->addMonths(6);
            } elseif ($request->subscription_plan == 'Ultimate P1299') {
                $tenant->valid_until = $now->addYear();
            }
        }
        
        $tenant->save();

        return redirect()->route('admin.tenants')->with('success', 'Tenant updated successfully');
    }

    /**
     * Toggle the tenant's active status.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        // Use App\Models\Tenant explicitly to avoid namespace conflicts
        $tenant = \App\Models\Tenant::findOrFail($id);
        
        // Check current status and toggle
        if ($tenant->is_active == 1) {
            // Deactivate the tenant (admin deactivation = 2)
            $tenant->deactivate();
            $status = 'deactivated';
        } else {
            // Capture the previous status before activation
            $previousStatus = $tenant->is_active;
            
            // Activate the tenant (active = 1)
            $tenant->activate();
            
            // Only update valid_until if previous status was INACTIVE (0) or EXPIRED (3)
            if ($previousStatus == \App\Models\Tenant::INACTIVE || $previousStatus == \App\Models\Tenant::EXPIRED) {
                // Extend subscription based on plan
                $now = now();
                if (str_contains($tenant->subscription_plan, 'Basic')) {
                    $tenant->valid_until = $now->addMonth();
                } elseif (str_contains($tenant->subscription_plan, 'Essentials')) {
                    $tenant->valid_until = $now->addMonths(6);
                } elseif (str_contains($tenant->subscription_plan, 'Ultimate')) {
                    $tenant->valid_until = $now->addYear();
                } else {
                    // Default to 1 month for unknown plans
                    $tenant->valid_until = $now->addMonth();
                }
                
                // Save the valid_until field
                $tenant->save();
                
                Log::info("Extended subscription for tenant {$id} until {$tenant->valid_until}");
            }
            
            $status = 'activated';
        }

        return redirect()->back()->with('success', "Tenant {$status} successfully");
    }

    /**
     * Update the tenant's subscription plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePlan(Request $request, $id)
    {
        $request->validate([
            'subscription_plan' => 'required|string|in:Basic P399,Essentials P799,Ultimate P1299',
        ]);

        $tenant = Tenant::findOrFail($id);
        $tenant->subscription_plan = $request->subscription_plan;
        
        // Update valid_until based on the plan
        $now = now();
        if ($request->subscription_plan == 'Basic P399') {
            $tenant->valid_until = $now->addMonth();
        } elseif ($request->subscription_plan == 'Essentials P799') {
            $tenant->valid_until = $now->addMonths(6);
        } elseif ($request->subscription_plan == 'Ultimate P1299') {
            $tenant->valid_until = $now->addYear();
        }
        
        $tenant->save();

        return redirect()->back()->with('success', 'Subscription plan updated successfully');
    }

    /**
     * Remove the specified tenant from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenantName = $tenant->name;
        
        // Delete the tenant
        $tenant->delete();

        return redirect()->route('admin.tenants')->with('success', "Tenant '{$tenantName}' has been removed");
    }

    /**
     * Debug method - accessible without authentication
     * 
     * @return \Illuminate\Http\Response
     */
    public function debug()
    {
        // Get counts of tenants with is_active EXACTLY equal to 1 (ACTIVE/SUBSCRIBED)
        // Try both integer and string comparison to catch potential type issues
        $activeTenantsCount = DB::table('tenants')
            ->where(function($query) {
                $query->where('is_active', '=', 1)
                      ->orWhere('is_active', '=', '1');
            })
            ->count();
        
        // Calculate income and revenue using the same logic as the dashboard method
        $totalIncome = 0;
        $totalRevenue = 0;
        
        // Fetch only active tenants for income calculation
        $activeTenantsData = DB::table('tenants')
            ->where(function($query) {
                $query->where('is_active', '=', 1)
                      ->orWhere('is_active', '=', '1');
            })
            ->get();
            
        $allTenants = DB::table('tenants')->get();
        
        // Calculate income from active tenants only
        foreach ($activeTenantsData as $tenant) {
            if (strpos($tenant->subscription_plan, 'Basic') !== false) {
                $totalIncome += 399;
            } elseif (strpos($tenant->subscription_plan, 'Essentials') !== false) {
                $totalIncome += 799;
            } elseif (strpos($tenant->subscription_plan, 'Ultimate') !== false) {
                $totalIncome += 1299;
            }
        }
        
        // Calculate total revenue from all tenants
        foreach ($allTenants as $tenant) {
            if (strpos($tenant->subscription_plan, 'Basic') !== false) {
                $totalRevenue += 399;
            } elseif (strpos($tenant->subscription_plan, 'Essentials') !== false) {
                $totalRevenue += 799;
            } elseif (strpos($tenant->subscription_plan, 'Ultimate') !== false) {
                $totalRevenue += 1299;
            }
        }
        
        // Log for debugging
        Log::info("DEBUG mode - Dashboard stats: Active tenants: {$activeTenantsCount}, Current income: {$totalIncome}, Total revenue: {$totalRevenue}");
        
        // Return simple view to verify that the controller works
        return view('admin.admindash', [
            'activeTenants' => $activeTenantsCount,
            'totalIncome' => $totalIncome,
            'totalRevenue' => $totalRevenue,
            'debug' => true
        ]);
    }

    /**
     * Debug tenant status information in JSON format
     * 
     * @return \Illuminate\Http\Response
     */
    public function tenantDebug()
    {
        // Query all tenants with their status information
        $tenants = Tenant::select('id', 'name', 'barangay', 'subscription_plan', 'is_active', 'valid_until')->get();
        
        $formattedTenants = $tenants->map(function($tenant) {
            // Get status text based on is_active value
            $statusText = '';
            if ($tenant->is_active === 0 || $tenant->is_active === '0') {
                $statusText = 'NOT ACTIVE';
            } elseif ($tenant->is_active === 1 || $tenant->is_active === '1') {
                $statusText = 'SUBSCRIBED';
            } elseif ($tenant->is_active === 2 || $tenant->is_active === '2') {
                $statusText = 'DISABLED BY ADMIN';
            } elseif ($tenant->is_active === 3 || $tenant->is_active === '3') {
                $statusText = 'EXPIRED SUBSCRIPTION';
            } else {
                $statusText = 'UNKNOWN STATUS: ' . $tenant->is_active;
            }
            
            // Format for display
            return [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'barangay' => $tenant->barangay,
                'plan' => $tenant->subscription_plan,
                'is_active_raw' => $tenant->is_active,
                'is_active_type' => gettype($tenant->is_active),
                'is_active_equals_1' => $tenant->is_active == 1,
                'is_active_strict_equals_1' => $tenant->is_active === 1,
                'status_text' => $statusText,
                'valid_until' => $tenant->valid_until ? $tenant->valid_until->format('Y-m-d') : null
            ];
        });
        
        return response()->json([
            'tenants' => $formattedTenants,
            'counts' => [
                'total' => $tenants->count(),
                'active_loose' => $tenants->where('is_active', 1)->count(),
                'active_strict' => $tenants->filter(function($t) { return $t->is_active === 1; })->count(),
                'db_query_count' => DB::table('tenants')->where('is_active', '=', 1)->count()
            ]
        ]);
    }
} 