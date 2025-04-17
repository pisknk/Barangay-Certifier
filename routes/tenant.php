<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomInitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\EnsureTenantIsActive;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Set InitializeTenancyByDomain first to ensure the tenant is properly identified
Route::middleware([
    'web',
    CustomInitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    EnsureTenantIsActive::class,
])->group(function () {
    Route::get('/', function () {
        $tenant = tenant();
        return 'This is your multi-tenant application. The id of the current tenant is ' . ($tenant ? $tenant->id : 'undefined');
    });

    // Add more tenant-specific routes here
    Route::get('/dashboard', function () {
        $tenant = tenant();
        return 'Welcome to your tenant dashboard! Your tenant ID is: ' . ($tenant ? $tenant->id : 'undefined');
    });
    
    // Debug route to check domain
    Route::get('/debug-domain', function () {
        return [
            'host' => request()->getHost(),
            'http_host' => request()->getHttpHost(),
            'tenant' => tenant() ? [
                'id' => tenant('id'),
                'is_active' => tenant()->is_active
            ] : null,
        ];
    });
});
