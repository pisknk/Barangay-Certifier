<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\TestMail::class,
        Commands\CreateTenantDatabase::class,
        Commands\FixTenantActiveStatus::class,
        Commands\CheckDomains::class,
        Commands\MigrateTenantDatabases::class,
        Commands\CreateTenantDomain::class,
        Commands\FixTenantHosts::class,
        Commands\CheckTenantDatabases::class,
        Commands\DeactivateTenant::class,
        Commands\ReactivateTenant::class,
        Commands\TestTenantActiveStatus::class,
        Commands\ShowTenantDatabases::class,
        Commands\InitializeTenantDatabases::class,
        Commands\DisableExpiredTenants::class,
    ];
    
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Check for expired tenant subscriptions daily at midnight
        $schedule->command('tenants:disable-expired')
                ->daily()
                ->at('00:01')
                ->appendOutputTo(storage_path('logs/tenant-subscription-checks.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 