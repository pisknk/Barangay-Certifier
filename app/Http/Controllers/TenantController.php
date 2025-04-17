<?php

// app/Http/Controllers/TenantController.php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\WelcomeMail;
use App\Mail\ApprovedMail;
use Stancl\Tenancy\Database\TenantCollection;
use Stancl\Tenancy\TenantManager;
use Illuminate\Support\Facades\DB;

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
            $request->validate([
                'name' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'email' => 'required|email|unique:tenants,email',
                'subscription_plan' => 'required|in:Basic,Essentials,Ultimate',
            ]);

            $password = Str::random(10);
            $barangaySlug = Str::slug($request->barangay);
            
            // Tenant ID should be just the slug without domain or port
            $tenantId = $barangaySlug;
            
            // Domain should include the full domain with port
            $domain = $barangaySlug . '.localhost:8000';

            // Check if the tenant already exists
            if (Tenant::where('id', $tenantId)->exists()) {
                return response()->json(['error' => 'This barangay name is already taken'], 422);
            }

            // Insert directly into database to bypass model events initially
            DB::table('tenants')->insert([
                'id' => $tenantId,
                'name' => $request->name,
                'barangay' => $request->barangay,
                'email' => $request->email,
                'subscription_plan' => $request->subscription_plan,
                'password' => Hash::make($password),
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
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating tenant: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create tenant', 'details' => $e->getMessage()], 500);
        }
    }

    public function deactivate($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->is_active = false;
            $tenant->save();

            return response()->json(['message' => 'Tenant deactivated successfully']);
        } catch (\Exception $e) {
            Log::error('Error deactivating tenant ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Failed to deactivate tenant', 'details' => $e->getMessage()], 500);
        }
    }

    public function activate($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            
            // Generate a temporary password for the tenant
            $tempPassword = Str::random(8);
            $tenant->password = Hash::make($tempPassword);
            $tenant->is_active = true;
            $tenant->save();

            Log::info("Tenant {$id} activated. Temporary password generated: {$tempPassword}");

            try {
                // Get the domain
                $domain = $tenant->domains()->first();
                $domainUrl = $domain ? 'http://' . $domain->domain : null;
                
                // Get database connection details
                $dbConnection = config('database.default');
                $dbHost = config('database.connections.' . $dbConnection . '.host');
                $dbUsername = config('database.connections.' . $dbConnection . '.username');
                $dbPassword = config('database.connections.' . $dbConnection . '.password');
                
                // Database name with prefix
                $dbName = 'tenant_' . $tenant->id;
                
                // Create database if it doesn't exist
                $createDbSQL = "CREATE DATABASE IF NOT EXISTS `{$dbName}`";
                DB::statement($createDbSQL);
                
                Log::info("Created database for tenant {$tenant->id}: {$dbName}");
                
                // Initialize the tenant database and run migrations
                tenancy()->initialize($tenant);
                
                // Run tenant-specific migrations
                $outputMigrate = \Artisan::call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);
                
                Log::info("Tenant-specific migration output for {$tenant->id}: " . \Artisan::output());
                
                // Run all migrations for tenant using the tenants:migrate command
                $output = \Artisan::call('tenants:migrate', [
                    '--tenants' => [$tenant->id],
                    '--force' => true
                ]);
                
                Log::info("Global migration output for tenant {$tenant->id}: " . \Artisan::output());
                
                // End the tenant context
                tenancy()->end();
                
                // Send approval email with temporary password
                try {
                    Mail::to($tenant->email)->send(new ApprovedMail($tenant, $tempPassword, $domainUrl));
                    Log::info('Approval email sent to: ' . $tenant->email . ' with temporary password and domain: ' . $domainUrl);
                } catch (\Exception $emailEx) {
                    Log::error('Error sending approval email: ' . $emailEx->getMessage());
                    // We'll log the error but continue with activation
                }
                
                return response()->json([
                    'message' => 'Tenant activated successfully and database created.',
                    'domain' => $domainUrl,
                    'temp_password' => $tempPassword // Include this for testing purposes only, remove in production
                ]);
            } catch (\Exception $innerEx) {
                Log::error('Error during tenant database setup or email: ' . $innerEx->getMessage());
                
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
