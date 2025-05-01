<?php

namespace App\Providers;

use App\Services\UpdateService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class UpdateServiceProvider extends ServiceProvider
{
    /**
     * register services
     */
    public function register()
    {
        $this->app->singleton(UpdateService::class, function ($app) {
            return new UpdateService();
        });
    }

    /**
     * bootstrap services
     */
    public function boot()
    {
        // Check for updates only on admin routes and when not in console
        if ($this->app->runningInConsole()) {
            return;
        }
        
        // Check if we're on an admin route
        $request = $this->app->make('request');
        if (str_contains($request->getHost(), 'admin-panel')) {
            // Check for updates in the background
            try {
                $updateService = $this->app->make(UpdateService::class);
                $updateService->checkForUpdates();
            } catch (\Exception $e) {
                // Silently fail, we don't want to break the application for update checks
            }
        }
    }
} 