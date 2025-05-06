<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateTenantSystemVersionsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create-system-versions {tenant_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create system_versions table for existing tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating system_versions table for tenants...');
        
        $tenantId = $this->argument('tenant_id');
        
        // If a specific tenant ID is provided, only process that tenant
        if ($tenantId) {
            $this->processTenant($tenantId);
        } else {
            // Get all tenants
            $tenants = DB::table('tenants')->get();
            
            if ($tenants->isEmpty()) {
                $this->warn('No tenants found.');
                return Command::SUCCESS;
            }
            
            foreach ($tenants as $tenant) {
                $this->processTenant($tenant->id);
            }
        }
        
        $this->info('Operation completed successfully.');
        return Command::SUCCESS;
    }
    
    /**
     * Process a single tenant
     */
    protected function processTenant($tenantId)
    {
        $this->info("Processing tenant: {$tenantId}");
        
        try {
            // Set the database connection for this tenant
            config(['database.connections.tenant.database' => 'tenant_' . $tenantId]);
            DB::purge('tenant');
            
            // Check if the table already exists
            if (Schema::connection('tenant')->hasTable('system_versions')) {
                $this->info("system_versions table already exists for tenant {$tenantId}");
                return;
            }
            
            // Create the table
            Schema::connection('tenant')->create('system_versions', function ($table) {
                $table->id();
                $table->string('version_number')->comment('Semantic version number (e.g., 1.0.0)');
                $table->string('version_name')->nullable()->comment('User-friendly version name');
                $table->text('release_notes')->nullable()->comment('Details about the version changes');
                $table->datetime('release_date')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('When this version was released');
                $table->boolean('is_critical_update')->default(false)->comment('Whether this update is critical');
                $table->timestamps();
            });
            
            // Insert the current version
            try {
                // Try to get the current version from the central database
                $lastVersion = DB::connection('mysql')->table('system_versions')
                    ->orderBy('id', 'desc')
                    ->first();
                    
                if ($lastVersion) {
                    $version = [
                        'version_number' => $lastVersion->version_number,
                        'version_name' => $lastVersion->version_name,
                        'release_notes' => $lastVersion->release_notes,
                        'release_date' => now(),
                        'is_critical_update' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    throw new \Exception('No version found in central database');
                }
            } catch (\Exception $e) {
                // If unable to get from central DB, use hardcoded defaults
                $version = [
                    'version_number' => '2.2',
                    'version_name' => 'Tenant Default',
                    'release_notes' => 'Default version for tenant database.',
                    'release_date' => now(),
                    'is_critical_update' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Insert the version record
            DB::connection('tenant')->table('system_versions')->insert($version);
            
            $this->info("Successfully created system_versions table for tenant {$tenantId}");
        } catch (\Exception $e) {
            $this->error("Error processing tenant {$tenantId}: " . $e->getMessage());
        }
    }
} 