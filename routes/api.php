<?php

use App\Http\Controllers\Admin\AdminModuleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeSectionController;
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\MediaUploadController;
use App\Http\Controllers\NavbarController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TopbarController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public direct aliases without prefix (e.g. /api/topbar, /api/services)
Route::get('topbar', [TopbarController::class, 'showPublic']);
Route::match(['put', 'post'], 'topbar', [TopbarController::class, 'saveTopbar']);
Route::match(['put', 'post'], 'topbar/toggle', [TopbarController::class, 'toggle']);
Route::get('topbar/items', [TopbarController::class, 'getItems']);
Route::get('topbar/socials', [TopbarController::class, 'getItems']);
Route::get('services', [ServiceController::class, 'indexPublic']);
Route::get('services/{slug}', [ServiceController::class, 'showPublic']);

// ═══════════════════════════════════════════════════════════
//  PUBLIC routes  (No Auth)
// ═══════════════════════════════════════════════════════════
Route::prefix('public')->group(function () {
    // Layouts
    Route::get('layouts', [LayoutController::class, 'index']);
    Route::match(['put', 'post'], 'layouts', [LayoutController::class, 'save']);

    // Module 1: Top Bar
    Route::get('topbar', [TopbarController::class, 'showPublic']);
    Route::match(['put', 'post'], 'topbar', [TopbarController::class, 'saveTopbar']);
    Route::match(['put', 'post'], 'topbar/toggle', [TopbarController::class, 'toggle']);
    Route::get('topbar/items', [TopbarController::class, 'getItems']);
    Route::get('topbar/socials', [TopbarController::class, 'getItems']);
    Route::get('navigation/topbar', [TopbarController::class, 'showPublic']);

    // 3.2 SEO
    Route::get('seo/{page}', [SeoController::class, 'showPublic']);

    // 3.4 Site Settings
    Route::get('settings', [SiteSettingController::class, 'show']);

    // Module 2: Navbar Navigation
    Route::get('navigation/navbar', [NavbarController::class, 'showPublic']);
    Route::match(['put', 'post'], 'navigation/navbar', [NavbarController::class, 'updateNavbar']);
    Route::match(['put', 'post'], 'navbar', [NavbarController::class, 'updateNavbar']);

    // Module 3: Home Sections
    Route::get('home-sections', [HomeSectionController::class, 'index']);
    Route::match(['put', 'post'], 'home-sections', [HomeSectionController::class, 'toggle']);
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

    // Module 7: Events
    Route::get('events', [EventController::class, 'indexPublic']);
    Route::get('events/{slug}', [EventController::class, 'showPublic']);

    // Module 8: Team
    Route::get('team', [TeamController::class, 'indexPublic']);
    Route::get('team/{id}', [TeamController::class, 'showPublic']);

    // Module 9: Testimonials
    Route::get('testimonials', [TestimonialController::class, 'indexPublic']);
    Route::get('testimonials/{id}', [TestimonialController::class, 'showPublic']);

    // Module 10: Gallery
    Route::get('gallery', [GalleryController::class, 'indexPublic']);
    Route::get('gallery/{id}', [GalleryController::class, 'showPublic']);
});

// ═══════════════════════════════════════════════════════════
//  ADMIN routes
// ═══════════════════════════════════════════════════════════
Route::prefix('admin')->group(function () {

    // ── Public Auth (Supports both /login and /auth/login) ──
    Route::post('login', [AuthController::class, 'login']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // ── Protected (Requires Bearer token) ──────────────────
    Route::middleware('auth:api')->group(function () {
        // Auth session & profile
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::match(['put', 'post'], 'profile', [AuthController::class, 'updateProfile']);
        Route::match(['put', 'post'], 'auth/profile', [AuthController::class, 'updateProfile']);
        Route::match(['put', 'post'], 'change-password', [AuthController::class, 'changePassword']);
        Route::match(['put', 'post'], 'auth/change-password', [AuthController::class, 'changePassword']);

        // Direct Module Admin GET APIs (e.g. /api/admin/topbar)
        Route::get('topbar', [AdminModuleController::class, 'getTopbar']);
        Route::get('navbar', [AdminModuleController::class, 'getNavbar']);
        Route::get('home', [AdminModuleController::class, 'getHome']);
        Route::get('service-detail', [AdminModuleController::class, 'getServiceDetail']);
        Route::get('portfolio-detail', [AdminModuleController::class, 'getPortfolioDetail']);
        Route::get('blog-detail', [AdminModuleController::class, 'getBlogDetail']);
        Route::get('events-feed', [AdminModuleController::class, 'getEvents']);
        Route::get('event-detail', [AdminModuleController::class, 'getEventDetail']);
        Route::get('team-feed', [AdminModuleController::class, 'getTeam']);
        Route::get('testimonials-feed', [AdminModuleController::class, 'getTestimonials']);
        Route::get('gallery-feed', [AdminModuleController::class, 'getGallery']);

        // Module-wise Admin GET APIs (Backward compatibility aliases)
        Route::prefix('modules')->group(function () {
            Route::get('topbar', [AdminModuleController::class, 'getTopbar']);
            Route::get('topbar/items', [TopbarController::class, 'getItems']);
            Route::get('topbar/socials', [TopbarController::class, 'getItems']);
            Route::get('navbar', [AdminModuleController::class, 'getNavbar']);
            Route::get('navigation/navbar', [AdminModuleController::class, 'getNavbar']);
            Route::get('home', [AdminModuleController::class, 'getHome']);
            Route::get('home-sections', [AdminModuleController::class, 'getHome']);
            Route::get('services', [AdminModuleController::class, 'getServices']);
            Route::get('api/public/services', [AdminModuleController::class, 'getServices']);
            Route::get('service-detail', [AdminModuleController::class, 'getServiceDetail']);
            Route::get('portfolio', [AdminModuleController::class, 'getPortfolio']);
            Route::get('portfolio-detail', [AdminModuleController::class, 'getPortfolioDetail']);
            Route::get('blog', [AdminModuleController::class, 'getBlog']);
            Route::get('blog-detail', [AdminModuleController::class, 'getBlogDetail']);
            Route::get('events', [AdminModuleController::class, 'getEvents']);
            Route::get('event-detail', [AdminModuleController::class, 'getEventDetail']);
            Route::get('team', [AdminModuleController::class, 'getTeam']);
            Route::get('testimonials', [AdminModuleController::class, 'getTestimonials']);
            Route::get('gallery', [AdminModuleController::class, 'getGallery']);
            Route::get('settings', [AdminModuleController::class, 'getSettings']);
            Route::get('layouts', [AdminModuleController::class, 'getLayouts']);
        });

        // Layouts (Modules 1, 2, 3, 4, 5 etc.)
        Route::get('layouts', [LayoutController::class, 'indexAdmin']);
        Route::post('layouts/save', [LayoutController::class, 'save']);
        Route::match(['put', 'post'], 'layouts', [LayoutController::class, 'store']);
        Route::match(['put', 'post'], 'layouts/{key}', [LayoutController::class, 'update']);

        // 3.2 SEO
        Route::get('seo/{page}', [SeoController::class, 'showAdmin']);
        Route::put('seo/{page}', [SeoController::class, 'update']);

        // 3.3 Media Uploads
        Route::post('uploads', [MediaUploadController::class, 'store']);
        Route::delete('uploads/{id}', [MediaUploadController::class, 'destroy']);

        // 3.4 Site Settings
        Route::get('settings', [SiteSettingController::class, 'show']);
        Route::put('settings', [SiteSettingController::class, 'update']);

        // Module 1: Top Bar (Admin CRUD, items, toggle, delete)
        Route::get('topbar', [TopbarController::class, 'showAdmin']);
        Route::match(['put', 'post', 'patch'], 'topbar', [TopbarController::class, 'saveTopbar']);
        Route::delete('topbar', [TopbarController::class, 'destroy']);
        Route::match(['put', 'post', 'patch'], 'topbar/toggle', [TopbarController::class, 'toggle']);

        // Topbar specific sections
        Route::match(['put', 'post', 'patch'], 'topbar/contact', [TopbarController::class, 'updateContact']);
        Route::match(['put', 'post', 'patch'], 'topbar/notice', [TopbarController::class, 'updateNotice']);
        Route::match(['put', 'post', 'patch'], 'topbar/announcement', [TopbarController::class, 'updateNotice']);
        Route::match(['put', 'post', 'patch'], 'topbar/notice/toggle', [TopbarController::class, 'toggleNotice']);
        Route::match(['put', 'post', 'patch'], 'topbar/cta', [TopbarController::class, 'updateCta']);
        Route::match(['put', 'post', 'patch'], 'topbar/cta/toggle', [TopbarController::class, 'toggleCta']);

        // Topbar items / socials CRUD (Add, Edit, Toggle, Delete)
        Route::get('topbar/items', [TopbarController::class, 'getItems']);
        Route::post('topbar/items', [TopbarController::class, 'addItem']);
        Route::post('topbar/item', [TopbarController::class, 'addItem']);
        Route::match(['put', 'post', 'patch'], 'topbar/items/{id}', [TopbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], 'topbar/item/{id}', [TopbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], 'topbar/items/{id}/toggle', [TopbarController::class, 'toggleItem']);
        Route::match(['delete', 'post'], 'topbar/items/{id}', [TopbarController::class, 'deleteItem']);
        Route::match(['delete', 'post'], 'topbar/items/{id}/delete', [TopbarController::class, 'deleteItem']);

        // Socials alias routes
        Route::get('topbar/socials', [TopbarController::class, 'getItems']);
        Route::post('topbar/socials', [TopbarController::class, 'addItem']);
        Route::post('topbar/social', [TopbarController::class, 'addItem']);
        Route::match(['put', 'post', 'patch'], 'topbar/socials/{id}', [TopbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], 'topbar/socials/{id}/toggle', [TopbarController::class, 'toggleItem']);
        Route::match(['delete', 'post'], 'topbar/socials/{id}', [TopbarController::class, 'deleteItem']);

        // Navigation topbar aliases
        Route::get('navigation/topbar', [TopbarController::class, 'showAdmin']);
        Route::match(['put', 'post', 'patch'], 'navigation/topbar', [TopbarController::class, 'saveTopbar']);
        Route::delete('navigation/topbar', [TopbarController::class, 'destroy']);
        Route::match(['put', 'post', 'patch'], 'navigation/topbar/toggle', [TopbarController::class, 'toggle']);

        // Module 2: Navbar Navigation
        Route::get('navigation/navbar', [NavbarController::class, 'showAdmin']);
        Route::match(['put', 'post'], 'navigation/navbar', [NavbarController::class, 'updateNavbar']);
        Route::match(['put', 'post'], 'navbar', [NavbarController::class, 'updateNavbar']);
        Route::put('navigation/navbar/behavior', [NavbarController::class, 'updateBehavior']);
        Route::put('navigation/navbar/items', [NavbarController::class, 'updateItems']);
        Route::put('navigation/navbar/cta', [NavbarController::class, 'updateCta']);
        Route::put('navbar/behavior',            [NavbarController::class, 'updateBehavior']);
        Route::put('navigation/navbar/cta',    [NavbarController::class, 'updateCta']);
        Route::put('navbar/cta',               [NavbarController::class, 'updateCta']);
        Route::post('navigation/navbar/reset', [NavbarController::class, 'reset']);
        Route::post('navbar/reset',            [NavbarController::class, 'reset']);

        // Navbar Menu Links Manager (List, Add, Edit, Toggle Show/Hide, Reorder, Delete)
        Route::get('navigation/navbar/items',  [NavbarController::class, 'getItems']);
        Route::get('navbar/items',             [NavbarController::class, 'getItems']);
        Route::post('navigation/navbar/items', [NavbarController::class, 'addItem']);
        Route::post('navbar/items',            [NavbarController::class, 'addItem']);
        Route::post('navigation/navbar/item',  [NavbarController::class, 'addItem']);
        Route::post('navbar/item',             [NavbarController::class, 'addItem']);
        Route::put('navigation/navbar/items',  [NavbarController::class, 'updateItems']);
        Route::put('navbar/items',             [NavbarController::class, 'updateItems']);
        Route::match(['put', 'post', 'patch'], 'navigation/navbar/items/{id}', [NavbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], 'navbar/items/{id}',            [NavbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], 'navigation/navbar/item/{id}',  [NavbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], 'navbar/item/{id}',             [NavbarController::class, 'updateItem']);
        Route::match(['put', 'post'], 'navigation/navbar/items/{id}/toggle',   [NavbarController::class, 'toggleItem']);
        Route::match(['put', 'post'], 'navbar/items/{id}/toggle',              [NavbarController::class, 'toggleItem']);
        Route::match(['put', 'post'], 'navigation/navbar/items/reorder',       [NavbarController::class, 'reorderItems']);
        Route::match(['put', 'post'], 'navbar/items/reorder',                  [NavbarController::class, 'reorderItems']);
        Route::match(['put', 'post'], 'navigation/navbar/items/{id}/move-up',   [NavbarController::class, 'moveUp']);
        Route::match(['put', 'post'], 'navigation/navbar/items/{id}/move-down', [NavbarController::class, 'moveDown']);
        Route::match(['put', 'post'], 'navbar/items/{id}/move-up',              [NavbarController::class, 'moveUp']);
        Route::match(['put', 'post'], 'navbar/items/{id}/move-down',            [NavbarController::class, 'moveDown']);
        Route::match(['delete', 'post'], 'navigation/navbar/items/{id}',        [NavbarController::class, 'deleteItem']);
        Route::match(['delete', 'post'], 'navbar/items/{id}',                   [NavbarController::class, 'deleteItem']);
        Route::match(['delete', 'post'], 'navigation/navbar/items/{id}/delete', [NavbarController::class, 'deleteItem']);
        Route::match(['delete', 'post'], 'navbar/items/{id}/delete',            [NavbarController::class, 'deleteItem']);

        // Module 3: Home Sections (GET & POST on/off toggle APIs)
        Route::get('home-sections', [HomeSectionController::class, 'index']);
        Route::match(['put', 'post'], 'home-sections', [HomeSectionController::class, 'toggle']);
        Route::match(['put', 'post'], 'home-sections/toggle', [HomeSectionController::class, 'toggle']);
        Route::match(['put', 'post'], 'home-sections/batch', [HomeSectionController::class, 'batch']);
        Route::post('home-sections/reset', [HomeSectionController::class, 'reset']);
        Route::match(['put', 'post'], 'home-sections/{sectionId}', [HomeSectionController::class, 'toggle']);
        Route::match(['put', 'post'], 'home', [HomeSectionController::class, 'toggle']);

        // Module 4: Services CRUD
        Route::get('services',                 [ServiceController::class, 'indexAdmin']);
        Route::get('services/{id}',            [ServiceController::class, 'showAdmin']);
        Route::post('services',                [ServiceController::class, 'store']);
        Route::match(['put', 'post', 'patch'], 'services/{id}', [ServiceController::class, 'update']);
        Route::delete('services/{id}',         [ServiceController::class, 'destroy']);

        // Module 5: Portfolio CRUD
        Route::get('portfolio', [PortfolioController::class, 'indexAdmin']);
        Route::get('portfolio/{id}', [PortfolioController::class, 'showAdmin']);
        Route::post('portfolio', [PortfolioController::class, 'store']);
        Route::put('portfolio/{id}', [PortfolioController::class, 'update']);
        Route::delete('portfolio/{id}', [PortfolioController::class, 'destroy']);

        // Module 6: Blog CRUD
        Route::get('blog', [BlogController::class, 'indexAdmin']);
        Route::get('blog/{id}', [BlogController::class, 'showAdmin']);
        Route::post('blog', [BlogController::class, 'store']);
        Route::put('blog/{id}', [BlogController::class, 'update']);
        Route::delete('blog/{id}', [BlogController::class, 'destroy']);

        // Module 7: Events CRUD
        Route::get('events', [EventController::class, 'indexAdmin']);
        Route::get('events/{id}', [EventController::class, 'showAdmin']);
        Route::post('events', [EventController::class, 'store']);
        Route::put('events/{id}', [EventController::class, 'update']);
        Route::delete('events/{id}', [EventController::class, 'destroy']);

        // Module 8: Team CRUD
        Route::get('team', [TeamController::class, 'indexAdmin']);
        Route::get('team/{id}', [TeamController::class, 'showAdmin']);
        Route::post('team', [TeamController::class, 'store']);
        Route::put('team/{id}', [TeamController::class, 'update']);
        Route::delete('team/{id}', [TeamController::class, 'destroy']);

        // Module 9: Testimonials CRUD
        Route::get('testimonials', [TestimonialController::class, 'indexAdmin']);
        Route::get('testimonials/{id}', [TestimonialController::class, 'showAdmin']);
        Route::post('testimonials', [TestimonialController::class, 'store']);
        Route::put('testimonials/{id}', [TestimonialController::class, 'update']);
        Route::delete('testimonials/{id}', [TestimonialController::class, 'destroy']);

        // Module 10: Gallery CRUD
        Route::get('gallery', [GalleryController::class, 'indexAdmin']);
        Route::get('gallery/{id}', [GalleryController::class, 'showAdmin']);
        Route::post('gallery', [GalleryController::class, 'store']);
        Route::put('gallery/{id}', [GalleryController::class, 'update']);
        Route::delete('gallery/{id}', [GalleryController::class, 'destroy']);
    });
});
