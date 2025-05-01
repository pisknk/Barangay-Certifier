<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowTenantDatabases extends Command
{
    protected $signature = 'tenancy:show-databases';
    protected $description = 'Show all tenant databases and their associated tenant information';

    public function handle()
    {
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->warn('No tenants found in the database.');
            return Command::FAILURE;
        }
        
        $this->info('Tenant Databases:');
        
        $tableData = [];
        foreach ($tenants as $tenant) {
            // Get database name from the tenant_db field or fallback
            $dbName = $tenant->tenant_db ?? 'tenant_' . $tenant->id;
            
            // Check if database exists
            $dbExists = false;
            try {
                $result = DB::select("SHOW DATABASES LIKE '{$dbName}'");
                $dbExists = !empty($result);
            } catch (\Exception $e) {
                // Can't check database existence
            }
            
            $tableData[] = [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'is_active' => $tenant->is_active ? 'Yes' : 'No',
                'database' => $dbName,
                'exists' => $dbExists ? 'Yes' : 'No',
            ];
        }
        
        $this->table(
            ['Tenant ID', 'Name', 'Active', 'Database', 'DB Exists'],
            $tableData
        );
        
        return Command::SUCCESS;
    }
} 