<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeactivateTenant extends Command
{
    protected $signature = 'tenancy:deactivate {tenant}';
    protected $description = 'Deactivate a tenant by ID';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        try {
            $tenant = Tenant::find($tenantId);
            
            if (!$tenant) {
                $this->error("Tenant not found: {$tenantId}");
                return Command::FAILURE;
            }
            
            if (!$tenant->is_active) {
                $this->info("Tenant {$tenantId} is already inactive.");
                return Command::SUCCESS;
            }
            
            // Check if tenant_db is set, if not, set it now
            if (empty($tenant->tenant_db)) {
                $dbName = 'tenant_' . $tenant->id;
                $tenant->tenant_db = $dbName;
                $tenant->save();
                
                // Also update directly to be sure
                DB::table('tenants')->where('id', $tenantId)->update([
                    'tenant_db' => $dbName
                ]);
                
                $this->info("Set database name for tenant {$tenantId} to: {$dbName}");
            } else {
                $this->info("Preserving database name for tenant {$tenantId}: {$tenant->tenant_db}");
            }
            
            // Update using both the model and direct DB query to ensure it's updated
            $tenant->is_active = false;
            $tenant->save();
            
            // Also update directly in the database to ensure changes are applied
            DB::table('tenants')->where('id', $tenantId)->update([
                'is_active' => false,
                'updated_at' => now()
            ]);
            
            // Verify the change
            $updatedTenant = DB::table('tenants')->where('id', $tenantId)->first();
            $isNowInactive = $updatedTenant && !$updatedTenant->is_active;
            
            $this->info("Tenant {$tenantId} has been deactivated. Inactive status: " . ($isNowInactive ? 'Yes' : 'No'));
            
            // Get domain
            $domain = $tenant->domains()->first();
            if ($domain) {
                $this->info("You can now visit http://{$domain->domain} to see the 'domain disabled' page.");
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error deactivating tenant {$tenantId}: " . $e->getMessage());
            Log::error("Error deactivating tenant: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 