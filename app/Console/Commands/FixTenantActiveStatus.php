<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class FixTenantActiveStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix-active-status {tenant_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the is_active status for tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        
        if ($tenantId) {
            // Fix a specific tenant
            $this->fixTenantStatus($tenantId);
        } else {
            // List all tenants and let user choose
            $tenants = DB::table('tenants')->get();
            if ($tenants->isEmpty()) {
                $this->error('No tenants found.');
                return 1;
            }
            
            $this->info('Available tenants:');
            foreach ($tenants as $index => $tenant) {
                $this->line(($index + 1) . '. ' . $tenant->name . ' (' . $tenant->id . ') - is_active: ' . 
                    ($tenant->is_active ? 'true' : 'false'));
            }
            
            $choice = $this->ask('Enter the number of the tenant to fix status for, "all" for all tenants, or "activate-all" to activate all:');
            
            if ($choice === 'all') {
                $this->info('Fixing status for all tenants...');
                foreach ($tenants as $tenant) {
                    $this->fixTenantStatus($tenant->id);
                }
            } else if ($choice === 'activate-all') {
                $this->info('Activating all tenants...');
                foreach ($tenants as $tenant) {
                    $this->activateTenant($tenant->id);
                }
            } else if (is_numeric($choice) && $choice > 0 && $choice <= count($tenants)) {
                $tenant = $tenants[$choice - 1];
                $this->askWhatToDoWithTenant($tenant->id, $tenant->is_active);
            } else {
                $this->error('Invalid choice.');
                return 1;
            }
        }
        
        return 0;
    }
    
    /**
     * Ask what to do with a specific tenant
     */
    protected function askWhatToDoWithTenant($tenantId, $currentStatus)
    {
        $this->info("Tenant {$tenantId} current status: " . ($currentStatus ? 'active' : 'inactive'));
        $action = $this->choice('What would you like to do?', [
            'activate' => 'Activate tenant',
            'deactivate' => 'Deactivate tenant',
            'fix' => 'Just fix any data inconsistencies',
            'cancel' => 'Cancel'
        ], 'fix');
        
        if ($action === 'activate') {
            $this->activateTenant($tenantId);
        } else if ($action === 'deactivate') {
            $this->deactivateTenant($tenantId);
        } else if ($action === 'fix') {
            $this->fixTenantStatus($tenantId);
        } else {
            $this->info('Operation cancelled.');
        }
    }
    
    /**
     * Fix tenant status to ensure DB and data JSON are consistent
     */
    protected function fixTenantStatus($tenantId)
    {
        $this->info("Fixing status for tenant: {$tenantId}");
        
        try {
            // Check if tenant exists
            $tenant = DB::table('tenants')->where('id', $tenantId)->first();
            if (!$tenant) {
                $this->error("Tenant not found: {$tenantId}");
                return;
            }
            
            // Get current status
            $isActive = $tenant->is_active;
            $this->info("Current status: " . ($isActive ? 'active' : 'inactive'));
            
            // Ensure data column is not overriding is_active
            $data = json_decode($tenant->data ?? '{}', true);
            if (isset($data['is_active'])) {
                $this->warn("Found is_active in data JSON: " . ($data['is_active'] ? 'true' : 'false'));
                unset($data['is_active']);
            }
            
            // Update the tenant with fixed data
            DB::table('tenants')->where('id', $tenantId)->update([
                'is_active' => $isActive,
                'data' => json_encode($data),
            ]);
            
            // Verify the fix
            $updatedTenant = DB::table('tenants')->where('id', $tenantId)->first();
            $this->info("Status after fix: " . ($updatedTenant->is_active ? 'active' : 'inactive'));
            $this->info("Successfully fixed tenant: {$tenantId}");
        } catch (\Exception $e) {
            $this->error("Error fixing tenant {$tenantId}: " . $e->getMessage());
            Log::error("Error fixing tenant status: " . $e->getMessage());
        }
    }
    
    /**
     * Activate a tenant
     */
    protected function activateTenant($tenantId)
    {
        $this->info("Activating tenant: {$tenantId}");
        
        try {
            // Update the tenant status to active
            DB::table('tenants')->where('id', $tenantId)->update([
                'is_active' => true,
            ]);
            
            // Verify the activation
            $updatedTenant = DB::table('tenants')->where('id', $tenantId)->first();
            if ($updatedTenant->is_active) {
                $this->info("Successfully activated tenant: {$tenantId}");
            } else {
                $this->error("Failed to activate tenant: {$tenantId}");
            }
        } catch (\Exception $e) {
            $this->error("Error activating tenant {$tenantId}: " . $e->getMessage());
            Log::error("Error activating tenant: " . $e->getMessage());
        }
    }
    
    /**
     * Deactivate a tenant
     */
    protected function deactivateTenant($tenantId)
    {
        $this->info("Deactivating tenant: {$tenantId}");
        
        try {
            // Update the tenant status to inactive
            DB::table('tenants')->where('id', $tenantId)->update([
                'is_active' => false,
            ]);
            
            // Verify the deactivation
            $updatedTenant = DB::table('tenants')->where('id', $tenantId)->first();
            if (!$updatedTenant->is_active) {
                $this->info("Successfully deactivated tenant: {$tenantId}");
            } else {
                $this->error("Failed to deactivate tenant: {$tenantId}");
            }
        } catch (\Exception $e) {
            $this->error("Error deactivating tenant {$tenantId}: " . $e->getMessage());
            Log::error("Error deactivating tenant: " . $e->getMessage());
        }
    }
} 