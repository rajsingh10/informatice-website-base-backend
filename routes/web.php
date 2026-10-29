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

// Interactive API Testing Dashboard Page
Route::get('/', function () {
    return view('api-tester');
});

Route::get('/api-tester', function () {
    return view('api-tester');
});

// Direct aliases without /api prefix
Route::middleware([\App\Http\Middleware\ForceJsonResponse::class])->group(function () {

    // Public aliases
    Route::get('/public/layouts', [LayoutController::class, 'index']);
    Route::get('/public/seo/{page}', [SeoController::class, 'showPublic']);
    Route::get('/public/settings', [SiteSettingController::class, 'show']);
    Route::get('/public/navigation/navbar', [NavbarController::class, 'showPublic']);
    Route::get('/public/home-sections', [HomeSectionController::class, 'index']);
    Route::get('/public/services', [ServiceController::class, 'indexPublic']);
    Route::get('/public/services/{slug}', [ServiceController::class, 'showPublic']);
    Route::get('/public/portfolio', [PortfolioController::class, 'indexPublic']);
    Route::get('/public/portfolio/{slug}', [PortfolioController::class, 'showPublic']);
    Route::get('/public/blog', [BlogController::class, 'indexPublic']);
    Route::get('/public/blog/{slug}', [BlogController::class, 'showPublic']);

    // Admin Auth aliases (public)
    Route::post('/admin/login', [AuthController::class, 'login']);
    Route::post('/admin/auth/login', [AuthController::class, 'login']);

    // Admin aliases (protected)
    Route::middleware('auth:api')->group(function () {
        // Admin Auth & Profile
        Route::post('/admin/logout',                              [AuthController::class, 'logout']);
        Route::post('/admin/auth/logout',                         [AuthController::class, 'logout']);
        Route::get('/admin/me',                                   [AuthController::class, 'me']);
        Route::get('/admin/auth/me',                              [AuthController::class, 'me']);
        Route::match(['put', 'post'], '/admin/profile',           [AuthController::class, 'updateProfile']);
        Route::match(['put', 'post'], '/admin/auth/profile',      [AuthController::class, 'updateProfile']);
        Route::match(['put', 'post'], '/admin/change-password',      [AuthController::class, 'changePassword']);
        Route::match(['put', 'post'], '/admin/auth/change-password', [AuthController::class, 'changePassword']);

        // Module Layouts
        Route::post('/admin/layouts/save', [LayoutController::class, 'save']);
        Route::post('/admin/layouts', [LayoutController::class, 'store']);
        Route::put('/admin/layouts/{key}', [LayoutController::class, 'update']);
        Route::get('/admin/seo/{page}', [SeoController::class, 'showAdmin']);
        Route::put('/admin/seo/{page}', [SeoController::class, 'update']);
        Route::post('/admin/uploads', [MediaUploadController::class, 'store']);
        Route::delete('/admin/uploads/{id}', [MediaUploadController::class, 'destroy']);
        Route::get('/admin/settings', [SiteSettingController::class, 'show']);
        Route::put('/admin/settings', [SiteSettingController::class, 'update']);

        // Module 2 Navbar
        Route::get('/admin/navigation/navbar', [NavbarController::class, 'showAdmin']);
        Route::put('/admin/navigation/navbar/behavior', [NavbarController::class, 'updateBehavior']);
        Route::put('/admin/navigation/navbar/items', [NavbarController::class, 'updateItems']);
        Route::put('/admin/navigation/navbar/cta', [NavbarController::class, 'updateCta']);
        Route::post('/admin/navigation/navbar/reset', [NavbarController::class, 'reset']);

        // Module 3 Home Sections
        Route::put('/admin/home-sections/batch', [HomeSectionController::class, 'batch']);
        Route::post('/admin/home-sections/reset', [HomeSectionController::class, 'reset']);
        Route::put('/admin/home-sections/{sectionId}', [HomeSectionController::class, 'update']);

        // Module 4 Services
        Route::get('/admin/services', [ServiceController::class, 'indexAdmin']);
        Route::get('/admin/services/{id}', [ServiceController::class, 'showAdmin']);
        Route::post('/admin/services', [ServiceController::class, 'store']);
        Route::put('/admin/services/{id}', [ServiceController::class, 'update']);
        Route::delete('/admin/services/{id}', [ServiceController::class, 'destroy']);

        // Module 5 Portfolio
        Route::get('/admin/portfolio', [PortfolioController::class, 'indexAdmin']);
        Route::get('/admin/portfolio/{id}', [PortfolioController::class, 'showAdmin']);
        Route::post('/admin/portfolio', [PortfolioController::class, 'store']);
        Route::put('/admin/portfolio/{id}', [PortfolioController::class, 'update']);
        Route::delete('/admin/portfolio/{id}', [PortfolioController::class, 'destroy']);

        // Module 6 Blog
        Route::get('/admin/blog', [BlogController::class, 'indexAdmin']);
        Route::get('/admin/blog/{id}', [BlogController::class, 'showAdmin']);
        Route::post('/admin/blog', [BlogController::class, 'store']);
        Route::put('/admin/blog/{id}', [BlogController::class, 'update']);
        Route::delete('/admin/blog/{id}', [BlogController::class, 'destroy']);
    });
});
