<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel Subdomain Routes
|--------------------------------------------------------------------------
|
| These routes are for the admin-panel.localhost:8000 subdomain
|
*/

// Admin panel landing page
Route::get('/', function () {
    return 'Admin Panel Dashboard';
})->name('admin.dashboard');

// Test route
Route::get('/test', function () {
    return 'Admin Panel subdomain test is working at: ' . request()->getHttpHost();
}); 