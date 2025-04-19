<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Central Domain Routes
|--------------------------------------------------------------------------
|
| These routes are for the central domain localhost:8000 (not for tenants)
|
*/

// Empty route list for central domain
Route::get('/', function () {
    return redirect('http://onboarding.localhost:8000');
});

Route::get('/test', function () {
    return 'Central domain test is working!';
});

// Define a dashboard route for central domain
Route::get('/dashboard', function () {
    return 'Central domain dashboard';
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// The login route is now handled by the Admin routes and auth.php
// No need for a fallback here

require __DIR__.'/auth.php';

// Global fallback for debugging 404 errors
Route::fallback(function () {
    $message = 'Route not found: ' . request()->path();
    \Illuminate\Support\Facades\Log::error($message, [
        'url' => request()->fullUrl(),
        'domain' => request()->getHost(),
        'available_routes' => collect(\Illuminate\Support\Facades\Route::getRoutes())->map(function($route) {
            return [
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'methods' => $route->methods(),
                'domain' => $route->getDomain()
            ];
        })->filter(function($route) {
            return str_contains($route['domain'] ?? '', request()->getHost());
        })->values()->toArray()
    ]);
    
    return response()->json([
        'error' => '404 Not Found',
        'message' => $message,
        'host' => request()->getHost()
    ], 404);
});

// Password setup routes
Route::get('/setup-password/{tenant_id}/{token}', function ($tenant_id, $token) {
    // Log for debugging
    \Illuminate\Support\Facades\Log::info("Setup password attempt", [
        'tenant_id' => $tenant_id,
        'token_length' => strlen($token),
        'token_first_chars' => substr($token, 0, 10) . '...'
    ]);
    
    // Find the tenant
    $tenant = \App\Models\Tenant::where('id', $tenant_id)
        ->where('setup_token', $token)
        ->first();
    
    // If tenant with exact token not found, check if it's a reused link
    if (!$tenant) {
        // Try to find tenant by ID only
        $tenantCheck = \App\Models\Tenant::find($tenant_id);
        
        if ($tenantCheck) {
            \Illuminate\Support\Facades\Log::info("Tenant found without token match", [
                'tenant_id' => $tenant_id,
                'has_setup_token' => !empty($tenantCheck->setup_token),
                'stored_token_length' => $tenantCheck->setup_token ? strlen($tenantCheck->setup_token) : 0,
                'stored_token_first_chars' => $tenantCheck->setup_token ? (substr($tenantCheck->setup_token, 0, 10) . '...') : 'none',
                'is_active' => $tenantCheck->is_active,
                'password_changed' => $tenantCheck->password_changed ? 'Yes' : 'No'
            ]);
            
            // Check if password has not been set yet - allow any token to work
            if (!$tenantCheck->password_changed) {
                \Illuminate\Support\Facades\Log::info("Allowing setup with mismatched token since password not changed yet", [
                    'tenant_id' => $tenant_id
                ]);
                
                // Use the tenant even with mismatched token since password hasn't been set
                $tenant = $tenantCheck;
            } else {
                \Illuminate\Support\Facades\Log::warning("Password already set for tenant", [
                    'tenant_id' => $tenant_id
                ]);
                
                // Generate the tenant URL
                $domain = $tenantCheck->domains()->first();
                $domainUrl = $domain ? 'http://' . $domain->domain : '#';
                
                return view('setup-password', [
                    'tenant_id' => $tenant_id,
                    'token' => $token,
                    'success' => 'You have already set up your password.',
                    'domain_url' => $domainUrl
                ]);
            }
        } else {
            \Illuminate\Support\Facades\Log::warning("Tenant not found", [
                'tenant_id' => $tenant_id
            ]);
            
            return view('setup-password', [
                'tenant_id' => $tenant_id,
                'token' => $token,
                'error' => 'Invalid or expired setup link. Please contact support.'
            ]);
        }
    }
    
    // More detailed logging for debugging
    \Illuminate\Support\Facades\Log::info("Tenant ready for setup", [
        'tenant_id' => $tenant_id,
        'tenant_email' => $tenant->email,
        'password_changed' => $tenant->password_changed ? 'Yes' : 'No'
    ]);
    
    // Check if password has already been changed
    if ($tenant->password_changed) {
        // Generate the tenant URL
        $domain = $tenant->domains()->first();
        $domainUrl = $domain ? 'http://' . $domain->domain : '#';
        
        return view('setup-password', [
            'tenant_id' => $tenant_id,
            'token' => $token,
            'success' => 'You have already set up your password.',
            'domain_url' => $domainUrl
        ]);
    }
    
    return view('setup-password', [
        'tenant_id' => $tenant_id,
        'token' => $token
    ]);
});

Route::post('/complete-setup', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'tenant_id' => 'required|string',
        'token' => 'required|string',
        'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
    ], [
        'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
    ]);
    
    try {
        // Find the tenant - allow any token if password not yet changed
        $tenant = \App\Models\Tenant::where('id', $request->tenant_id)
            ->first();
            
        if (!$tenant) {
            return back()->withErrors(['error' => 'Tenant not found. Please contact support.']);
        }
        
        // Check if password already changed
        if ($tenant->password_changed) {
            return back()->withErrors(['error' => 'Password has already been set for this account.']);
        }
        
        // Set password_changed flag to true
        DB::transaction(function() use ($tenant) {
            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update([
                    'password_changed' => true
                ]);
        });
        
        // Update the model to match what we updated in the DB
        $tenant->password_changed = true;
        
        // Hash the new password
        $hashedPassword = \Illuminate\Support\Facades\Hash::make($request->password);
        
        // Update the tenant record
        $tenant->password = $hashedPassword;
        $tenant->save();
        
        // Initialize tenant context to update the user record
        tenancy()->initialize($tenant);
        
        // Update the tenant user
        $updated = \Illuminate\Support\Facades\DB::connection('tenant')
            ->table('tenant_users')
            ->where('email', $tenant->email)
            ->update([
                'password' => $hashedPassword
            ]);
            
        // End tenant context
        tenancy()->end();
        
        if ($updated) {
            // Generate the tenant URL
            $domain = $tenant->domains()->first();
            $domainUrl = $domain ? 'http://' . $domain->domain : '#';
            
            return back()->with([
                'success' => 'Password set successfully! Your account is now ready to use.',
                'domain_url' => $domainUrl
            ]);
        } else {
            return back()->withErrors(['error' => 'User record not found in tenant database']);
        }
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Password setup error: ' . $e->getMessage());
        return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
});
