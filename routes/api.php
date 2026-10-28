<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\LayoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Module 18 — Admin Auth
| ─────────────────────────────────────────────
| POST   /api/admin/login           → no auth
| POST   /api/admin/logout          → Bearer token required
| GET    /api/admin/me              → Bearer token required
| PUT    /api/admin/profile         → Bearer token required
| PUT    /api/admin/change-password → Bearer token required
|
| Module — Layout Management
| ─────────────────────────────────────────────
| GET    /api/public/layouts        → no auth
| PUT    /api/admin/layouts/:key    → Bearer token required
|
*/

// ═══════════════════════════════════════════════════════════
//  PUBLIC routes  (no auth)
// ═══════════════════════════════════════════════════════════
Route::prefix('public')->group(function () {
    Route::get('layouts', [LayoutController::class, 'index']);
});

// ═══════════════════════════════════════════════════════════
//  ADMIN routes
// ═══════════════════════════════════════════════════════════
Route::prefix('admin')->group(function () {

    // ── Auth: public ───────────────────────────────────────
    Route::post('login', [AuthController::class, 'login']);

    // ── Auth: protected ────────────────────────────────────
    Route::middleware('auth:api')->group(function () {
        Route::post('logout',          [AuthController::class, 'logout']);
        Route::get('me',               [AuthController::class, 'me']);
        Route::put('profile',          [AuthController::class, 'updateProfile']);
        Route::put('change-password',  [AuthController::class, 'changePassword']);

        // Layout management
        Route::post('layouts/save',    [LayoutController::class, 'save']);
        Route::post('layouts',         [LayoutController::class, 'store']);
        Route::put('layouts/{key}',    [LayoutController::class, 'update']);
    });
});

