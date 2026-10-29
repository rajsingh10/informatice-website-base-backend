<?php

use App\Http\Controllers\LayoutController;
use Illuminate\Support\Facades\Route;

// Interactive API Testing Dashboard Page
Route::get('/', function () {
    return view('api-tester');
});

Route::get('/api-tester', function () {
    return view('api-tester');
});

// Direct aliases for /public/layouts and /admin/layouts/{key}
Route::middleware([\App\Http\Middleware\ForceJsonResponse::class])->group(function () {
    Route::get('/public/layouts', [LayoutController::class, 'index']);

    Route::middleware('auth:api')->group(function () {
        Route::put('/admin/layouts/{key}', [LayoutController::class, 'update']);
    });
});
