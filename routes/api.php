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
