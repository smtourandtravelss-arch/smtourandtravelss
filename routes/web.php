<?php

use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// One-time installer (locked after installation)
Route::get('/install', [InstallController::class, 'show']);
Route::post('/install', [InstallController::class, 'store']);

// Enquiry forms (old /send.php links still work)
Route::post('/send', [EnquiryController::class, 'store'])->middleware('throttle:20,1');
Route::post('/send.php', [EnquiryController::class, 'store'])->middleware('throttle:20,1');

// Admin only: sample preview of one page-builder section (hover in "Add a section")
Route::get('/admin/block-preview/{type}', function (string $type) {
    abort_unless(auth()->check(), 403);
    $data = \App\Support\BlockSamples::for($type);
    abort_if($data === null, 404);

    return response(\App\Services\SiteRenderer::renderBlockPreview($type, $data))
        ->header('Content-Type', 'text/html; charset=utf-8')
        ->header('X-Robots-Tag', 'noindex');
})->where('type', '[a-z_]+');

Route::get('/sitemap.xml', [SiteController::class, 'sitemap']);
Route::get('/robots.txt', [SiteController::class, 'robots']);

// Blog category pages
Route::get('/category/{slug}', [SiteController::class, 'category'])->where('slug', '[A-Za-z0-9-]+');

// Website pages: /  and  /page-slug
Route::get('/', [SiteController::class, 'show']);
// Slugs keep the old website form: letters, numbers, "-", "&" and an optional ".php" (e.g. /about-us.php)
Route::get('/{slug}', [SiteController::class, 'show'])
    ->where('slug', '(?!admin\b|livewire|filament|install\b|up\b)[A-Za-z0-9][A-Za-z0-9&-]*(\.php)?');

// Anything else: Admin → Redirects, otherwise the 404 page
Route::fallback([SiteController::class, 'fallback']);
