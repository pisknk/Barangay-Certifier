<?php

namespace App\Bootstrappers;

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Exceptions\TenantDatabaseDoesNotExistException;
use Illuminate\Support\Facades\Log;

class CustomDatabaseTenancyBootstrapper extends DatabaseTenancyBootstrapper
{
    public function bootstrap(\Stancl\Tenancy\Contracts\Tenant $tenant)
    {
        try {
            Log::info("Bootstrapping tenant: " . $tenant->getTenantKey());
            
            // Check if tenant is active
            if (property_exists($tenant, 'is_active')) {
                $isActive = (int)$tenant->is_active;
                
                // Only allow access if tenant is active (is_active = 1)
                if ($isActive !== \App\Models\Tenant::ACTIVE) {
                    Log::warning("Attempted to access inactive tenant: " . $tenant->getTenantKey() . 
                        " (Status: " . $isActive . ")");
                    
                    // Provide more specific error message based on the status
                    $statusMessage = match($isActive) {
                        \App\Models\Tenant::INACTIVE => "This tenant is not active.",
                        \App\Models\Tenant::DEACTIVATED => "This tenant has been deactivated by an administrator.",
                        \App\Models\Tenant::EXPIRED => "This tenant's subscription has expired.",
                        default => "This tenant is not active.",
                    };
                    
                    throw new \Exception($statusMessage);
                }
            }
            
            // Get the database name from tenant_db field if available
            if (property_exists($tenant, 'tenant_db') && !empty($tenant->tenant_db)) {
                $database = $tenant->tenant_db;
                Log::info("Using existing tenant_db: " . $database);
            } else {
                // Fall back to standard naming convention
                $database = $this->getTenantDatabaseName($tenant);
                Log::info("Using fallback database name: " . $database);
                
                // Update tenant_db directly via DB query instead of save() which might serialize
                try {
                    DB::table('tenants')
                        ->where('id', $tenant->getTenantKey())
                        ->update(['tenant_db' => $database]);
                    
                    Log::info("Updated tenant_db field via direct query for tenant: " . $tenant->getTenantKey());
                    
                    // Update the property on the model without saving
                    if (method_exists($tenant, 'setDatabaseName')) {
                        $tenant->setDatabaseName($database);
                    }
                } catch (\Exception $e) {
                    Log::error("Error updating tenant_db: " . $e->getMessage());
                }
            }

            if (!$this->databaseExists($database)) {
                Log::error("Database does not exist: " . $database);
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
            Log::info("Tenant database connected successfully: " . $database);
        } catch (\Exception $e) {
            Log::error("Error in tenant bootstrap: " . $e->getMessage());
            throw $e;
        }
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
            Log::error("Error checking if database exists: " . $e->getMessage());
            return false;
        }
    }
} 