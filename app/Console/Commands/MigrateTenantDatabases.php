<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MigrateTenantDatabases extends Command
{
    protected $signature = 'tenancy:migrate-all';
    protected $description = 'Run migrations for all tenant databases';

    public function handle()
    {
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->warn('No tenants found in the database.');
            return Command::FAILURE;
        }
        
        $this->info('Starting migrations for ' . $tenants->count() . ' tenant(s)...');
        
        foreach ($tenants as $tenant) {
            $this->info("Migrating database for tenant: {$tenant->id}");
            
            try {
                // Initialize the tenant context
                tenancy()->initialize($tenant);
                
                // Run migrations
                $this->info("Running migrations for tenant {$tenant->id}...");
                $output = Artisan::call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);
                
                $this->info("Migration output for {$tenant->id}: " . Artisan::output());
                
                // End the tenant context
                tenancy()->end();
                
                $this->info("Successfully migrated database for tenant: {$tenant->id}");
            } catch (\Exception $e) {
                $this->error("Error migrating database for tenant {$tenant->id}: " . $e->getMessage());
                Log::error("Error migrating tenant database: " . $e->getMessage());
            }
        }
        
        $this->info('All tenant migrations completed.');
        
        return Command::SUCCESS;
    }
} 