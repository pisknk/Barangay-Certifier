<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DisableExpiredTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:disable-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically disable tenants with expired subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired tenant subscriptions...');
        
        // Get all active tenants with expired subscription dates
        $expiredTenants = Tenant::where('is_active', true)
            ->whereNotNull('valid_until')
            ->where('valid_until', '<', now())
            ->get();
            
        $count = $expiredTenants->count();
        
        if ($count === 0) {
            $this->info('No expired tenant subscriptions found.');
            return 0;
        }
        
        $this->info("Found {$count} expired tenant subscriptions. Disabling...");
        
        // Process each expired tenant
        foreach ($expiredTenants as $tenant) {
            try {
                $this->info("Disabling tenant: {$tenant->id} (expired on {$tenant->valid_until->format('Y-m-d')})");
                
                // Disable the tenant
                $tenant->is_active = false;
                $tenant->save();
                
                // Double-check via direct DB update to ensure it's updated
                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update(['is_active' => false]);
                
                // Log the action
                Log::info("Tenant {$tenant->id} automatically disabled due to subscription expiration on {$tenant->valid_until}");
                
            } catch (\Exception $e) {
                $this->error("Error disabling tenant {$tenant->id}: {$e->getMessage()}");
                Log::error("Error disabling expired tenant {$tenant->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info('Finished processing expired tenant subscriptions.');
        return 0;
    }
}
