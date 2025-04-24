<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the application with required configurations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up the application...');
        
        // Create symbolic link for storage
        $this->info('Creating symbolic link for storage...');
        if (!file_exists(public_path('storage'))) {
            $this->call('storage:link');
            $this->info('Symbolic link for storage created successfully.');
        } else {
            $this->info('Symbolic link for storage already exists.');
        }
        
        // Run migrations
        $this->info('Running database migrations...');
        $this->call('migrate');
        
        $this->info('Application setup completed successfully!');
        
        return Command::SUCCESS;
    }
} 