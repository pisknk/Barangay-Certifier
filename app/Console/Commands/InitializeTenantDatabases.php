<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InitializeTenantDatabases extends Command
{
    protected $signature = 'tenancy:init-databases';
    protected $description = 'Initialize the tenant_db field for all tenants';

    public function handle()
    {
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->warn('No tenants found in the database.');
            return Command::FAILURE;
        }
        
        $this->info('Initializing tenant_db field for ' . $tenants->count() . ' tenant(s)...');
        
        foreach ($tenants as $tenant) {
            if (empty($tenant->tenant_db)) {
                $dbName = 'tenant_' . $tenant->id;
                
                $this->info("Setting database name for tenant {$tenant->id} to: {$dbName}");
                
                // Update the model
                $tenant->tenant_db = $dbName;
                $tenant->save();
                
                // Also update directly to be sure
                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update(['tenant_db' => $dbName]);
                
                $this->info("Created database reference for tenant {$tenant->id}: {$dbName}");
                
                // Create the database if it doesn't exist
                try {
                    $createDbSQL = "CREATE DATABASE IF NOT EXISTS `{$dbName}`";
                    DB::statement($createDbSQL);
                    $this->info("Ensured database exists: {$dbName}");
                } catch (\Exception $e) {
                    $this->error("Failed to create database {$dbName}: " . $e->getMessage());
                    Log::error("Failed to create database {$dbName}: " . $e->getMessage());
                }
            } else {
                $this->info("Tenant {$tenant->id} already has database name: {$tenant->tenant_db}");
            }
        }
        
        $this->info('All tenant databases initialized. Run tenancy:show-databases to see the results.');
        
        return Command::SUCCESS;
    }
} 