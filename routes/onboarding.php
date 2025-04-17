<?php

use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Onboarding Subdomain Routes
|--------------------------------------------------------------------------
|
| These routes are for the onboarding.localhost:8000 subdomain
|
*/

// Root route that redirects to /landing
Route::get('/', function () {
    // Using full URL for redirect to ensure it works with subdomains
    return redirect('http://onboarding.localhost:8000/landing');
});

// Landing page with its own URL
Route::get('/landing', function () {
    return view('landing');
})->name('onboarding.landing');

// Debug route to check request information
Route::get('/debug', function () {
    return [
        'host' => request()->getHttpHost(),
        'domain' => request()->getHost(),
        'url' => url()->current(),
        'root' => url()->to('/'),
        'full_url' => request()->fullUrl(),
        'headers' => request()->headers->all(),
    ];
});

// Test route with simple HTML output
Route::get('/simple-test', function () {
    return '<!DOCTYPE html>
    <html>
    <head>
        <title>Simple Test</title>
    </head>
    <body>
        <h1>Simple Test Page</h1>
        <p>This is a test page to verify routing works.</p>
        <p>Current Host: ' . request()->getHttpHost() . '</p>
        <p>Current URL: ' . url()->current() . '</p>
    </body>
    </html>';
});

// Signup page
Route::get('/signup', function () {
    return view('signup');
})->name('signup');

// Handle signup form submission
Route::post('/signup', [TenantController::class, 'store'])->name('tenant.store');

// Thank you page
Route::get('/thanks', function () {
    return view('thanks');
})->name('thanks');

// Test route
Route::get('/test', function () {
    return 'Onboarding subdomain test is working at: ' . request()->getHttpHost();
}); 