<?php

use App\Http\Controllers\LayoutController;
use App\Http\Controllers\MediaUploadController;
use App\Http\Controllers\SeoController;
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

    // Admin aliases (protected)
    Route::middleware('auth:api')->group(function () {
        Route::put('/admin/layouts/{key}', [LayoutController::class, 'update']);
        Route::get('/admin/seo/{page}', [SeoController::class, 'showAdmin']);
        Route::put('/admin/seo/{page}', [SeoController::class, 'update']);
        Route::post('/admin/uploads', [MediaUploadController::class, 'store']);
        Route::delete('/admin/uploads/{id}', [MediaUploadController::class, 'destroy']);
        Route::get('/admin/settings', [SiteSettingController::class, 'show']);
        Route::put('/admin/settings', [SiteSettingController::class, 'update']);
    });
});
