<?php

use App\Http\Controllers\TenantController;

Route::prefix('tenants')->group(function () {
    Route::get('/', [TenantController::class, 'index']);
    Route::get('{id}', [TenantController::class, 'show']);
    Route::post('/', [TenantController::class, 'store']);
    Route::patch('{id}/deactivate', [TenantController::class, 'deactivate']);
    Route::patch('{id}/activate', [TenantController::class, 'activate']);
    Route::post('{id}/change-password', [TenantController::class, 'changePassword']);
});

// Debug endpoint for API testing
Route::get('/debug', function () {
    return [
        'status' => 'success',
        'message' => 'API is working correctly',
        'timestamp' => now()->toIso8601String(),
        'environment' => app()->environment(),
        'request_info' => [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => request()->method(),
            'url' => request()->fullUrl(),
        ],
    ];
});
