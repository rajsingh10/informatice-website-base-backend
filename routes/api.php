<?php

use App\Http\Controllers\Admin\AdminModuleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeSectionController;
use App\Http\Controllers\InquiryController;
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
    Route::get('navigation/navbar',                     [NavbarController::class, 'showPublic']);
    Route::match(['put', 'post'], 'navigation/navbar',  [NavbarController::class, 'updateNavbar']);
    Route::match(['put', 'post'], 'navbar',             [NavbarController::class, 'updateNavbar']);

    // Module 3: Home Sections
    Route::get('home-sections',                         [HomeSectionController::class, 'index']);
    Route::match(['put', 'post'], 'home-sections',      [HomeSectionController::class, 'toggle']);
    Route::match(['put', 'post'], 'home-sections/{sectionId}', [HomeSectionController::class, 'toggle']);

    // Module 4: Services
    Route::get('services', [ServiceController::class, 'indexPublic']);
    Route::get('services/{slug}', [ServiceController::class, 'showPublic']);

    // Module 5: Portfolio
    Route::get('portfolio', [PortfolioController::class, 'indexPublic']);
    Route::get('portfolio/{slug}', [PortfolioController::class, 'showPublic']);

    // Module 6: Blog
    Route::get('blog', [BlogController::class, 'indexPublic']);
    Route::get('blog/{slug}', [BlogController::class, 'showPublic']);

    // Module 11: Contact Page & Inquiry Forms
    Route::get('contact', [InquiryController::class, 'getPublicContact']);
    Route::post('contact/inquiry', [InquiryController::class, 'submitInquiry']);
    Route::post('inquiry', [InquiryController::class, 'submitInquiry']);
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

        // Direct Module Admin GET APIs (e.g. /api/admin/topbar)
        Route::get('topbar',                   [AdminModuleController::class, 'getTopbar']);
        Route::get('navbar',                   [AdminModuleController::class, 'getNavbar']);
        Route::get('home',                     [AdminModuleController::class, 'getHome']);
        Route::get('service-detail',           [AdminModuleController::class, 'getServiceDetail']);
        Route::get('portfolio-detail',         [AdminModuleController::class, 'getPortfolioDetail']);
        Route::get('blog-detail',              [AdminModuleController::class, 'getBlogDetail']);
        Route::get('contact',                  [AdminModuleController::class, 'getContact']);
        Route::match(['put', 'post'], 'contact', [InquiryController::class, 'updateContactInfo']);
        Route::match(['put', 'post'], 'contact/info', [InquiryController::class, 'updateContactInfo']);

        // Module-wise Admin GET APIs (Backward compatibility aliases)
        Route::prefix('modules')->group(function () {
            Route::get('topbar',                   [AdminModuleController::class, 'getTopbar']);
            Route::get('navbar',                   [AdminModuleController::class, 'getNavbar']);
            Route::get('navigation/navbar',        [AdminModuleController::class, 'getNavbar']);
            Route::get('home',                     [AdminModuleController::class, 'getHome']);
            Route::get('home-sections',            [AdminModuleController::class, 'getHome']);
            Route::get('services',                 [AdminModuleController::class, 'getServices']);
            Route::get('api/public/services',      [AdminModuleController::class, 'getServices']);
            Route::get('service-detail',           [AdminModuleController::class, 'getServiceDetail']);
            Route::get('portfolio',                [AdminModuleController::class, 'getPortfolio']);
            Route::get('portfolio-detail',         [AdminModuleController::class, 'getPortfolioDetail']);
            Route::get('blog',                     [AdminModuleController::class, 'getBlog']);
            Route::get('blog-detail',              [AdminModuleController::class, 'getBlogDetail']);
            Route::get('contact',                  [AdminModuleController::class, 'getContact']);
            Route::match(['put', 'post'], 'contact', [InquiryController::class, 'updateContactInfo']);
            Route::match(['put', 'post'], 'contact/info', [InquiryController::class, 'updateContactInfo']);
            Route::get('settings',                 [AdminModuleController::class, 'getSettings']);
            Route::get('layouts',                  [AdminModuleController::class, 'getLayouts']);
        });

        // Layouts (Modules 1, 2, 3, 4, 5 etc.)
        Route::get('layouts',                  [LayoutController::class, 'indexAdmin']);
        Route::post('layouts/save',            [LayoutController::class, 'save']);
        Route::match(['put', 'post'], 'layouts', [LayoutController::class, 'store']);
        Route::match(['put', 'post'], 'layouts/{key}', [LayoutController::class, 'update']);

        // 3.2 SEO
        Route::get('seo/{page}',               [SeoController::class, 'showAdmin']);
        Route::match(['put', 'post'], 'seo/{page}', [SeoController::class, 'update']);

        // 3.3 Media Uploads
        Route::post('uploads',                 [MediaUploadController::class, 'store']);
        Route::delete('uploads/{id}',          [MediaUploadController::class, 'destroy']);

        // 3.4 Site Settings
        Route::get('settings',                 [SiteSettingController::class, 'show']);
        Route::put('settings',                 [SiteSettingController::class, 'update']);

        // Module 2: Navbar Navigation
        Route::get('navigation/navbar',        [NavbarController::class, 'showAdmin']);
        Route::match(['put', 'post'], 'navigation/navbar', [NavbarController::class, 'updateNavbar']);
        Route::match(['put', 'post'], 'navbar',            [NavbarController::class, 'updateNavbar']);
        Route::put('navigation/navbar/behavior', [NavbarController::class, 'updateBehavior']);
        Route::put('navigation/navbar/items',  [NavbarController::class, 'updateItems']);
        Route::put('navigation/navbar/cta',    [NavbarController::class, 'updateCta']);
        Route::post('navigation/navbar/reset', [NavbarController::class, 'reset']);

        // Module 3: Home Sections (GET & POST on/off toggle APIs)
        Route::get('home-sections',                    [HomeSectionController::class, 'index']);
        Route::match(['put', 'post'], 'home-sections', [HomeSectionController::class, 'toggle']);
        Route::match(['put', 'post'], 'home-sections/toggle', [HomeSectionController::class, 'toggle']);
        Route::match(['put', 'post'], 'home-sections/batch',  [HomeSectionController::class, 'batch']);
        Route::post('home-sections/reset',                    [HomeSectionController::class, 'reset']);
        Route::match(['put', 'post'], 'home-sections/{sectionId}', [HomeSectionController::class, 'toggle']);
        Route::match(['put', 'post'], 'home',                 [HomeSectionController::class, 'toggle']);

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

        // Module 11: Contact Inquiries CRUD
        Route::get('inquiries',                [InquiryController::class, 'index']);
        Route::get('inquiries/{id}',           [InquiryController::class, 'show']);
        Route::match(['put', 'patch', 'post'], 'inquiries/{id}', [InquiryController::class, 'updateStatus']);
        Route::delete('inquiries/{id}',        [InquiryController::class, 'destroy']);
    });
});
