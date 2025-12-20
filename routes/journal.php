<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Journal\PublicJournalController;
use App\Http\Controllers\Journal\ArticleController;
use App\Http\Controllers\Journal\DashboardController;

/*
|--------------------------------------------------------------------------
| Journal Routes - URL-Based Journal Routing
|--------------------------------------------------------------------------
|
| These routes handle all journal-related functionality using SEO-friendly
| URL patterns with journal acronyms. All routes are scoped to specific
| journals using the {acronym} parameter.
|
*/

// Journal-scoped routes with authentication and context middleware
Route::middleware(['auth', 'set.journal.context'])->group(function () {

    // Journal Dashboard Routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/articles', [DashboardController::class, 'articles'])->name('articles');
        Route::get('/editorial', [DashboardController::class, 'editorial'])->name('editorial');
        Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    });

    // Article Management Routes
    Route::resource('articles', ArticleController::class)->except(['show']);

    // Editorial Action Routes
    Route::post('articles/{article}/review', [ArticleController::class, 'review'])->name('articles.review');
    Route::post('articles/{article}/approve', [ArticleController::class, 'approve'])->name('articles.approve');
    Route::post('articles/{article}/reject', [ArticleController::class, 'reject'])->name('articles.reject');
    Route::post('articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
    Route::get('articles/{article}/download', [ArticleController::class, 'download'])->name('articles.download');

    // Article Show Route (for authenticated users)
    Route::get('articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
});

// Public journal routes (no authentication required)
Route::middleware(['set.journal.context'])->group(function () {

    // Public Journal Pages
    Route::get('/', [PublicJournalController::class, 'index'])->name('public.index');
    Route::get('/about', [PublicJournalController::class, 'about'])->name('public.about');
    Route::get('/editorial-board', [PublicJournalController::class, 'editorialBoard'])->name('public.editorial-board');
    Route::get('/submission-guidelines', [PublicJournalController::class, 'submissionGuidelines'])->name('public.submission-guidelines');

    // Public Article Routes
    Route::get('/articles', [PublicJournalController::class, 'articles'])->name('public.articles');
    Route::get('/articles/{article}', [PublicJournalController::class, 'articleDetails'])->name('public.article-details');

    // Archive and Contact
    Route::get('/archive', [PublicJournalController::class, 'archive'])->name('public.archive');
    Route::get('/contact', [PublicJournalController::class, 'contact'])->name('public.contact');
});

// Legacy route redirects (for backward compatibility)
Route::get('journal/{journal_id}/{any?}', function ($journalId, $any = null) {
    $journal = \App\Models\ArticleCategory::find($journalId);

    if ($journal && $journal->journal_acronym) {
        $targetPath = $any ? '/' . $any : '/';
        return redirect()->route('public.index', [$journal->journal_acronym . $targetPath], 301);
    }

    abort(404);
})->where('any', '.*')->name('legacy-journal-redirect');

