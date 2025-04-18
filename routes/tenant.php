<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomInitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Controllers\Tenant\TenantUserController;
use App\Http\Controllers\Tenant\TestController;

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

// Common middleware for all tenant routes
$tenantMiddleware = [
    CustomInitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    EnsureTenantIsActive::class,
];

// Web Routes
Route::middleware(array_merge(['web'], $tenantMiddleware))->group(function () {
    Route::get('/', function () {
        $tenant = tenant();
        return 'This is your multi-tenant application. The id of the current tenant is ' . ($tenant ? $tenant->id : 'undefined');
    });

    Route::get('/dashboard', function () {
        $tenant = tenant();
        return 'Welcome to your tenant dashboard! Your tenant ID is: ' . ($tenant ? $tenant->id : 'undefined');
    });
    
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

// API Routes
Route::middleware(array_merge(['api'], $tenantMiddleware))
    ->prefix('api')
    ->group(function () {
        // Tenant User CRUD Routes
        Route::prefix('users')->group(function () {
            Route::get('/', [TenantUserController::class, 'index']);
            Route::post('/', [TenantUserController::class, 'store']);
            Route::get('/{id}', [TenantUserController::class, 'show']);
            Route::put('/{id}', [TenantUserController::class, 'update']);
            Route::delete('/{id}', [TenantUserController::class, 'destroy']);
        });
        
        // Test routes
        Route::prefix('test')->group(function () {
            Route::get('/users', [TestController::class, 'testTenantUsers']);
            Route::post('/create-user', [TestController::class, 'createTestUser']);
        });
        
        // API health check
        Route::get('/health-check', function () {
            return response()->json([
                'status' => 'success',
                'message' => 'Tenant API is working',
                'tenant_id' => tenant('id'),
                'timestamp' => now()->toIso8601String()
            ]);
        });
    });
