<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeSectionController;
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\MediaUploadController;
use App\Http\Controllers\NavbarController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\ServiceController;
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

    // Module 2: Navbar Navigation
    Route::get('navigation/navbar', [NavbarController::class, 'showPublic']);

    // Module 3: Home Sections
    Route::get('home-sections', [HomeSectionController::class, 'index']);

    // Module 4: Services
    Route::get('services', [ServiceController::class, 'indexPublic']);
    Route::get('services/{slug}', [ServiceController::class, 'showPublic']);

    // Module 5: Portfolio
    Route::get('portfolio', [PortfolioController::class, 'indexPublic']);
    Route::get('portfolio/{slug}', [PortfolioController::class, 'showPublic']);

    // Module 6: Blog
    Route::get('blog', [BlogController::class, 'indexPublic']);
    Route::get('blog/{slug}', [BlogController::class, 'showPublic']);
});

// ═══════════════════════════════════════════════════════════
//  ADMIN routes
// ═══════════════════════════════════════════════════════════
Route::prefix('admin')->group(function () {

    // ── Public Auth (Supports both /login and /auth/login) ──
    Route::post('login',      [AuthController::class, 'login']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // ── Protected (Requires Bearer token) ──────────────────
    Route::middleware('auth:api')->group(function () {
        // Auth session & profile
        Route::post('logout',                              [AuthController::class, 'logout']);
        Route::post('auth/logout',                         [AuthController::class, 'logout']);
        Route::get('me',                                   [AuthController::class, 'me']);
        Route::get('auth/me',                              [AuthController::class, 'me']);
        Route::match(['put', 'post'], 'profile',           [AuthController::class, 'updateProfile']);
        Route::match(['put', 'post'], 'auth/profile',      [AuthController::class, 'updateProfile']);
        Route::match(['put', 'post'], 'change-password',      [AuthController::class, 'changePassword']);
        Route::match(['put', 'post'], 'auth/change-password', [AuthController::class, 'changePassword']);

        // Layouts (Modules 1, 2, 3, 4, 5 etc.)
        Route::post('layouts/save',            [LayoutController::class, 'save']);
        Route::post('layouts',                 [LayoutController::class, 'store']);
        Route::put('layouts/{key}',            [LayoutController::class, 'update']);

        // 3.2 SEO
        Route::get('seo/{page}',               [SeoController::class, 'showAdmin']);
        Route::put('seo/{page}',               [SeoController::class, 'update']);

        // 3.3 Media Uploads
        Route::post('uploads',                 [MediaUploadController::class, 'store']);
        Route::delete('uploads/{id}',          [MediaUploadController::class, 'destroy']);

        // 3.4 Site Settings
        Route::get('settings',                 [SiteSettingController::class, 'show']);
        Route::put('settings',                 [SiteSettingController::class, 'update']);

        // Module 2: Navbar Navigation
        Route::get('navigation/navbar',        [NavbarController::class, 'showAdmin']);
        Route::put('navigation/navbar/behavior', [NavbarController::class, 'updateBehavior']);
        Route::put('navigation/navbar/items',  [NavbarController::class, 'updateItems']);
        Route::put('navigation/navbar/cta',    [NavbarController::class, 'updateCta']);
        Route::post('navigation/navbar/reset', [NavbarController::class, 'reset']);

        // Module 3: Home Sections
        Route::put('home-sections/batch',      [HomeSectionController::class, 'batch']);
        Route::post('home-sections/reset',     [HomeSectionController::class, 'reset']);
        Route::put('home-sections/{sectionId}', [HomeSectionController::class, 'update']);

        // Module 4: Services CRUD
        Route::get('services',                 [ServiceController::class, 'indexAdmin']);
        Route::get('services/{id}',            [ServiceController::class, 'showAdmin']);
        Route::post('services',                [ServiceController::class, 'store']);
        Route::put('services/{id}',            [ServiceController::class, 'update']);
        Route::delete('services/{id}',         [ServiceController::class, 'destroy']);

        // Module 5: Portfolio CRUD
        Route::get('portfolio',                [PortfolioController::class, 'indexAdmin']);
        Route::get('portfolio/{id}',           [PortfolioController::class, 'showAdmin']);
        Route::post('portfolio',               [PortfolioController::class, 'store']);
        Route::put('portfolio/{id}',           [PortfolioController::class, 'update']);
        Route::delete('portfolio/{id}',        [PortfolioController::class, 'destroy']);

        // Module 6: Blog CRUD
        Route::get('blog',                     [BlogController::class, 'indexAdmin']);
        Route::get('blog/{id}',                [BlogController::class, 'showAdmin']);
        Route::post('blog',                    [BlogController::class, 'store']);
        Route::put('blog/{id}',                [BlogController::class, 'update']);
        Route::delete('blog/{id}',             [BlogController::class, 'destroy']);
    });
});
