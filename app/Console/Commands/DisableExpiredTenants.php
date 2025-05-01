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
    protected $signature = 'tenants:mark-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark tenants with expired subscriptions as expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired tenant subscriptions...');
        
        // Get all active tenants with expired subscription dates
        $expiredTenants = Tenant::where('is_active', Tenant::ACTIVE)
            ->whereNotNull('valid_until')
            ->where('valid_until', '<', now())
            ->get();
            
        $count = $expiredTenants->count();
        
        if ($count === 0) {
            $this->info('No expired tenant subscriptions found.');
            return 0;
        }
        
        $this->info("Found {$count} expired tenant subscriptions. Marking as expired...");
        
        // Process each expired tenant
        foreach ($expiredTenants as $tenant) {
            try {
                $this->info("Marking tenant as expired: {$tenant->id} (expired on {$tenant->valid_until->format('Y-m-d')})");
                
                // Mark the tenant as expired - this preserves the valid_until date for future reactivation
                $tenant->markAsExpired();
                
                // Log the action
                Log::info("Tenant {$tenant->id} automatically marked as expired due to subscription expiration on {$tenant->valid_until}");
                
            } catch (\Exception $e) {
                $this->error("Error marking tenant {$tenant->id}: {$e->getMessage()}");
                Log::error("Error marking expired tenant {$tenant->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info('Finished processing expired tenant subscriptions.');
        return 0;
    }
}
