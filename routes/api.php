<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\MediaUploadController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SiteSettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════════════════════════
//  PUBLIC routes  (No Auth)
// ═══════════════════════════════════════════════════════════
Route::prefix('public')->group(function () {
    // Layouts
    Route::get('layouts', [LayoutController::class, 'index']);

    // 3.2 SEO
    Route::get('seo/{page}', [SeoController::class, 'showPublic']);

    // 3.4 Site Settings
    Route::get('settings', [SiteSettingController::class, 'show']);
});

// ═══════════════════════════════════════════════════════════
//  ADMIN routes
// ═══════════════════════════════════════════════════════════
Route::prefix('admin')->group(function () {

    // ── Public Auth ────────────────────────────────────────
    Route::post('login', [AuthController::class, 'login']);

    // ── Protected (Requires Bearer token) ──────────────────
    Route::middleware('auth:api')->group(function () {
        // Auth session & profile
        Route::post('logout',          [AuthController::class, 'logout']);
        Route::get('me',               [AuthController::class, 'me']);
        Route::put('profile',          [AuthController::class, 'updateProfile']);
        Route::put('change-password',  [AuthController::class, 'changePassword']);

        // Layouts
        Route::post('layouts/save',    [LayoutController::class, 'save']);
        Route::post('layouts',         [LayoutController::class, 'store']);
        Route::put('layouts/{key}',    [LayoutController::class, 'update']);

        // 3.2 SEO
        Route::get('seo/{page}',       [SeoController::class, 'showAdmin']);
        Route::put('seo/{page}',       [SeoController::class, 'update']);

        // 3.3 Media Uploads
        Route::post('uploads',         [MediaUploadController::class, 'store']);
        Route::delete('uploads/{id}',  [MediaUploadController::class, 'destroy']);

        // 3.4 Site Settings
        Route::get('settings',         [SiteSettingController::class, 'show']);
        Route::put('settings',         [SiteSettingController::class, 'update']);
    });
});
