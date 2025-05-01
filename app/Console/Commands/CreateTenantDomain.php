<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTenantDomain extends Command
{
    protected $signature = 'tenancy:create-domain {tenant} {domain?}';
    protected $description = 'Create or update domain for a tenant with correct port format';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        // Get tenant
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant not found: {$tenantId}");
            return Command::FAILURE;
        }
        
        $this->info("Found tenant: {$tenant->name} ({$tenant->id})");
        
        // Get or create domain
        $domainName = $this->argument('domain') ?? $tenant->id . '.localhost:8000';
        
        // Check if domain already exists
        $existingDomain = DB::table('domains')
            ->where('tenant_id', $tenant->id)
            ->first();
            
        if ($existingDomain) {
            $this->info("Updating existing domain: {$existingDomain->domain} -> {$domainName}");
            
            // Update domain
            DB::table('domains')
                ->where('id', $existingDomain->id)
                ->update([
                    'domain' => $domainName, 
                    'updated_at' => now()
                ]);
        } else {
            $this->info("Creating new domain: {$domainName}");
            
            // Create domain
            $tenant->domains()->create(['domain' => $domainName]);
        }
        
        $this->info("Domain {$domainName} has been created/updated for tenant {$tenant->id}");
        
        return Command::SUCCESS;
    }
} 