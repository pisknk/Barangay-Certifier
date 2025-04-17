<?php

namespace App\Bootstrappers;

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Exceptions\TenantDatabaseDoesNotExistException;

class CustomDatabaseTenancyBootstrapper extends DatabaseTenancyBootstrapper
{
    public function bootstrap(\Stancl\Tenancy\Contracts\Tenant $tenant)
    {
        // Get the database name from tenant_db field if available
        if (property_exists($tenant, 'tenant_db') && !empty($tenant->tenant_db)) {
            $database = $tenant->tenant_db;
        } else {
            // Fall back to standard naming convention
            $database = $this->getTenantDatabaseName($tenant);
            
            // Try to update the tenant_db field if tenant supports it
            if (method_exists($tenant, 'setDatabaseName')) {
                $tenant->setDatabaseName($database);
                
                // Persist the change
                if (method_exists($tenant, 'save')) {
                    $tenant->save();
                    
                    // Also update directly to be sure
                    if ($tenant instanceof TenantWithDatabase) {
                        DB::table('tenants')
                            ->where('id', $tenant->getTenantKey())
                            ->update(['tenant_db' => $database]);
                    }
                }
            }
        }

        if (! $this->databaseExists($database)) {
            throw new TenantDatabaseDoesNotExistException($database);
        }

        config([
            'database.connections.tenant.database' => $database,
        ]);

        app()->forgetInstance('db.connection');
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::setDefaultConnection('tenant');

        $this->connection = DB::connection('tenant')->getPdo();
    }
    
    /**
     * Get the tenant database name.
     */
    public function getTenantDatabaseName(\Stancl\Tenancy\Contracts\Tenant $tenant): string
    {
        $prefix = config('tenancy.database.prefix', 'tenant_');
        $suffix = config('tenancy.database.suffix', '');
        
        return $prefix . $tenant->getTenantKey() . $suffix;
    }

    /**
     * Check if the database exists.
     */
    public function databaseExists(string $database): bool
    {
        try {
            // Check if database exists by attempting to select from it
            $result = DB::select("SHOW DATABASES LIKE '{$database}'");
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }
} 