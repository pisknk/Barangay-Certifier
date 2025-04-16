<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class CreateTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create-db {tenant_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually create tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        
        if ($tenantId) {
            // Create database for a specific tenant
            $this->createDatabaseForTenant($tenantId);
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
            
            $choice = $this->ask('Enter the number of the tenant to create database for, or "all" for all tenants:');
            
            if ($choice === 'all') {
                $this->info('Creating databases for all tenants...');
                foreach ($tenants as $tenant) {
                    $this->createDatabaseForTenant($tenant->id);
                }
            } else if (is_numeric($choice) && $choice > 0 && $choice <= count($tenants)) {
                $tenant = $tenants[$choice - 1];
                $this->createDatabaseForTenant($tenant->id);
            } else {
                $this->error('Invalid choice.');
                return 1;
            }
        }
        
        return 0;
    }
    
    /**
     * Create database for a specific tenant.
     */
    protected function createDatabaseForTenant($tenantId)
    {
        $this->info("Creating database for tenant: {$tenantId}");
        
        try {
            // Check if tenant exists
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                $this->error("Tenant not found: {$tenantId}");
                return;
            }
            
            // Database name with prefix
            $dbName = 'tenant_' . $tenant->id;
            
            // Create database if it doesn't exist
            $createDbSQL = "CREATE DATABASE IF NOT EXISTS `{$dbName}`";
            DB::statement($createDbSQL);
            
            $this->info("Database created: {$dbName}");
            
            // Initialize the tenant database and run migrations
            $this->info("Running migrations for tenant: {$tenantId}");
            
            tenancy()->initialize($tenant);
            
            // Run migrations for the tenant
            $output = \Artisan::call('tenants:migrate', [
                '--tenants' => [$tenant->id],
            ]);
            
            $this->info("Migration output: " . \Artisan::output());
            
            // End the tenant context
            tenancy()->end();
            
            $this->info("Successfully created database and ran migrations for tenant: {$tenantId}");
        } catch (\Exception $e) {
            $this->error("Error creating database for tenant {$tenantId}: " . $e->getMessage());
            Log::error("Error creating tenant database: " . $e->getMessage());
        }
    }
} 