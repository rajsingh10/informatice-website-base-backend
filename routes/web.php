<?php

use App\Http\Controllers\Admin\AdminModuleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeSectionController;
use App\Http\Controllers\InquiryController; // [Contact Page API]
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\MediaUploadController;
use App\Http\Controllers\NavbarController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TopbarController;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Interactive API Testing Dashboard / Root Services API
Route::get('/', function (Request $request) {
    // If request wants JSON (API request, fetch, axios, or not a full browser page navigation)
    $isBrowserDocument = str_contains($request->header('Accept', ''), 'text/html')
        && ($request->header('Sec-Fetch-Dest', '') === 'document' || ! $request->hasHeader('Sec-Fetch-Dest'))
        && ! $request->hasHeader('X-Requested-With')
        && ! $request->has('json');

    if (! $isBrowserDocument || $request->wantsJson() || $request->ajax() || $request->has('json')) {
        return app(ServiceController::class)->indexPublic($request);
    }

    return view('api-tester');
});

Route::get('/api-tester', function () {
    return view('api-tester');
});

// Direct file serving for /services/public/... assets (ONLY actual static files)
Route::get('/services/public/{path}', function ($path) {
    $fullPath = public_path($path);
    if (! file_exists($fullPath) || is_dir($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*\.(css|js|png|jpg|jpeg|gif|svg|ico|webp|woff|woff2|ttf|eot|map)$');

// Direct aliases without /api prefix
Route::middleware([ForceJsonResponse::class])->group(function () {

    // Direct services aliases
    Route::get('/services', [ServiceController::class, 'indexPublic']);
    Route::get('/services/{slug}', [ServiceController::class, 'showPublic']);

    // Module 1: Top Bar public aliases
    Route::get('/topbar', [TopbarController::class, 'showPublic']);
    Route::match(['put', 'post'], '/topbar', [TopbarController::class, 'saveTopbar']);
    Route::match(['put', 'post'], '/topbar/toggle', [TopbarController::class, 'toggle']);
    Route::get('/topbar/items', [TopbarController::class, 'getItems']);
    Route::get('/topbar/socials', [TopbarController::class, 'getItems']);
    Route::get('/public/topbar', [TopbarController::class, 'showPublic']);
    Route::match(['put', 'post'], '/public/topbar', [TopbarController::class, 'saveTopbar']);
    Route::match(['put', 'post'], '/public/topbar/toggle', [TopbarController::class, 'toggle']);
    Route::get('/public/topbar/items', [TopbarController::class, 'getItems']);
    Route::get('/public/topbar/socials', [TopbarController::class, 'getItems']);
    Route::get('/public/navigation/topbar', [TopbarController::class, 'showPublic']);
    Route::get('/public/navigation/topbar/items', [TopbarController::class, 'getItems']);

    // Public aliases
    Route::get('/public/layouts', [LayoutController::class, 'index']);
    Route::match(['put', 'post'], '/public/layouts', [LayoutController::class, 'save']);
    Route::get('/public/seo/{page}', [SeoController::class, 'showPublic']);
    Route::get('/public/settings', [SiteSettingController::class, 'show']);
    Route::get('/public/navigation/navbar', [NavbarController::class, 'showPublic']);
    Route::match(['put', 'post'], '/public/navigation/navbar', [NavbarController::class, 'updateNavbar']);
    Route::match(['put', 'post'], '/public/navbar', [NavbarController::class, 'updateNavbar']);
    Route::get('/public/home-sections', [HomeSectionController::class, 'index']);
    Route::match(['put', 'post'], '/public/home-sections', [HomeSectionController::class, 'toggle']);
    Route::match(['put', 'post'], '/public/home-sections/{sectionId}', [HomeSectionController::class, 'toggle']);
    Route::get('/public/services', [ServiceController::class, 'indexPublic']);
    Route::get('/public/services/{slug}', [ServiceController::class, 'showPublic']);
    Route::get('/public/portfolio', [PortfolioController::class, 'indexPublic']);
    Route::get('/public/portfolio/{slug}', [PortfolioController::class, 'showPublic']);
    Route::get('/public/blog', [BlogController::class, 'indexPublic']);
    Route::get('/public/blog/{slug}', [BlogController::class, 'showPublic']);

    // Module 7: Events
    Route::get('/public/events', [EventController::class, 'indexPublic']);
    Route::get('/public/events/{slug}', [EventController::class, 'showPublic']);

    // Module 8: Team
    Route::get('/public/team', [TeamController::class, 'indexPublic']);
    Route::get('/public/team/{id}', [TeamController::class, 'showPublic']);

    // ─── Contact Page API (Module 11) ───
    Route::get('/public/contact', [InquiryController::class, 'getPublicContact']); // Contact Page API
    Route::post('/public/contact/inquiry', [InquiryController::class, 'submitInquiry']); // Contact Page API
    Route::post('/public/inquiry', [InquiryController::class, 'submitInquiry']); // Contact Page API
    // Module 9: Testimonials
    Route::get('/public/testimonials', [TestimonialController::class, 'indexPublic']);
    Route::get('/public/testimonials/{id}', [TestimonialController::class, 'showPublic']);

    // Module 10: Gallery
    Route::get('/public/gallery', [GalleryController::class, 'indexPublic']);
    Route::get('/public/gallery/{id}', [GalleryController::class, 'showPublic']);

    // Module 13: Careers public aliases
    Route::get('/careers', [CareerController::class, 'indexPublic']);
    Route::get('/careers/{slug}', [CareerController::class, 'showPublic']);
    Route::get('/public/careers', [CareerController::class, 'indexPublic']);
    Route::get('/public/careers/{slug}', [CareerController::class, 'showPublic']);

    // Module 14: FAQs public aliases
    Route::get('/faqs', [FaqController::class, 'indexPublic']);
    Route::get('/public/faqs', [FaqController::class, 'indexPublic']);

    // Module 15 & 16: Legal Pages public aliases
    Route::get('/legal/{type}', [LegalPageController::class, 'showPublic']);
    Route::get('/public/legal/{type}', [LegalPageController::class, 'showPublic']);
    Route::get('/terms', [LegalPageController::class, 'showTermsPublic']);
    Route::get('/public/terms', [LegalPageController::class, 'showTermsPublic']);
    Route::get('/privacy', [LegalPageController::class, 'showPrivacyPublic']);
    Route::get('/public/privacy', [LegalPageController::class, 'showPrivacyPublic']);

    // Admin Auth aliases (public)
    Route::post('/admin/login', [AuthController::class, 'login']);
    Route::post('/admin/auth/login', [AuthController::class, 'login']);

    // Admin aliases (protected)
    Route::middleware('auth:api')->group(function () {
        // Admin Auth & Profile
        Route::post('/admin/logout', [AuthController::class, 'logout']);
        Route::post('/admin/auth/logout', [AuthController::class, 'logout']);
        Route::get('/admin/me', [AuthController::class, 'me']);
        Route::get('/admin/auth/me', [AuthController::class, 'me']);
        Route::match(['put', 'post'], '/admin/profile', [AuthController::class, 'updateProfile']);
        Route::match(['put', 'post'], '/admin/auth/profile', [AuthController::class, 'updateProfile']);
        Route::match(['put', 'post'], '/admin/change-password', [AuthController::class, 'changePassword']);
        Route::match(['put', 'post'], '/admin/auth/change-password', [AuthController::class, 'changePassword']);

        // Direct Module Admin GET API aliases without /modules/ prefix
        Route::get('/admin/topbar', [AdminModuleController::class, 'getTopbar']);
        Route::get('/admin/navbar', [AdminModuleController::class, 'getNavbar']);
        Route::get('/admin/home', [AdminModuleController::class, 'getHome']);
        Route::get('/admin/service-detail', [AdminModuleController::class, 'getServiceDetail']);
        Route::get('/admin/portfolio-detail', [AdminModuleController::class, 'getPortfolioDetail']);
        Route::get('/admin/blog-detail', [AdminModuleController::class, 'getBlogDetail']);
        Route::get('/admin/events-feed', [AdminModuleController::class, 'getEvents']);
        Route::get('/admin/event-detail', [AdminModuleController::class, 'getEventDetail']);
        Route::get('/admin/team-feed', [AdminModuleController::class, 'getTeam']);
        Route::get('/admin/testimonials-feed', [AdminModuleController::class, 'getTestimonials']);
        Route::get('/admin/gallery-feed', [AdminModuleController::class, 'getGallery']);
        Route::get('/admin/about-feed', [AdminModuleController::class, 'getAbout']);
        Route::get('/admin/career', [AdminModuleController::class, 'getCareer']);
        Route::get('/admin/faq', [AdminModuleController::class, 'getFaq']);
        Route::get('/admin/terms', [AdminModuleController::class, 'getTerms']);
        Route::get('/admin/privacy', [AdminModuleController::class, 'getPrivacy']);

        // Contact Page API (Module 11)
        Route::get('/admin/contact', [AdminModuleController::class, 'getContact']); // Contact Page API
        Route::match(['put', 'post'], '/admin/contact', [InquiryController::class, 'updateContactInfo']); // Contact Page API
        Route::match(['put', 'post'], '/admin/contact/info', [InquiryController::class, 'updateContactInfo']); // Contact Page API

        // Module-wise Admin GET API aliases with /admin/modules/ prefix
        Route::prefix('/admin/modules')->group(function () {
            Route::get('topbar', [AdminModuleController::class, 'getTopbar']);
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

            // Contact Page API (Module 11)
            Route::get('contact', [AdminModuleController::class, 'getContact']); // Contact Page API
            Route::match(['put', 'post'], 'contact', [InquiryController::class, 'updateContactInfo']); // Contact Page API
            Route::match(['put', 'post'], 'contact/info', [InquiryController::class, 'updateContactInfo']); // Contact Page API

            Route::get('testimonials', [AdminModuleController::class, 'getTestimonials']);
            Route::get('gallery', [AdminModuleController::class, 'getGallery']);
            Route::get('about', [AdminModuleController::class, 'getAbout']);
            Route::get('career', [AdminModuleController::class, 'getCareer']);
            Route::get('careers', [AdminModuleController::class, 'getCareer']);
            Route::get('faq', [AdminModuleController::class, 'getFaq']);
            Route::get('faqs', [AdminModuleController::class, 'getFaq']);
            Route::get('terms', [AdminModuleController::class, 'getTerms']);
            Route::get('privacy', [AdminModuleController::class, 'getPrivacy']);
            Route::get('settings', [AdminModuleController::class, 'getSettings']);
            Route::get('layouts', [AdminModuleController::class, 'getLayouts']);
        });

        // Module Layouts
        Route::get('/admin/layouts', [LayoutController::class, 'indexAdmin']);
        Route::post('/admin/layouts/save', [LayoutController::class, 'save']);
        Route::match(['put', 'post'], '/admin/layouts', [LayoutController::class, 'store']);
        Route::match(['put', 'post'], '/admin/layouts/{key}', [LayoutController::class, 'update']);
        Route::get('/admin/seo/{page}', [SeoController::class, 'showAdmin']);
        Route::match(['put', 'post'], '/admin/seo/{page}', [SeoController::class, 'update']);
        Route::post('/admin/uploads', [MediaUploadController::class, 'store']);
        Route::delete('/admin/uploads/{id}', [MediaUploadController::class, 'destroy']);
        Route::get('/admin/settings', [SiteSettingController::class, 'show']);
        Route::put('/admin/settings', [SiteSettingController::class, 'update']);

        // Module 1 Topbar (CRUD, items, toggle, delete)
        Route::match(['put', 'post', 'patch'], '/admin/topbar', [TopbarController::class, 'saveTopbar']);
        Route::delete('/admin/topbar', [TopbarController::class, 'destroy']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/toggle', [TopbarController::class, 'toggle']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/contact', [TopbarController::class, 'updateContact']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/notice', [TopbarController::class, 'updateNotice']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/announcement', [TopbarController::class, 'updateNotice']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/notice/toggle', [TopbarController::class, 'toggleNotice']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/cta', [TopbarController::class, 'updateCta']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/cta/toggle', [TopbarController::class, 'toggleCta']);

        // Topbar items / socials CRUD
        Route::get('/admin/topbar/items', [TopbarController::class, 'getItems']);
        Route::post('/admin/topbar/items', [TopbarController::class, 'addItem']);
        Route::post('/admin/topbar/item', [TopbarController::class, 'addItem']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/items/{id}', [TopbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/item/{id}', [TopbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/items/{id}/toggle', [TopbarController::class, 'toggleItem']);
        Route::match(['delete', 'post'], '/admin/topbar/items/{id}', [TopbarController::class, 'deleteItem']);
        Route::match(['delete', 'post'], '/admin/topbar/items/{id}/delete', [TopbarController::class, 'deleteItem']);

        // Topbar socials alias
        Route::get('/admin/topbar/socials', [TopbarController::class, 'getItems']);
        Route::post('/admin/topbar/socials', [TopbarController::class, 'addItem']);
        Route::post('/admin/topbar/social', [TopbarController::class, 'addItem']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/socials/{id}', [TopbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], '/admin/topbar/socials/{id}/toggle', [TopbarController::class, 'toggleItem']);
        Route::match(['delete', 'post'], '/admin/topbar/socials/{id}', [TopbarController::class, 'deleteItem']);

        Route::get('/admin/navigation/topbar', [TopbarController::class, 'showAdmin']);
        Route::match(['put', 'post', 'patch'], '/admin/navigation/topbar', [TopbarController::class, 'saveTopbar']);
        Route::delete('/admin/navigation/topbar', [TopbarController::class, 'destroy']);
        Route::match(['put', 'post', 'patch'], '/admin/navigation/topbar/toggle', [TopbarController::class, 'toggle']);

        // Module 2 Navbar
        Route::get('/admin/navigation/navbar', [NavbarController::class, 'showAdmin']);
        Route::match(['put', 'post'], '/admin/navigation/navbar', [NavbarController::class, 'updateNavbar']);
        Route::match(['put', 'post'], '/admin/navbar', [NavbarController::class, 'updateNavbar']);
        Route::put('/admin/navigation/navbar/behavior', [NavbarController::class, 'updateBehavior']);
        Route::put('/admin/navigation/navbar/items', [NavbarController::class, 'updateItems']);
        Route::put('/admin/navigation/navbar/cta', [NavbarController::class, 'updateCta']);
        Route::put('/admin/navbar/behavior', [NavbarController::class, 'updateBehavior']);
        Route::put('/admin/navbar/cta', [NavbarController::class, 'updateCta']);
        Route::post('/admin/navigation/navbar/reset', [NavbarController::class, 'reset']);
        Route::post('/admin/navbar/reset', [NavbarController::class, 'reset']);

        // Navbar Menu Links Manager (List, Add, Edit, Toggle Show/Hide, Reorder, Delete)
        Route::get('/admin/navigation/navbar/items', [NavbarController::class, 'getItems']);
        Route::get('/admin/navbar/items', [NavbarController::class, 'getItems']);
        Route::post('/admin/navigation/navbar/items', [NavbarController::class, 'addItem']);
        Route::post('/admin/navbar/items', [NavbarController::class, 'addItem']);
        Route::post('/admin/navigation/navbar/items/{parentId}/subitems', [NavbarController::class, 'addSubItem']);
        Route::post('/admin/navbar/items/{parentId}/subitems', [NavbarController::class, 'addSubItem']);
        Route::post('/admin/navigation/navbar/items/{parentId}/submenu', [NavbarController::class, 'addSubItem']);
        Route::post('/admin/navbar/items/{parentId}/submenu', [NavbarController::class, 'addSubItem']);
        Route::post('/admin/navigation/navbar/item', [NavbarController::class, 'addItem']);
        Route::post('/admin/navbar/item', [NavbarController::class, 'addItem']);
        Route::put('/admin/navigation/navbar/items', [NavbarController::class, 'updateItems']);
        Route::put('/admin/navbar/items', [NavbarController::class, 'updateItems']);
        Route::match(['put', 'post', 'patch'], '/admin/navigation/navbar/items/{id}', [NavbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], '/admin/navbar/items/{id}', [NavbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], '/admin/navigation/navbar/item/{id}', [NavbarController::class, 'updateItem']);
        Route::match(['put', 'post', 'patch'], '/admin/navbar/item/{id}', [NavbarController::class, 'updateItem']);
        Route::match(['put', 'post'], '/admin/navigation/navbar/items/{id}/toggle', [NavbarController::class, 'toggleItem']);
        Route::match(['put', 'post'], '/admin/navbar/items/{id}/toggle', [NavbarController::class, 'toggleItem']);
        Route::match(['put', 'post'], '/admin/navigation/navbar/items/reorder', [NavbarController::class, 'reorderItems']);
        Route::match(['put', 'post'], '/admin/navbar/items/reorder', [NavbarController::class, 'reorderItems']);
        Route::match(['put', 'post'], '/admin/navigation/navbar/items/{id}/move-up', [NavbarController::class, 'moveUp']);
        Route::match(['put', 'post'], '/admin/navigation/navbar/items/{id}/move-down', [NavbarController::class, 'moveDown']);
        Route::match(['put', 'post'], '/admin/navbar/items/{id}/move-up', [NavbarController::class, 'moveUp']);
        Route::match(['put', 'post'], '/admin/navbar/items/{id}/move-down', [NavbarController::class, 'moveDown']);
        Route::match(['delete', 'post'], '/admin/navigation/navbar/items/{id}', [NavbarController::class, 'deleteItem']);
        Route::match(['delete', 'post'], '/admin/navbar/items/{id}', [NavbarController::class, 'deleteItem']);
        Route::match(['delete', 'post'], '/admin/navigation/navbar/items/{id}/delete', [NavbarController::class, 'deleteItem']);
        Route::match(['delete', 'post'], '/admin/navbar/items/{id}/delete', [NavbarController::class, 'deleteItem']);

        // Module 3 Home Sections (GET & POST on/off toggle APIs)
        Route::get('/admin/home-sections', [HomeSectionController::class, 'index']);
        Route::match(['put', 'post'], '/admin/home-sections', [HomeSectionController::class, 'toggle']);
        Route::match(['put', 'post'], '/admin/home-sections/toggle', [HomeSectionController::class, 'toggle']);
        Route::match(['put', 'post'], '/admin/home-sections/batch', [HomeSectionController::class, 'batch']);
        Route::post('/admin/home-sections/reset', [HomeSectionController::class, 'reset']);
        Route::match(['put', 'post'], '/admin/home-sections/{sectionId}', [HomeSectionController::class, 'toggle']);
        Route::match(['put', 'post'], '/admin/home', [HomeSectionController::class, 'toggle']);

        // Module 4 Services
        Route::get('/admin/services', [ServiceController::class, 'indexAdmin']);
        Route::get('/admin/services/{id}', [ServiceController::class, 'showAdmin']);
        Route::post('/admin/services', [ServiceController::class, 'store']);
        Route::match(['put', 'post', 'patch'], '/admin/services/{id}', [ServiceController::class, 'update']);
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

        // Module 7 Events
        Route::get('/admin/events', [EventController::class, 'indexAdmin']);
        Route::get('/admin/events/{id}', [EventController::class, 'showAdmin']);
        Route::post('/admin/events', [EventController::class, 'store']);
        Route::put('/admin/events/{id}', [EventController::class, 'update']);
        Route::delete('/admin/events/{id}', [EventController::class, 'destroy']);

        // Module 8 Team
        Route::get('/admin/team', [TeamController::class, 'indexAdmin']);
        Route::get('/admin/team/{id}', [TeamController::class, 'showAdmin']);
        Route::post('/admin/team', [TeamController::class, 'store']);
        Route::put('/admin/team/{id}', [TeamController::class, 'update']);
        Route::delete('/admin/team/{id}', [TeamController::class, 'destroy']);

        // ─── Contact Page API (Module 11: Inquiries Management) ───
        Route::get('/admin/inquiries', [InquiryController::class, 'index']); // Contact Page API
        Route::get('/admin/inquiries/{id}', [InquiryController::class, 'show']); // Contact Page API
        Route::match(['put', 'patch', 'post'], '/admin/inquiries/{id}', [InquiryController::class, 'updateStatus']); // Contact Page API
        Route::delete('/admin/inquiries/{id}', [InquiryController::class, 'destroy']); // Contact Page API
        // Module 9 Testimonials
        Route::get('/admin/testimonials', [TestimonialController::class, 'indexAdmin']);
        Route::get('/admin/testimonials/{id}', [TestimonialController::class, 'showAdmin']);
        Route::post('/admin/testimonials', [TestimonialController::class, 'store']);
        Route::put('/admin/testimonials/{id}', [TestimonialController::class, 'update']);
        Route::delete('/admin/testimonials/{id}', [TestimonialController::class, 'destroy']);

        // Module 10 Gallery
        Route::get('/admin/gallery', [GalleryController::class, 'indexAdmin']);
        Route::get('/admin/gallery/{id}', [GalleryController::class, 'showAdmin']);
        Route::post('/admin/gallery', [GalleryController::class, 'store']);
        Route::put('/admin/gallery/{id}', [GalleryController::class, 'update']);
        Route::delete('/admin/gallery/{id}', [GalleryController::class, 'destroy']);

        // Module 13 Careers
        Route::get('/admin/careers', [CareerController::class, 'indexAdmin']);
        Route::get('/admin/careers/{id}', [CareerController::class, 'showAdmin'])->whereNumber('id');
        Route::post('/admin/careers', [CareerController::class, 'store']);
        Route::match(['put', 'post', 'patch'], '/admin/careers/{id}', [CareerController::class, 'update'])->whereNumber('id');
        Route::delete('/admin/careers/{id}', [CareerController::class, 'destroy'])->whereNumber('id');

        // Module 14 FAQs
        Route::get('/admin/faqs', [FaqController::class, 'indexAdmin']);
        Route::match(['put', 'post'], '/admin/faqs', [FaqController::class, 'saveAll']);
        Route::get('/admin/faqs/{id}', [FaqController::class, 'showAdmin'])->whereNumber('id');
        Route::post('/admin/faqs/item', [FaqController::class, 'store']);
        Route::match(['put', 'post', 'patch'], '/admin/faqs/{id}', [FaqController::class, 'update'])->whereNumber('id');
        Route::delete('/admin/faqs/{id}', [FaqController::class, 'destroy'])->whereNumber('id');

        // Module 15 & 16 Legal Pages (Terms & Privacy)
        Route::get('/admin/legal/{type}', [LegalPageController::class, 'showAdmin']);
        Route::match(['put', 'post'], '/admin/legal/{type}', [LegalPageController::class, 'update']);
        Route::get('/admin/terms', [LegalPageController::class, 'showTermsAdmin']);
        Route::match(['put', 'post'], '/admin/terms', [LegalPageController::class, 'updateTerms']);
        Route::get('/admin/privacy', [LegalPageController::class, 'showPrivacyAdmin']);
        Route::match(['put', 'post'], '/admin/privacy', [LegalPageController::class, 'updatePrivacy']);
    });
});
