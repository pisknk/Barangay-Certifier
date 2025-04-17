<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateTenantAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create-admin {tenant_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user in tenant database based on tenant owner information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        
        if ($tenantId) {
            // Create admin for a specific tenant
            $this->createAdminForTenant($tenantId);
        } else {
            // List all tenants and let user choose
            $tenants = Tenant::all();
            if ($tenants->isEmpty()) {
                $this->error('No tenants found.');
                return 1;
            }
            
            $this->info('Available tenants:');
            foreach ($tenants as $index => $tenant) {
                $this->line(($index + 1) . '. ' . $tenant->name . ' (' . $tenant->id . ')');
            }
            
            $choice = $this->ask('Enter the number of the tenant to create admin for, or "all" for all tenants:');
            
            if ($choice === 'all') {
                $this->info('Creating admin users for all tenants...');
                foreach ($tenants as $tenant) {
                    $this->createAdminForTenant($tenant->id);
                }
            } else if (is_numeric($choice) && $choice > 0 && $choice <= count($tenants)) {
                $tenant = $tenants[$choice - 1];
                $this->createAdminForTenant($tenant->id);
            } else {
                $this->error('Invalid choice.');
                return 1;
            }
        }
        
        return 0;
    }
    
    /**
     * Create admin user for a specific tenant.
     */
    protected function createAdminForTenant($tenantId)
    {
        $this->info("Creating admin user for tenant: {$tenantId}");
        
        try {
            // Check if tenant exists
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                $this->error("Tenant not found: {$tenantId}");
                return;
            }
            
            // Initialize tenancy for this tenant
            $this->info("Initializing tenancy for: {$tenantId}");
            tenancy()->initialize($tenant);
            
            // Check if tenant_users table exists
            if (!$this->checkIfTableExists('tenant_users')) {
                $this->error("tenant_users table does not exist for tenant {$tenantId}.");
                $this->warn("You may need to run migrations first.");
                tenancy()->end();
                return;
            }
            
            // Check if admin user already exists
            $existingAdmin = DB::connection('tenant')
                ->table('tenant_users')
                ->where('email', $tenant->email)
                ->first();
                
            if ($existingAdmin) {
                $this->info("Admin user already exists for tenant {$tenantId} with email {$tenant->email}");
                
                // Update role to ensure it's admin
                DB::connection('tenant')
                    ->table('tenant_users')
                    ->where('email', $tenant->email)
                    ->update(['role' => 'admin']);
                    
                $this->info("Ensured user has admin role");
                tenancy()->end();
                return;
            }
            
            // Create admin user
            $now = now();
            
            // Ensure we have a valid password
            $password = $tenant->password;
            if (empty($password)) {
                // Generate a random password if none exists
                $tempPassword = Str::random(10);
                $password = Hash::make($tempPassword);
                
                // Update the tenant record with this password
                $tenant->password = $password;
                $tenant->save();
                
                $this->info("Generated new password for tenant {$tenantId} admin: {$tempPassword}");
                Log::info("Generated new password for tenant {$tenantId} admin (see logs for value)");
            }
            
            DB::connection('tenant')->table('tenant_users')->insert([
                'name' => $tenant->name,
                'email' => $tenant->email,
                'password' => $password,
                'role' => 'admin',
                'position' => 'Barangay Administrator',
                'created_at' => $now,
                'updated_at' => $now
            ]);
            
            $this->info("Created admin user for tenant {$tenantId} with email {$tenant->email}");
            
            // End tenancy
            tenancy()->end();
            
        } catch (\Exception $e) {
            $this->error("Error creating admin user for tenant {$tenantId}: " . $e->getMessage());
            Log::error("Error creating admin user: " . $e->getMessage(), [
                'tenant_id' => $tenantId,
                'exception' => $e
            ]);
            
            // Make sure to end tenancy even if there's an error
            try {
                tenancy()->end();
            } catch (\Exception $endEx) {
                Log::error("Error ending tenancy: " . $endEx->getMessage());
            }
        }
    }
    
    /**
     * Check if a table exists in the tenant database.
     */
    protected function checkIfTableExists($tableName)
    {
        return DB::connection('tenant')->getSchemaBuilder()->hasTable($tableName);
    }
}
