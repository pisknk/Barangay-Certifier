<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // API routes
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Subdomain routes
            Route::middleware('web')
                ->domain('onboarding.localhost')
                ->group(base_path('routes/onboarding.php'));

            // Admin subdomain - using only web middleware
            // IMPORTANT: Keeping this simple by only using 'web' middleware
            Route::middleware('web')
                ->domain('admin-panel.localhost')
                ->group(base_path('routes/admin.php'));

            // Web routes (central domain)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
} 