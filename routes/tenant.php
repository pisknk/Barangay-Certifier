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
use App\Http\Controllers\Tenant\TenantSetupController;

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
    
    // Public routes
    Route::get('/', function () {
        return view('tenant.nopay');
    })->name('tenant.nopay')->middleware(\App\Http\Middleware\EnsureTenantIsActive::class . ':0');

    Route::get('/', function () {
        return view('tenant.expired');
    })->name('tenant.expired')->middleware(\App\Http\Middleware\EnsureTenantIsActive::class . ':3');

    Route::get('/', function () {
        return redirect()->route('tenant.login');
    })->middleware(\App\Http\Middleware\EnsureTenantIsActive::class . ':1,2')->name('tenant.home');
    
    // Guest routes
    Route::middleware([\App\Http\Middleware\EnsureTenantIsActive::class . ':1,2', 'guest:tenant'])->group(function () {
        Route::get('/login', [TenantUserViewController::class, 'showLoginForm'])->name('tenant.login');
        Route::post('/login', [TenantUserViewController::class, 'login'])->name('tenant.login.submit');
        
        // Password reset routes
        Route::get('/forgot-password', [TenantUserViewController::class, 'showForgotPasswordForm'])->name('tenant.password.request');
        Route::post('/forgot-password', [TenantUserViewController::class, 'sendResetLinkEmail'])->name('tenant.password.email');
        Route::get('/reset-password/{token}', [TenantUserViewController::class, 'showResetPasswordForm'])->name('tenant.password.reset');
        Route::post('/reset-password', [TenantUserViewController::class, 'resetPassword'])->name('tenant.password.update');
        
        // Setup routes
        Route::get('/setup/{token}', [TenantSetupController::class, 'setupForm'])->name('tenant.setup');
        Route::post('/setup/{token}', [TenantSetupController::class, 'processSetup'])->name('tenant.setup.process');
    });
    
    // Access to theme settings
    Route::get('/settings/get-theme', [App\Http\Controllers\Tenant\TenantSettingsController::class, 'getThemeSettings'])
        ->name('tenant.settings.get-theme');
    
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
            $centralTenant = null;
            
            try {
                $centralTenant = DB::connection('central')
                    ->table('tenants')
                    ->where('id', $tenant->id)
                    ->first();
            } catch (\Exception $e) {
                return [
                    'status' => 'warning',
                    'message' => 'User found in tenant database, but central database check failed',
                    'error' => $e->getMessage(),
                    'tenant_user_data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ]
                ];
            }
            
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
                try {
                    DB::connection('central')->table('tenants')
                        ->where('id', $tenant->id)
                        ->update([
                            'password' => Hash::make($password)
                        ]);
                } catch (\Exception $e) {
                    // Continue even if central DB update fails
                    return [
                        'status' => 'partial_success',
                        'message' => 'Password updated in tenant database only. Central database update failed.',
                        'error' => $e->getMessage(),
                        'hash_starts_with' => substr(Hash::make($password), 0, 10) . '...'
                    ];
                }
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
            
            $centralDbInfo = null;
            $passwordComparison = null;
            
            // Try to access central database info
            try {
                // Check if passwords match between central and tenant databases
                $passwordComparison = [
                    'hashes_match' => $user->password === $tenant->password,
                    'tenant_password_looks_like_hash' => (strpos($tenant->password, '$2y$') === 0),
                    'user_password_looks_like_hash' => (strpos($user->password, '$2y$') === 0)
                ];
                
                $centralDbInfo = [
                    'id' => $tenant->id,
                    'email' => $tenant->email,
                    'password_hash_starts_with' => substr($tenant->password, 0, 10) . '...',
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'partial_info',
                    'message' => 'Could not access central database info: ' . $e->getMessage(),
                    'tenant_user_info' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'role' => $user->role,
                        'password_hash_starts_with' => substr($user->password, 0, 10) . '...',
                    ]
                ];
            }
            
            $result = [
                'status' => 'success',
                'tenant_info' => $centralDbInfo,
                'tenant_user_info' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'password_hash_starts_with' => substr($user->password, 0, 10) . '...',
                ],
                'password_comparison' => $passwordComparison
            ];
            
            return $result;
            
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error checking password consistency: ' . $e->getMessage()
            ];
        }
    });
    
    // Registration routes - will be implemented later
    // Route::get('/register', [TenantUserViewController::class, 'showRegistrationForm'])->name('tenant.register');
    // Route::post('/register', [TenantUserViewController::class, 'register'])->name('tenant.register.submit');
    
    // Protected routes
    Route::middleware([\App\Http\Middleware\EnsureTenantIsActive::class . ':1,2', 'auth:tenant'])->group(function () {
        // Dashboard route
        Route::get('/dashboard', function () {
            // Instead of trying to access the central database, use placeholder values
            // or data from the tenant database to avoid connection errors
            $totalIncome = 45000; // Placeholder value
            $activeTenants = 25; // Placeholder value
            $totalRevenue = 1200000; // Placeholder value
            
            return view('tenant.tenantdash', compact('totalIncome', 'activeTenants', 'totalRevenue'));
        })->name('tenant.dashboard');
        
        // User management routes (admin only)
        Route::prefix('users')->middleware(\App\Http\Middleware\TenantAdminMiddleware::class)->name('tenant.users.')->group(function () {
            Route::get('/', [TenantUserViewController::class, 'index'])->name('index');
            Route::get('/create', [TenantUserViewController::class, 'create'])->name('create');
            Route::post('/', [TenantUserViewController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [TenantUserViewController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TenantUserViewController::class, 'update'])->name('update');
            Route::delete('/{id}', [TenantUserViewController::class, 'destroy'])->name('destroy');
        });
        
        // Certificate routes
        Route::prefix('certificates')->name('tenant.certificates.')->group(function () {
            Route::get('/', [App\Http\Controllers\Tenant\TenantCertificateController::class, 'index'])->name('index');
            Route::get('/download/{filename}', [App\Http\Controllers\Tenant\TenantCertificateController::class, 'downloadCertificate'])->name('download');
            Route::get('/view/{filename}', [App\Http\Controllers\Tenant\TenantCertificateController::class, 'viewCertificate'])->name('view');
            Route::post('/email/{filename}', [App\Http\Controllers\Tenant\TenantCertificateController::class, 'emailCertificate'])->name('email');
            Route::get('/{type}', [App\Http\Controllers\Tenant\TenantCertificateController::class, 'showForm'])->name('form');
            Route::post('/{type}', [App\Http\Controllers\Tenant\TenantCertificateController::class, 'submitForm'])->name('submit');
        });
        
        // Settings routes
        Route::prefix('settings')->name('tenant.settings.')->group(function () {
            Route::get('/', [App\Http\Controllers\Tenant\TenantSettingsController::class, 'index'])->name('index');
            
            // Certificate header settings (available to all plans)
            Route::post('/certificate', [App\Http\Controllers\Tenant\TenantSettingsController::class, 'updateCertificateSettings'])
                ->name('update-certificate');
            
            // Website settings (available to all plans)
            Route::post('/website', [App\Http\Controllers\Tenant\TenantSettingsController::class, 'updateWebsiteSettings'])
                ->name('update-website');
            
            // Software updates (only for Ultimate plan)
            Route::post('/check-updates', [App\Http\Controllers\Tenant\TenantSettingsController::class, 'checkForUpdates'])
                ->name('check-updates');
            
            // New routes for update operations
            Route::post('/perform-update', [App\Http\Controllers\Tenant\TenantSettingsController::class, 'performUpdate'])
                ->name('perform-update');
            
            Route::get('/update-progress', [App\Http\Controllers\Tenant\TenantSettingsController::class, 'getUpdateProgress'])
                ->name('update-progress');
            
            // Theme customization (only for Ultimate plan) - permission check now in controller
            Route::post('/save-theme', [App\Http\Controllers\Tenant\TenantSettingsController::class, 'saveThemeSettings'])
                ->name('save-theme');
        });
        
        // Logout route
        Route::post('/logout', [TenantUserViewController::class, 'logout'])->name('tenant.logout');
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
        // Tenant User CRUD Routes - only accessible to admin users
        Route::prefix('users')->middleware([\App\Http\Middleware\TenantAdminMiddleware::class])->group(function () {
            Route::get('/', [TenantUserController::class, 'index']);
            Route::post('/', [TenantUserController::class, 'store']);
            Route::get('/{id}', [TenantUserController::class, 'show']);
            Route::put('/{id}', [TenantUserController::class, 'update']);
            Route::delete('/{id}', [TenantUserController::class, 'destroy']);
        });
        
        // Check for update file - no auth required
        Route::get('/check-update-file', function() {
            $updateFile = storage_path('app/system/update_available.json');
            $hasUpdate = file_exists($updateFile);
            
            if ($hasUpdate) {
                $updateInfo = json_decode(file_get_contents($updateFile), true);
                return response()->json([
                    'update_available' => true,
                    'update_info' => $updateInfo
                ]);
            } else {
                return response()->json([
                    'update_available' => false
                ]);
            }
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
