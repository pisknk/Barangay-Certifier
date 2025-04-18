<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\TestDashboardController;
use App\Http\Controllers\TempDashController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Admin Panel Subdomain Routes
|--------------------------------------------------------------------------
|
| These routes are for the admin-panel.localhost:8000 subdomain
|
*/

// Super simple test route that doesn't use a view at all
Route::get('/test-text', function () {
    return 'This is a simple text response from /test-text route';
});

// Temporary diagnostic controller
Route::get('/temp-dash', [TempDashController::class, 'index']);

// Login routes - accessible without authentication
Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [AdminAuthController::class, 'login']);

// Admin console route (renamed from dashboard) - with manual auth check
Route::get('/console', function () {
    // Check if user is logged in
    if (Auth::check()) {
        // Hard-coded data for now
        return view('admin.admindash', [
            'activeTenants' => 5,
            'totalIncome' => 2000,
            'totalRevenue' => 5000
        ]);
    } else {
        // Redirect to login if not authenticated
        return redirect()->route('admin.login');
    }
})->name('admin.console');

// Redirect root to console for convenience
Route::get('/', function() {
    return redirect()->route('admin.console');
});

// Test route - for quick testing
Route::get('/direct-dash', function () {
    return view('admin.admindash', [
        'activeTenants' => 5,
        'totalIncome' => 2000,
        'totalRevenue' => 5000
    ]);
});

// Other routes that need authentication - using manual check
Route::get('/tenants', function () {
    if (!Auth::check()) return redirect()->route('admin.login');
    return app()->call([app(AdminController::class), 'tenants']);
})->name('admin.tenants');

Route::get('/domains', function () {
    if (!Auth::check()) return redirect()->route('admin.login');
    return app()->call([app(AdminController::class), 'domains']);
})->name('admin.domains');

// Tenant editing routes with manual auth check
Route::get('/tenants/{id}/edit', function ($id) {
    if (!Auth::check()) return redirect()->route('admin.login');
    return app()->call([app(AdminController::class), 'edit'], ['id' => $id]);
})->name('admin.tenants.edit');

// Add tenant update, toggle status, update plan, and delete routes
Route::put('/tenants/{id}', function ($id) {
    if (!Auth::check()) return redirect()->route('admin.login');
    return app()->call([app(AdminController::class), 'update'], ['id' => $id]);
})->name('admin.tenants.update');

Route::put('/tenants/{id}/toggle', function ($id) {
    if (!Auth::check()) return redirect()->route('admin.login');
    return app()->call([app(AdminController::class), 'toggleStatus'], ['id' => $id]);
})->name('admin.tenants.toggle');

Route::put('/tenants/{id}/update-plan', function ($id) {
    if (!Auth::check()) return redirect()->route('admin.login');
    return app()->call([app(AdminController::class), 'updatePlan'], ['id' => $id]);
})->name('admin.tenants.update-plan');

Route::delete('/tenants/{id}', function ($id) {
    if (!Auth::check()) return redirect()->route('admin.login');
    return app()->call([app(AdminController::class), 'destroy'], ['id' => $id]);
})->name('admin.tenants.destroy');

// Logout route
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Test routes - not behind auth
Route::get('/test-dashboard', [TestDashboardController::class, 'testDashboard'])->name('admin.test-dashboard');
Route::get('/simple-test', [TestDashboardController::class, 'simpleTest'])->name('admin.simple-test');

// Fallback dashboard route using direct view rendering
Route::get('/fallback-dashboard', function () {
    return view('admin.admindash', [
        'activeTenants' => 5,
        'totalIncome' => 2000,
        'totalRevenue' => 5000
    ]);
})->name('admin.fallback-dashboard');

// Test route
Route::get('/test', function () {
    return 'Admin Panel subdomain test is working at: ' . request()->getHttpHost();
}); 