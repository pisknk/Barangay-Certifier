<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReactivateTenant extends Command
{
    protected $signature = 'tenancy:reactivate {tenant}';
    protected $description = 'Reactivate a deactivated tenant by ID';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        try {
            $tenant = Tenant::find($tenantId);
            
            if (!$tenant) {
                $this->error("Tenant not found: {$tenantId}");
                return Command::FAILURE;
            }
            
            if ($tenant->is_active) {
                $this->info("Tenant {$tenantId} is already active.");
                return Command::SUCCESS;
            }
            
            // Update using both the model and direct DB query to ensure it's updated
            $tenant->is_active = true;
            $tenant->save();
            
            // Also update directly in the database to ensure changes are applied
            DB::table('tenants')->where('id', $tenantId)->update([
                'is_active' => true,
                'updated_at' => now()
            ]);
            
            // Verify the change
            $updatedTenant = DB::table('tenants')->where('id', $tenantId)->first();
            $isNowActive = $updatedTenant && $updatedTenant->is_active;
            
            $this->info("Tenant {$tenantId} has been reactivated. Active status: " . ($isNowActive ? 'Yes' : 'No'));
            
            // Check database
            $dbName = $tenant->tenant_db ?? 'tenant_' . $tenant->id;
            
            // If tenant_db is empty, set it
            if (empty($tenant->tenant_db)) {
                $tenant->tenant_db = $dbName;
                $tenant->save();
                
                // Also update directly to be sure
                DB::table('tenants')->where('id', $tenantId)->update([
                    'tenant_db' => $dbName
                ]);
                
                $this->info("Set database name for tenant {$tenantId} to: {$dbName}");
            } else {
                $this->info("Using existing database for tenant {$tenantId}: {$dbName}");
            }
            
            // Ensure the database exists
            $createDbSQL = "CREATE DATABASE IF NOT EXISTS `{$dbName}`";
            DB::statement($createDbSQL);
            
            // Get domain
            $domain = $tenant->domains()->first();
            if ($domain) {
                $this->info("You can now visit http://{$domain->domain} to access the tenant application again.");
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error reactivating tenant {$tenantId}: " . $e->getMessage());
            Log::error("Error reactivating tenant: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 