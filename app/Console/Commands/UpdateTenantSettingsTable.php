<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateTenantSettingsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:update-settings-table {tenant_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the tenant_settings table structure to replace address with header';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update tenant settings table...');
        
        $tenantId = $this->argument('tenant_id');
        
        // If a specific tenant ID is provided, only process that tenant
        if ($tenantId) {
            $this->processTenant($tenantId);
        } else {
            // Look for tenant database connections in config
            $tenantDatabases = [];
            foreach (config('database.connections') as $name => $config) {
                if (strpos($name, 'tenant_') === 0) {
                    $tenantDatabases[] = $name;
                }
            }
            
            if (empty($tenantDatabases)) {
                $this->warn('No tenant databases found in config. Try running with a specific tenant ID.');
                // Create for current tenant database
                $this->processTenant('default');
                return Command::SUCCESS;
            }
            
            foreach ($tenantDatabases as $db) {
                $tenantId = str_replace('tenant_', '', $db);
                $this->processTenant($tenantId);
            }
        }
        
        $this->info('Tenant settings table update completed.');
        
        return Command::SUCCESS;
    }
    
    /**
     * Process a single tenant database
     */
    protected function processTenant($tenantId)
    {
        $this->info("Processing tenant: {$tenantId}");
        
        try {
            // Use the tenant connection directly
            $connection = $tenantId === 'default' ? 'tenant' : 'tenant';
            
            // Check if the tenant_settings table exists
            if (Schema::connection($connection)->hasTable('tenant_settings')) {
                // Check if the 'address' column exists
                if (Schema::connection($connection)->hasColumn('tenant_settings', 'address')) {
                    $this->info("Modifying tenant_settings table for tenant {$tenantId}...");
                    
                    // Rename 'address' to 'header'
                    DB::connection($connection)->statement('ALTER TABLE tenant_settings CHANGE address header TEXT NULL');
                    
                    $this->info("Successfully updated tenant_settings table for tenant {$tenantId}");
                } else if (Schema::connection($connection)->hasColumn('tenant_settings', 'header')) {
                    $this->info("Table already has header column for tenant {$tenantId}");
                } else {
                    $this->warn("Neither 'address' nor 'header' column found for tenant {$tenantId}");
                }
            } else {
                // Create the tenant_settings table from scratch
                $this->info("Creating tenant_settings table for tenant {$tenantId}...");
                
                Schema::connection($connection)->create('tenant_settings', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('tenant_user_id');
                    $table->string('barangay_logo')->nullable();
                    $table->string('municipality_logo')->nullable();
                    $table->text('header')->nullable();
                    $table->string('paper_size')->default('A4');
                    $table->string('watermark')->default('None');
                    $table->string('theme')->default('light');
                    $table->timestamps();
                    
                    // Index for faster lookups
                    $table->index('tenant_user_id');
                });
                
                $this->info("Successfully created tenant_settings table for tenant {$tenantId}");
            }
        } catch (\Exception $e) {
            $this->error("Error processing tenant {$tenantId}: " . $e->getMessage());
        }
    }
} 