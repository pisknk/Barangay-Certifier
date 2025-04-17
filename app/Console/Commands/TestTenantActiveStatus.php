<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestTenantActiveStatus extends Command
{
    protected $signature = 'tenancy:test-active-status {tenant}';
    protected $description = 'Test the is_active status discrepancies for a tenant';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        try {
            // Get tenant via Eloquent model
            $tenant = Tenant::find($tenantId);
            
            if (!$tenant) {
                $this->error("Tenant not found: {$tenantId}");
                return Command::FAILURE;
            }
            
            // Get tenant directly from DB
            $dbTenant = DB::table('tenants')->where('id', $tenantId)->first();
            
            $this->info("Tenant ID: {$tenantId}");
            $this->info("Name: {$tenant->name}");
            
            // Compare is_active status
            $this->table(
                ['Source', 'is_active value', 'Cast type'],
                [
                    ['Eloquent Model', $tenant->is_active ? 'true' : 'false', gettype($tenant->is_active)],
                    ['DB Raw', $dbTenant->is_active ? 'true' : 'false', gettype($dbTenant->is_active)],
                ]
            );
            
            // Check for is_active in data JSON
            $data = json_decode($tenant->data ?? '{}', true);
            $hasIsActiveInData = isset($data['is_active']);
            
            if ($hasIsActiveInData) {
                $this->warn("Found is_active in data JSON: " . ($data['is_active'] ? 'true' : 'false'));
            } else {
                $this->info("No is_active key found in data JSON");
            }
            
            // Test updating via eloquent
            $this->info("Testing Eloquent update...");
            $currentStatus = $tenant->is_active;
            $tenant->is_active = !$currentStatus;
            $tenant->save();
            $tenant->refresh();
            
            $this->info("After Eloquent update, is_active = " . ($tenant->is_active ? 'true' : 'false'));
            
            // Test updating via DB
            $this->info("Testing direct DB update...");
            DB::table('tenants')->where('id', $tenantId)->update([
                'is_active' => $currentStatus,
                'updated_at' => now()
            ]);
            
            // Check final status
            $tenant->refresh();
            $dbTenantAfter = DB::table('tenants')->where('id', $tenantId)->first();
            
            $this->table(
                ['Source', 'Final is_active value'],
                [
                    ['Eloquent Model', $tenant->is_active ? 'true' : 'false'],
                    ['DB Raw', $dbTenantAfter->is_active ? 'true' : 'false'],
                ]
            );
            
            // Check domain info
            $domain = $tenant->domains()->first();
            if ($domain) {
                $this->info("Domain info: {$domain->domain}");
            } else {
                $this->warn("No domain found for this tenant");
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error testing tenant {$tenantId}: " . $e->getMessage());
            Log::error("Error testing tenant active status: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 