<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

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

// Emergency dashboard route that will work regardless of subdomain routing issues
Route::domain('admin-panel.localhost')->group(function () {
    Route::get('/direct-dash', function () {
        return view('admin.admindash', [
            'activeTenants' => 5, 
            'totalIncome' => 2000,
            'totalRevenue' => 5000
        ]);
    });
});

// Special admin dashboard route that should work regardless of other routing issues
Route::get('/admin-dashboard', function () {
    if (request()->getHost() === 'admin-panel.localhost') {
        return view('admin.admindash', [
            'activeTenants' => 5, 
            'totalIncome' => 2000,
            'totalRevenue' => 5000
        ]);
    } else {
        return 'This route is only available on admin-panel.localhost domain';
    }
});
