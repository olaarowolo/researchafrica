<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublisherGalleyController;

// ...existing routes...

Route::middleware(['auth', 'role:Publisher'])->group(function () {
    Route::get('/publisher/articles/{article}/galley', [PublisherGalleyController::class, 'showUploadForm'])->name('publisher.galley.form');
    Route::post('/publisher/articles/{article}/galley', [PublisherGalleyController::class, 'uploadGalley'])->name('publisher.galley.upload');
    Route::get('/publisher/articles/{article}/final', [PublisherGalleyController::class, 'showFinalUploadForm'])->name('publisher.final.form');
    Route::post('/publisher/articles/{article}/final', [PublisherGalleyController::class, 'uploadFinalVersion'])->name('publisher.final.upload');
});
