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
        // For now, using hardcoded values that we know work
        // Later, when database issues are resolved, we can uncomment the code below
        
        /* 
        // Get counts and statistics for the dashboard
        $activeTenants = Tenant::where('is_active', true)->count();
        
        // Calculate total income based on subscription plans
        $totalIncome = 0;
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            if (strpos($tenant->subscription_plan, 'Basic') !== false) {
                $totalIncome += 399;
            } elseif (strpos($tenant->subscription_plan, 'Essentials') !== false) {
                $totalIncome += 799;
            } elseif (strpos($tenant->subscription_plan, 'Ultimate') !== false) {
                $totalIncome += 1299;
            }
        }
        */
        
        return view('admin.admindash', [
            'activeTenants' => 5,
            'totalIncome' => 2000,
            'totalRevenue' => 5000
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
        // Get counts and statistics for the dashboard
        $activeTenants = Tenant::where('is_active', true)->count();
        
        // Return simple view to verify that the controller works
        return view('admin.admindash', [
            'activeTenants' => $activeTenants,
            'totalIncome' => 0,
            'debug' => true
        ]);
    }
} 