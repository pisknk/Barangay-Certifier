<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckTenantDatabases extends Command
{
    protected $signature = 'tenancy:check-db {tenant?}';
    protected $description = 'Check tenant databases for required tables';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        if ($tenantId) {
            $tenants = Tenant::where('id', $tenantId)->get();
            if ($tenants->isEmpty()) {
                $this->error("Tenant not found: {$tenantId}");
                return Command::FAILURE;
            }
        } else {
            $tenants = Tenant::all();
            if ($tenants->isEmpty()) {
                $this->warn('No tenants found in the database.');
                return Command::FAILURE;
            }
        }
        
        foreach ($tenants as $tenant) {
            $this->info("Checking database for tenant: {$tenant->id}");
            
            try {
                // Initialize tenant context
                tenancy()->initialize($tenant);
                
                // Check for sessions table
                $hasSessionsTable = Schema::hasTable('sessions');
                
                // Get all tables
                $tables = DB::select('SHOW TABLES');
                $tableColumn = 'Tables_in_' . config('database.connections.tenant.database');
                $tableList = array_map(function ($table) use ($tableColumn) {
                    return $table->$tableColumn;
                }, $tables);
                
                $this->info("Tables in tenant {$tenant->id} database:");
                $this->table(['Table Name'], array_map(function ($table) {
                    return [$table];
                }, $tableList));
                
                $this->info("Sessions table exists: " . ($hasSessionsTable ? 'Yes' : 'No'));
                
                // End tenant context
                tenancy()->end();
            } catch (\Exception $e) {
                $this->error("Error checking database for tenant {$tenant->id}: " . $e->getMessage());
            }
        }
        
        return Command::SUCCESS;
    }
} 