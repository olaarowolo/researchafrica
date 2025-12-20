<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublisherGalleyController;

// =============================
// Public Routes
// =============================
// Public User Documentation
Route::view('/user-docs', 'user-docs')->name('user.docs');
// Author galley proof approval (public link)
Route::get('/author/galley-approval/{article}', [PublisherGalleyController::class, 'authorGalleyApproval'])->name('author.galley.approval');

// =============================
// Authentication Routes
// =============================
Route::get('/login', 'Auth\LoginController@login')->name('login');
Route::post('/login', 'Auth\LoginController@authLogin')->name('login.submit');
Route::get('/admin/login', 'Auth\LoginController@login')->name('admin.login');
Route::post('/admin/login', 'Auth\LoginController@authLogin')->name('admin.submit-login');
Route::post('/admin/logout', 'Auth\LoginController@logout')->name('admin.logout');
Route::get('/admin', function () {
    return redirect('/admin/login');
});

// =============================
// Publisher Routes
// =============================
require __DIR__.'/publisher.php';

// =============================
// User Routes
// =============================
require __DIR__.'/user.php';

// =============================
// Admin Routes
// =============================
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth:web']], function () {
    Route::get('/home', 'Admin\HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'Admin\PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'Admin\PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'Admin\RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'Admin\RolesController');

    // Users
    Route::delete('users/destroy', 'Admin\UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'Admin\UsersController');

    // Setting
    Route::delete('settings/destroy', 'Admin\SettingController@massDestroy')->name('settings.massDestroy');
    Route::post('settings/media', 'Admin\SettingController@storeMedia')->name('settings.storeMedia');
    Route::get('settings', 'Admin\SettingController@index')->name('settings.index');
    Route::post('settings', 'Admin\SettingController@update')->name('settings.update');

    // Faq Category
    Route::delete('faq-categories/destroy', 'Admin\FaqCategoryController@massDestroy')->name('faq-categories.massDestroy');
    Route::resource('faq-categories', 'Admin\FaqCategoryController');

    // Faq Question
    Route::delete('faq-questions/destroy', 'Admin\FaqQuestionController@massDestroy')->name('faq-questions.massDestroy');
    Route::resource('faq-questions', 'Admin\FaqQuestionController');

    // Content Category
    Route::delete('content-categories/destroy', 'Admin\ContentCategoryController@massDestroy')->name('content-categories.massDestroy');
    Route::resource('content-categories', 'Admin\ContentCategoryController');

    // Content Tag
    Route::delete('content-tags/destroy', 'Admin\ContentTagController@massDestroy')->name('content-tags.massDestroy');
    Route::resource('content-tags', 'Admin\ContentTagController');

    // Content Page
    Route::delete('content-pages/destroy', 'Admin\ContentPageController@massDestroy')->name('content-pages.massDestroy');
    Route::post('content-pages/media', 'Admin\ContentPageController@storeMedia')->name('content-pages.storeMedia');
    Route::post('content-pages/ckmedia', 'Admin\ContentPageController@storeCKEditorImages')->name('content-pages.storeCKEditorImages');
    Route::resource('content-pages', 'Admin\ContentPageController');

    // Member
    Route::delete('members/destroy', 'Admin\MemberController@massDestroy')->name('members.massDestroy');
    Route::post('members/media', 'Admin\MemberController@storeMedia')->name('members.storeMedia');
    Route::post('members/ckmedia', 'Admin\MemberController@storeCKEditorImages')->name('members.storeCKEditorImages');
    Route::post('members/parse-csv-import', 'Admin\MemberController@parseCsvImport')->name('members.parseCsvImport');
    Route::post('members/process-csv-import', 'Admin\MemberController@processCsvImport')->name('members.processCsvImport');
    Route::resource('members', 'Admin\MemberController');

    // MemberRole
    Route::resource('member-roles', 'Admin\MemberRoleController');


    // Countries
    Route::delete('countries/destroy', 'Admin\CountriesController@massDestroy')->name('countries.massDestroy');
    Route::resource('countries', 'Admin\CountriesController');

    // Subscriptions
    Route::delete('subscriptions/destroy', 'Admin\SubscriptionsController@massDestroy')->name('subscriptions.massDestroy');
    Route::post('subscriptions/media', 'Admin\SubscriptionsController@storeMedia')->name('subscriptions.storeMedia');
    Route::post('subscriptions/ckmedia', 'Admin\SubscriptionsController@storeCKEditorImages')->name('subscriptions.storeCKEditorImages');
    Route::post('subscriptions/parse-csv-import', 'Admin\SubscriptionsController@parseCsvImport')->name('subscriptions.parseCsvImport');
    Route::post('subscriptions/process-csv-import', 'Admin\SubscriptionsController@processCsvImport')->name('subscriptions.processCsvImport');
    Route::resource('subscriptions', 'Admin\SubscriptionsController');

    // Member Subscriptions
    Route::delete('member-subscriptions/destroy', 'Admin\MemberSubscriptionsController@massDestroy')->name('member-subscriptions.massDestroy');
    Route::post('member-subscriptions/parse-csv-import', 'Admin\MemberSubscriptionsController@parseCsvImport')->name('member-subscriptions.parseCsvImport');
    Route::post('member-subscriptions/process-csv-import', 'Admin\MemberSubscriptionsController@processCsvImport')->name('member-subscriptions.processCsvImport');
    Route::resource('member-subscriptions', 'Admin\MemberSubscriptionsController');

    // Article Keyword
    Route::delete('article-keywords/destroy', 'Admin\ArticleKeywordController@massDestroy')->name('article-keywords.massDestroy');
    Route::resource('article-keywords', 'Admin\ArticleKeywordController');


// Article Category
    Route::delete('article-categories/destroy', 'Admin\ArticleCategoryController@massDestroy')->name('article-categories.massDestroy');
    Route::resource('article-categories', 'Admin\ArticleCategoryController');

    // Journals
    Route::resource('journals', 'Admin\JournalController');
    Route::get('journals/{journal}/settings', 'Admin\JournalController@settings')->name('journals.settings');
    Route::put('journals/{journal}/settings', 'Admin\JournalController@updateSettings')->name('journals.settings.update');
    Route::get('journals/{journal}/analytics', 'Admin\JournalController@analytics')->name('journals.analytics');

    // Journal Memberships
    Route::get('journal-memberships/{journal}', 'Admin\JournalMembershipController@index')->name('journal-memberships.index');
    Route::get('journal-memberships/{journal}/create', 'Admin\JournalMembershipController@create')->name('journal-memberships.create');
    Route::post('journal-memberships/{journal}', 'Admin\JournalMembershipController@store')->name('journal-memberships.store');
    Route::get('journal-memberships/{journal}/{member}/edit', 'Admin\JournalMembershipController@edit')->name('journal-memberships.edit');
    Route::put('journal-memberships/{journal}/{member}', 'Admin\JournalMembershipController@update')->name('journal-memberships.update');
    Route::delete('journal-memberships/{journal}/{member}', 'Admin\JournalMembershipController@destroy')->name('journal-memberships.destroy');
    Route::post('journal-memberships/{journal}/{member}/approve', 'Admin\JournalMembershipController@approve')->name('journal-memberships.approve');
    Route::post('journal-memberships/{journal}/{member}/reject', 'Admin\JournalMembershipController@reject')->name('journal-memberships.reject');
    Route::post('journal-memberships/{journal}/{member}/suspend', 'Admin\JournalMembershipController@suspend')->name('journal-memberships.suspend');
    Route::post('journal-memberships/{journal}/{member}/reactivate', 'Admin\JournalMembershipController@reactivate')->name('journal-memberships.reactivate');
    Route::post('journal-memberships/{journal}/bulk-update', 'Admin\JournalMembershipController@bulkUpdate')->name('journal-memberships.bulk-update');
    Route::get('journal-memberships/{journal}/statistics', 'Admin\JournalMembershipController@statistics')->name('journal-memberships.statistics');


    // ARticle Journal


    Route::delete('article-sub-categories/destroy', 'Admin\ArticleSubCategoryController@massDestroy')->name('article-sub-categories.massDestroy');
    Route::resource('article-sub-categories', 'Admin\ArticleSubCategoryController');

    // Article
    Route::delete('articles/destroy', 'Admin\ArticleController@massDestroy')->name('articles.massDestroy');
    Route::post('articles/media', 'Admin\ArticleController@storeMedia')->name('articles.storeMedia');
    Route::post('articles/ckmedia', 'Admin\ArticleController@storeCKEditorImages')->name('articles.storeCKEditorImages');
    Route::resource('articles', 'Admin\ArticleController');

    // About
    Route::get('abouts', 'Admin\AboutController@index')->name('abouts.index');
    Route::post('abouts', 'Admin\AboutController@update')->name('abouts.update');

    // Member Type
    Route::delete('member-types/destroy', 'Admin\MemberTypeController@massDestroy')->name('member-types.massDestroy');
    Route::resource('member-types', 'Admin\MemberTypeController');

    // Comment
    Route::delete('comments/destroy', 'Admin\CommentController@massDestroy')->name('comments.massDestroy');
    Route::post('comments/media', 'Admin\CommentController@storeMedia')->name('comments.storeMedia');
    Route::post('comments/ckmedia', 'Admin\CommentController@storeCKEditorImages')->name('comments.storeCKEditorImages');
    Route::resource('comments', 'Admin\CommentController');

    // Editorial Workflows
    Route::delete('editorial-workflows/destroy', 'Admin\EditorialWorkflowController@massDestroy')->name('editorial-workflows.massDestroy');
    Route::resource('editorial-workflows', 'Admin\EditorialWorkflowController');

    // Editorial Board
    Route::get('editorial-board/{journal}', 'Admin\EditorialBoardController@index')->name('editorial-board.index');
    Route::get('editorial-board/{journal}/create', 'Admin\EditorialBoardController@create')->name('editorial-board.create');
    Route::post('editorial-board/{journal}', 'Admin\EditorialBoardController@store')->name('editorial-board.store');
    Route::get('editorial-board/{journal}/{member}/edit', 'Admin\EditorialBoardController@edit')->name('editorial-board.edit');
    Route::put('editorial-board/{journal}/{member}', 'Admin\EditorialBoardController@update')->name('editorial-board.update');
    Route::delete('editorial-board/{journal}/{member}', 'Admin\EditorialBoardController@destroy')->name('editorial-board.destroy');
    Route::post('editorial-board/{journal}/reorder', 'Admin\EditorialBoardController@reorder')->name('editorial-board.reorder');
    Route::get('editorial-board/{journal}/analytics', 'Admin\EditorialBoardController@analytics')->name('editorial-board.analytics');

    // Editorial Workflow Stages (nested under workflows)
    Route::post('editorial-workflows/{workflow}/stages', 'Admin\EditorialWorkflowController@storeStage')->name('editorial-workflows.stages.store');
    Route::put('editorial-workflow-stages/{stage}', 'Admin\EditorialWorkflowController@updateStage')->name('editorial-workflow-stages.update');
    Route::delete('editorial-workflow-stages/{stage}', 'Admin\EditorialWorkflowController@destroyStage')->name('editorial-workflow-stages.destroy');

    // Assign article to workflow
    Route::post('editorial-workflows/{workflow}/assign-article', 'Admin\EditorialWorkflowController@assignArticle')->name('editorial-workflows.assign-article');
});

Route::group(['prefix' => 'admin/profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
 });

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('password/reset', [\App\Http\Controllers\AdminPasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [\App\Http\Controllers\AdminPasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [\App\Http\Controllers\AdminPasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [\App\Http\Controllers\AdminPasswordResetController::class, 'reset'])->name('password.update');
});


// =============================
// Journal Routes (SEO & Legacy)
// =============================
// SEO-friendly journal routes
Route::prefix('journals/{acronym}')->name('journals.')->group(function () {
    include __DIR__ . '/journal.php';
});

// Legacy journal route redirects (for backward compatibility)
Route::get('journal/{journal_id}', function ($journalId) {
    $journal = \App\Models\ArticleCategory::find($journalId);
    if ($journal && $journal->journal_acronym) {
        return redirect()->route('journals.public.index', $journal->journal_acronym, 301);
    }
    abort(404);
})->name('legacy-journal-redirect');

// Legacy article redirects
Route::get('articles/{article}', function ($articleId) {
    $article = \App\Models\Article::find($articleId);
    if ($article && $article->journal && $article->article_status == 3) {
        return redirect()->route('journals.public.article-details', [
            $article->journal->journal_acronym,
            $article
        ], 301);
    }
    abort(404);
})->name('legacy-article-redirect');