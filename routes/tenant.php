<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomInitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Controllers\Tenant\TenantUserController;
use App\Http\Controllers\Tenant\TenantUserViewController;
use App\Http\Controllers\Tenant\TestController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

// Add asset route for direct asset access
Route::get('/assets/{path}', function ($path) {
    $publicPath = public_path("assets/{$path}");
    
    if (!file_exists($publicPath)) {
        abort(404);
    }
    
    $mimeType = [
        'css' => 'text/css',
        'js' => 'text/javascript',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
    ][pathinfo($publicPath, PATHINFO_EXTENSION)] ?? 'text/plain';
    
    return response()->file($publicPath, ['Content-Type' => $mimeType]);
})->where('path', '.*')->middleware($tenantMiddleware);

// Web Routes
Route::middleware(array_merge(['web'], $tenantMiddleware))->group(function () {
    
    // Guest routes
    Route::get('/', [TenantUserViewController::class, 'showLoginForm'])->name('tenant.login');
    Route::post('/login', [TenantUserViewController::class, 'login'])->name('tenant.login.submit');
    
    // Debug route to check user credentials
    Route::get('/debug-check/{email}/{password?}', function ($email, $password = null) {
        $user = DB::connection('tenant')->table('tenant_users')->where('email', $email)->first();
        if (!$user) {
            return [
                'status' => 'error',
                'message' => 'User not found with email: ' . $email,
                'users_count' => DB::connection('tenant')->table('tenant_users')->count()
            ];
        }
        
        // Get the stored password hash
        $storedHash = $user->password;
        
        // Return basic user info for debugging
        $result = [
            'status' => 'success',
            'user_found' => true,
            'hash_check' => $password ? Hash::check($password, $storedHash) : null,
            'stored_hash_starts_with' => substr($storedHash, 0, 10) . '...',
            'total_users' => DB::connection('tenant')->table('tenant_users')->count()
        ];
        
        return $result;
    });
    
    // Add new diagnostic route to view raw user data for debugging password issues
    Route::get('/debug-user-data/{email}', function ($email) {
        try {
            // Get user from tenant database
            $user = DB::connection('tenant')
                ->table('tenant_users')
                ->where('email', $email)
                ->first();
                
            if (!$user) {
                return [
                    'status' => 'error',
                    'message' => 'User not found in tenant database with email: ' . $email
                ];
            }
            
            // Check if there's a tenant with the same email in the central database
            $tenant = tenant();
            $centralTenant = DB::connection('central')
                ->table('tenants')
                ->where('id', $tenant->id)
                ->first();
                
            return [
                'status' => 'success',
                'tenant_user_data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'password_hash_starts_with' => substr($user->password, 0, 10) . '...',
                    'password_hash_length' => strlen($user->password),
                    'created_at' => $user->created_at
                ],
                'central_tenant_data' => $centralTenant ? [
                    'id' => $centralTenant->id,
                    'name' => $centralTenant->name,
                    'email' => $centralTenant->email,
                    'password_hash_starts_with' => substr($centralTenant->password, 0, 10) . '...',
                    'password_hash_length' => strlen($centralTenant->password)
                ] : null
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error retrieving user data: ' . $e->getMessage()
            ];
        }
    });
    
    // Route to fix the admin password
    Route::get('/fix-password/{email}/{password}', function ($email, $password) {
        try {
            // Verify this is an admin user
            $user = DB::connection('tenant')->table('tenant_users')
                ->where('email', $email)
                ->where('role', 'admin')
                ->first();
            
            if (!$user) {
                return [
                    'status' => 'error',
                    'message' => 'Admin user not found with email: ' . $email
                ];
            }
            
            // Update the password with a proper hash
            $updated = DB::connection('tenant')->table('tenant_users')
                ->where('id', $user->id)
                ->update([
                    'password' => Hash::make($password)
                ]);
            
            // Get tenant information
            $tenant = tenant();
            
            // Also update the main tenant record if needed
            if ($tenant && $tenant->email === $email) {
                // Using central database connection to update the tenant record
                DB::connection('central')->table('tenants')
                    ->where('id', $tenant->id)
                    ->update([
                        'password' => Hash::make($password)
                    ]);
            }
            
            if ($updated) {
                return [
                    'status' => 'success',
                    'message' => 'Password updated successfully for ' . $email,
                    'hash_starts_with' => substr(Hash::make($password), 0, 10) . '...'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to update password'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    });
    
    // New diagnostic route to check password consistency
    Route::get('/check-password-consistency', function () {
        try {
            // Get tenant information
            $tenant = tenant();
            if (!$tenant) {
                return [
                    'status' => 'error',
                    'message' => 'No tenant context available'
                ];
            }
            
            // Get the corresponding user in tenant database
            $user = DB::connection('tenant')
                ->table('tenant_users')
                ->where('email', $tenant->email)
                ->first();
                
            if (!$user) {
                return [
                    'status' => 'error',
                    'message' => 'No user found in tenant database with email: ' . $tenant->email
                ];
            }
            
            // Check if passwords match between central and tenant databases
            $result = [
                'status' => 'success',
                'tenant_info' => [
                    'id' => $tenant->id,
                    'email' => $tenant->email,
                    'password_hash_starts_with' => substr($tenant->password, 0, 10) . '...',
                ],
                'tenant_user_info' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'password_hash_starts_with' => substr($user->password, 0, 10) . '...',
                ],
                'password_comparison' => [
                    'hashes_match' => $user->password === $tenant->password,
                    'tenant_password_looks_like_hash' => (strpos($tenant->password, '$2y$') === 0),
                    'user_password_looks_like_hash' => (strpos($user->password, '$2y$') === 0)
                ]
            ];
            
            return $result;
            
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error checking password consistency: ' . $e->getMessage()
            ];
        }
    });
    
    // Password reset routes - will be implemented later
    // Route::get('/forgot-password', [TenantUserViewController::class, 'showForgotPasswordForm'])->name('tenant.password.request');
    // Route::post('/forgot-password', [TenantUserViewController::class, 'sendResetLinkEmail'])->name('tenant.password.email');
    // Route::get('/reset-password/{token}', [TenantUserViewController::class, 'showResetPasswordForm'])->name('tenant.password.reset');
    // Route::post('/reset-password', [TenantUserViewController::class, 'resetPassword'])->name('tenant.password.update');
    
    // Registration routes - will be implemented later
    // Route::get('/register', [TenantUserViewController::class, 'showRegistrationForm'])->name('tenant.register');
    // Route::post('/register', [TenantUserViewController::class, 'register'])->name('tenant.register.submit');
    
    // Protected routes
    Route::middleware(['auth:tenant'])->group(function () {
        Route::get('/dashboard', [TenantUserViewController::class, 'dashboard'])->name('tenant.dashboard');
        Route::post('/logout', [TenantUserViewController::class, 'logout'])->name('tenant.logout');
        
        // User management routes
        Route::prefix('users')->name('tenant.users.')->group(function () {
            Route::get('/', [TenantUserViewController::class, 'index'])->name('index');
            Route::get('/create', [TenantUserViewController::class, 'create'])->name('create');
            Route::post('/', [TenantUserViewController::class, 'store'])->name('store');
            Route::get('/{id}', [TenantUserViewController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [TenantUserViewController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TenantUserViewController::class, 'update'])->name('update');
            Route::delete('/{id}', [TenantUserViewController::class, 'destroy'])->name('destroy');
        });
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
