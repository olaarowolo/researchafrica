<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AfriScribe\Http\Controllers\AfriscribeController;
use App\Modules\AfriScribe\Http\Controllers\QuoteRequestController;

/*
|--------------------------------------------------------------------------
| AfriScribe Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register AfriScribe module routes for your application.
| These routes are loaded by the AfriScribeServiceProvider.
|
*/

// Public routes
Route::prefix('afriscribe')->name('afriscribe.')->group(function () {
    Route::get('/login', function () {
        return view('afriscribe.login');
    })->name('login');
    Route::post('/login', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'login'])->name('login.submit');
    Route::get('/home', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'welcome'])->name('welcome');
    Route::get('/about', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'about'])->name('about');
    Route::get('/manuscripts', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'manuscripts'])->name('manuscripts');
    Route::get('/proofreading', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'proofreading'])->name('proofreading');
    Route::get('/welcome-form', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'welcomeForm'])->name('welcome-form');
    Route::post('/request', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'processRequest'])->name('request');
    Route::post('/process-request', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'processRequest'])->name('process-request');
    Route::get('/quote-request', [\App\Modules\AfriScribe\Http\Controllers\QuoteRequestController::class, 'create'])->name('quote-request.create');
    Route::post('/quote-request', [\App\Modules\AfriScribe\Http\Controllers\QuoteRequestController::class, 'store'])->name('quote-request.store');
    Route::get('/pricing-data', [\App\Modules\AfriScribe\Http\Controllers\QuoteRequestController::class, 'getPricingData'])->name('pricing-data');
});

// Admin routes (protected)
Route::prefix('admin/afriscribe')->name('admin.afriscribe.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/requests', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'getRequests'])->name('requests');
    Route::put('/requests/{id}/status', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'updateRequestStatus'])->name('update-status');
    Route::get('/quote-requests', [\App\Modules\AfriScribe\Http\Controllers\QuoteRequestController::class, 'index'])->name('quote-requests.index');
    Route::get('/quote-requests/{id}', [\App\Modules\AfriScribe\Http\Controllers\QuoteRequestController::class, 'show'])->name('quote-requests.show');
    Route::put('/quote-requests/{id}/status', [\App\Modules\AfriScribe\Http\Controllers\QuoteRequestController::class, 'updateStatus'])->name('quote-requests.update-status');
    Route::get('/quote-requests/{id}/download', [\App\Modules\AfriScribe\Http\Controllers\QuoteRequestController::class, 'downloadFile'])->name('quote-requests.download');
});

// Dashboard routes (protected)
Route::prefix('afriscribe')->name('afriscribe.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'dashboard'])->name('dashboard');
    Route::get('/insights', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'insights'])->name('insights');
    Route::get('/connect', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'connect'])->name('connect');
    Route::get('/archive', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'archive'])->name('archive');
    Route::get('/editor', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'editor'])->name('editor');
});
