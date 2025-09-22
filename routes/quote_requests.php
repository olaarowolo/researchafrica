<?php

use App\Http\Controllers\QuoteRequestController;
use Illuminate\Support\Facades\Route;

// Quote Request Routes
Route::get('/quote-request', [QuoteRequestController::class, 'create'])->name('quote-request.create');
Route::post('/quote-request', [QuoteRequestController::class, 'store'])->name('quote-request.store');
Route::get('/quote-request/pricing', [QuoteRequestController::class, 'getPricingData'])->name('quote-request.pricing');

// Admin routes for managing quote requests
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/quote-requests', [QuoteRequestController::class, 'index'])->name('quote-requests.index');
    Route::get('/quote-requests/{id}', [QuoteRequestController::class, 'show'])->name('quote-requests.show');
    Route::put('/quote-requests/{id}/status', [QuoteRequestController::class, 'updateStatus'])->name('quote-requests.update-status');
    Route::get('/quote-requests/{id}/download', [QuoteRequestController::class, 'downloadFile'])->name('quote-requests.download');
});
