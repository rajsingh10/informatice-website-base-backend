<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Auth Routes — Module 18
|--------------------------------------------------------------------------
|
| Base URL : /api/admin
| Auth     : Laravel Passport (auth:api guard)
|
| POST   /api/admin/login           → no auth
| POST   /api/admin/logout          → Bearer token required
| GET    /api/admin/me              → Bearer token required
| PUT    /api/admin/profile         → Bearer token required
| PUT    /api/admin/change-password → Bearer token required
|
*/

Route::prefix('admin')->group(function () {

    // ── Public ──────────────────────────────────────────────────────────
    Route::post('login', [AuthController::class, 'login']);

    // ── Protected (requires Bearer token) ───────────────────────────────
    Route::middleware('auth:api')->group(function () {
        Route::post('logout',          [AuthController::class, 'logout']);
        Route::get('me',               [AuthController::class, 'me']);
        Route::put('profile',          [AuthController::class, 'updateProfile']);
        Route::put('change-password',  [AuthController::class, 'changePassword']);
    });
});

