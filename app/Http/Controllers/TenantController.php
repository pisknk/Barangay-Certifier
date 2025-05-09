<?php

// app/Http/Controllers/TenantController.php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Mail\WelcomeMail;
use App\Mail\ApprovedMail;
use Stancl\Tenancy\Database\TenantCollection;
use Stancl\Tenancy\TenantManager;
use Illuminate\Support\Facades\DB;
use App\Services\TurnstileService;

class TenantController extends Controller
{
    public function index()
    {
        try {
            // Get fresh data from DB to ensure we have the latest values
            $tenants = DB::table('tenants')->get();
            
            // Log for debugging
            Log::info("Fetched " . count($tenants) . " tenants. Sample is_active values: " . 
                collect($tenants)->take(3)->map(function($t) { 
                    return "{$t->id}: " . ($t->is_active ? 'true' : 'false'); 
                })->join(', '));
            
            return response()->json($tenants, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching tenants: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch tenants', 'details' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            // Get fresh data directly from DB
            $tenant = DB::table('tenants')->where('id', $id)->first();
            
            if (!$tenant) {
                return response()->json(['error' => 'Tenant not found'], 404);
            }
            
            // Log for debugging
            Log::info("Fetched tenant {$id}, is_active value: " . ($tenant->is_active ? 'true' : 'false'));
            
            return response()->json($tenant, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching tenant ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Tenant not found', 'details' => $e->getMessage()], 404);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Validate Turnstile first
            $turnstileService = app(\App\Services\TurnstileService::class);
            $token = $request->input('cf-turnstile-response');
            
            if (empty($token) || !$turnstileService->validate($token)) {
                DB::rollBack();
                Log::warning("Captcha validation failed during signup");
                
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Captcha validation failed. Please try again.'], 422);
                }
                
                return redirect()->back()->with('error', 'Captcha validation failed. Please try again.');
            }
            
            // Create a slugified version of the barangay name
            $barangaySlug = Str::slug($request->barangay);
            $tenantId = $barangaySlug;
            
            // Check if the tenant already exists before validation
            if (Tenant::where('id', $tenantId)->exists()) {
                DB::rollBack();
                Log::warning("Duplicate barangay registration attempt: {$request->barangay}");
                
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'This barangay name is already taken'], 422);
                }
                
                // Use with() to flash the error to the session
                return redirect()->back()->with('error', 'This barangay name is already taken.');
            }
            
            $request->validate([
                'name' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'email' => 'required|email|unique:tenants,email',
                'subscription_plan' => 'required|in:Basic,Essentials,Ultimate',
                'cf-turnstile-response' => 'required|string',
            ]);
            
            // Domain should include the full domain with port
            $domain = $barangaySlug . '.localhost:8000';

            // Insert directly into database to bypass model events initially
            DB::table('tenants')->insert([
                'id' => $tenantId,
                'name' => $request->name,
                'barangay' => $request->barangay,
                'email' => $request->email,
                'subscription_plan' => $request->subscription_plan,
                'password' => null, // No password set initially - will be set during setup process
                'password_changed' => false, // Not changed yet
                'is_active' => false,
                'data' => '{}',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Now retrieve the tenant from DB
            $tenant = Tenant::find($tenantId);
            
            // Create domain for the tenant
            $tenant->domains()->create(['domain' => $domain]);

            // Get domain URL
            $domainUrl = 'http://' . $domain;
            
            // Send welcome email
            try {
                Mail::to($tenant->email)->send(new WelcomeMail($tenant));
                Log::info('Welcome email sent to: ' . $tenant->email);
            } catch (\Exception $emailEx) {
                Log::error('Error sending welcome email: ' . $emailEx->getMessage());
                // We'll continue even if email fails - don't rollback transaction
            }
            
            DB::commit();

            if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tenant created successfully. Awaiting activation.',
                'tenant' => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'barangay' => $tenant->barangay,
                    'email' => $tenant->email,
                    'subscription_plan' => $tenant->subscription_plan,
                    'is_active' => $tenant->is_active,
                    'domain' => $domain,
                    'domain_url' => $domainUrl
                ]
            ], 201);
            }
            
            return redirect()->route('thanks');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating tenant: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
            return response()->json(['error' => 'Failed to create tenant', 'details' => $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to create tenant: ' . $e->getMessage());
        }
    }

    public function deactivate($id)
    {
        try {
            // Use App\Models\Tenant explicitly to avoid namespace conflicts
            $tenant = \App\Models\Tenant::findOrFail($id);
            
            // Log before deactivation
            Log::info("Attempting to deactivate tenant {$id}. Current status: " . $tenant->is_active);
            
            // Use the deactivate method that sets is_active to DEACTIVATED (2)
            $tenant->deactivate();
            
            // Force refresh from database to get the latest values
            DB::table('tenants')
                ->where('id', $id)
                ->update([
                    'is_active' => \App\Models\Tenant::DEACTIVATED
                ]);
                
            // Verify deactivation by querying the database directly
            $checkTenant = DB::table('tenants')->where('id', $id)->first();
            $isDeactivated = $checkTenant && $checkTenant->is_active == \App\Models\Tenant::DEACTIVATED;
            
            Log::info("Tenant {$id} deactivated through API. " . 
                "New is_active status in DB: " . ($checkTenant ? $checkTenant->is_active : 'unknown') . ". " .
                "Successfully deactivated: " . ($isDeactivated ? 'Yes' : 'No'));
            
            if (!$isDeactivated) {
                // If not deactivated, force it directly
                Log::warning("Tenant {$id} not properly deactivated. Forcing direct DB update.");
                DB::table('tenants')
                    ->where('id', $id)
                    ->update([
                        'is_active' => \App\Models\Tenant::DEACTIVATED,
                        'updated_at' => now()
                    ]);
            }

            return response()->json(['message' => 'Tenant deactivated successfully']);
        } catch (\Exception $e) {
            Log::error('Error deactivating tenant ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Failed to deactivate tenant', 'details' => $e->getMessage()], 500);
        }
    }

    public function activate($id)
    {
        try {
            // Use App\Models\Tenant explicitly to avoid namespace conflicts
            $tenant = \App\Models\Tenant::findOrFail($id);
            
            // Store current status before changing
            $previousStatus = $tenant->is_active;
            
            // Generate a setup token instead of a random password
            $setupToken = Str::random(64);
            
            // Store the setup token
            $tenant->setup_token = $setupToken;
            $tenant->password_changed = false; // Explicitly set to false
            
            // Save the token to the tenant record in database
            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update([
                    'setup_token' => $setupToken,
                    'password_changed' => false
                ]);
            
            // Check if this was an expired subscription or inactive (new) tenant
            $wasExpired = $tenant->is_active == \App\Models\Tenant::EXPIRED;
            $wasInactive = $tenant->is_active == \App\Models\Tenant::INACTIVE;
            
            // Only set valid_until date if tenant was inactive (0) or expired (3)
            // Do not set it if tenant was deactivated by admin (2)
            if ($wasInactive || $wasExpired || $tenant->isExpired()) {
                Log::info("Setting expiration date for tenant {$id} with previous status: {$previousStatus}, plan: {$tenant->subscription_plan}");
                
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
                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update([
                        'valid_until' => $tenant->valid_until,
                    ]);
                
                Log::info("Extended subscription for tenant {$id} until {$tenant->valid_until}");
            } else {
                Log::info("Preserving existing expiration date for tenant {$id} (previous status: {$previousStatus})");
            }
            
            // Use the activate method which will set is_active to ACTIVE (1)
            $tenant->activate();
            
            Log::info("Tenant {$id} activated through API. Status: " . $tenant->is_active . " with valid_until: " . $tenant->valid_until);

            try {
                // Get the domain
                $domain = $tenant->domains()->first();
                $domainUrl = $domain ? 'http://' . $domain->domain : null;
                
                // Get database connection details
                $dbConnection = config('database.default');
                $dbHost = config('database.connections.' . $dbConnection . '.host');
                $dbUsername = config('database.connections.' . $dbConnection . '.username');
                $dbPassword = config('database.connections.' . $dbConnection . '.password');
                
                // Use existing database name if available, otherwise create a new one
                $dbName = $tenant->tenant_db;
                
                if (empty($dbName)) {
                    // Database name with prefix
                    $dbName = 'tenant_' . $tenant->id;
                    
                    // Update the tenant record with the database name
                    $tenant->setDatabaseName($dbName);
                    
                    // Also update directly to be sure
                    DB::table('tenants')->where('id', $tenant->id)->update([
                        'tenant_db' => $dbName
                    ]);
                }
                
                // Create database if it doesn't exist
                $createDbSQL = "CREATE DATABASE IF NOT EXISTS `{$dbName}`";
                DB::statement($createDbSQL);
                
                Log::info("Using database for tenant {$tenant->id}: {$dbName}");
                
                // Initialize the tenant database and run migrations
                tenancy()->initialize($tenant);
                
                // Run tenant-specific migrations
                $outputMigrate = \Illuminate\Support\Facades\Artisan::call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);
                
                Log::info("Tenant-specific migration output for {$tenant->id}: " . \Illuminate\Support\Facades\Artisan::output());
                
                // Run all migrations for tenant using the tenants:migrate command
                $output = \Illuminate\Support\Facades\Artisan::call('tenants:migrate', [
                    '--tenants' => [$tenant->id],
                    '--force' => true
                ]);
                
                Log::info("Global migration output for tenant {$tenant->id}: " . \Illuminate\Support\Facades\Artisan::output());
                
                // Create admin user in tenant database without setting a password yet
                // The password will be set by the user through the setup link
                \Illuminate\Support\Facades\Artisan::call('tenant:create-admin', [
                    'tenant_id' => $tenant->id
                ]);
                
                Log::info("Admin user creation output for {$tenant->id}: " . \Illuminate\Support\Facades\Artisan::output());
                
                // End the tenant context
                tenancy()->end();
                
                // Send approval email with setup link
                try {
                    Log::info('Attempting to send approval email to: ' . $tenant->email . ' with domain: ' . $domainUrl);
                    Mail::to($tenant->email)->send(new ApprovedMail($tenant, null, $domainUrl, $setupToken));
                    Log::info('Approval email sent successfully to: ' . $tenant->email);
                } catch (\Exception $emailEx) {
                    Log::error('Error sending approval email: ' . $emailEx->getMessage(), [
                        'exception' => $emailEx,
                        'trace' => $emailEx->getTraceAsString()
                    ]);
                    // We'll log the error but continue with activation
                }
                
                return response()->json([
                    'message' => 'Tenant activated successfully and database created.',
                    'domain' => $domainUrl,
                    'valid_until' => $tenant->valid_until,
                    'setup_token' => $setupToken // Include for testing purposes only, remove in production
                ]);
            } catch (\Exception $innerEx) {
                // If we're still in tenant context, end it
                if (tenancy()->initialized) {
                    tenancy()->end();
                }
                
                Log::error('Error during tenant database setup: ' . $innerEx->getMessage(), [
                    'exception' => $innerEx,
                    'trace' => $innerEx->getTraceAsString()
                ]);
                
                // Even if there's an error with the database setup, we'll still try to send the email
                try {
                    if (isset($domain) && $domain) {
                        $domainUrl = 'http://' . $domain->domain;
                        Mail::to($tenant->email)->send(new ApprovedMail($tenant, null, $domainUrl, $setupToken));
                        Log::info('Approval email sent after database error to: ' . $tenant->email);
                    }
                } catch (\Exception $emailEx) {
                    Log::error('Error sending approval email after database error: ' . $emailEx->getMessage());
                }
                
                // Even if there's an error with the database or email, we keep the tenant active
                return response()->json([
                    'message' => 'Tenant activated but there was an issue with database setup. Please contact admin.',
                    'details' => $innerEx->getMessage()
                ], 201);
            }
        } catch (\Exception $e) {
            Log::error('Error activating tenant ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Failed to activate tenant', 'details' => $e->getMessage()], 500);
        }
    }

    public function changePassword($id, Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:8',
            ]);

            $tenant = Tenant::findOrFail($id);
            $tenant->password = Hash::make($request->password);
            $tenant->save();

            return response()->json(['message' => 'Password updated successfully']);
        } catch (\Exception $e) {
            Log::error('Error changing password for tenant ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update password', 'details' => $e->getMessage()], 500);
        }
    }
}
