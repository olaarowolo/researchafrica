
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublisherGalleyController;

// =============================
// Admin Routes
// =============================
// =============================
// Publisher & Galley Proof Routes
// =============================
// Author galley proof approval (public link)
Route::get('/author/galley-approval/{article}', [PublisherGalleyController::class, 'authorGalleyApproval'])->name('author.galley.approval');
// Publisher routes (grouped in routes/publisher.php)
require __DIR__.'/publisher.php';




Route::get('/admin', function () {
    return redirect('/admin/login');
});
// Auth
Route::get('/login', 'Auth\LoginController@login')->name('login');
Route::post('/login', 'Auth\LoginController@authLogin')->name('login.submit');
Route::get('/admin/login', 'Auth\LoginController@login')->name('admin.login');
Route::post('/admin/login', 'Auth\LoginController@authLogin')->name('admin.submit-login');

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function ()
{

    Route::group(['namespace' => 'Auth'], function ()
    {
    //     Route::get('login', 'LoginController@authLogin')->name('submit-login');

    }); // This closing brace was misplaced, it should be after the Auth namespace group.
    Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function () {
        // Route::post('logout', 'UserController@logout')->name('logout');



        Route::get('/home', 'HomeController@index')->name('home');
        Route::post('/logout', 'HomeController@logout')->name('logout');
        // Permissions
        Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
        Route::resource('permissions', 'PermissionsController');

        // Roles
        Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
        Route::resource('roles', 'RolesController');

        // Users
        Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
        Route::resource('users', 'UsersController');

        // Setting
        Route::delete('settings/destroy', 'SettingController@massDestroy')->name('settings.massDestroy');
        Route::post('settings/media', 'SettingController@storeMedia')->name('settings.storeMedia');
        Route::get('settings', 'SettingController@index')->name('settings.index');
        Route::post('settings', 'SettingController@update')->name('settings.update');

        // Faq Category
        Route::delete('faq-categories/destroy', 'FaqCategoryController@massDestroy')->name('faq-categories.massDestroy');
        Route::resource('faq-categories', 'FaqCategoryController');

        // Faq Question
        Route::delete('faq-questions/destroy', 'FaqQuestionController@massDestroy')->name('faq-questions.massDestroy');
        Route::resource('faq-questions', 'FaqQuestionController');

        // Content Category
        Route::delete('content-categories/destroy', 'ContentCategoryController@massDestroy')->name('content-categories.massDestroy');
        Route::resource('content-categories', 'ContentCategoryController');

        // Content Tag
        Route::delete('content-tags/destroy', 'ContentTagController@massDestroy')->name('content-tags.massDestroy');
        Route::resource('content-tags', 'ContentTagController');

        // Content Page
        Route::delete('content-pages/destroy', 'ContentPageController@massDestroy')->name('content-pages.massDestroy');
        Route::post('content-pages/media', 'ContentPageController@storeMedia')->name('content-pages.storeMedia');
        Route::post('content-pages/ckmedia', 'ContentPageController@storeCKEditorImages')->name('content-pages.storeCKEditorImages');
        Route::resource('content-pages', 'ContentPageController');

        // Member
        Route::delete('members/destroy', 'MemberController@massDestroy')->name('members.massDestroy');
        Route::post('members/media', 'MemberController@storeMedia')->name('members.storeMedia');
        Route::post('members/ckmedia', 'MemberController@storeCKEditorImages')->name('members.storeCKEditorImages');
        Route::post('members/parse-csv-import', 'MemberController@parseCsvImport')->name('members.parseCsvImport');
        Route::post('members/process-csv-import', 'MemberController@processCsvImport')->name('members.processCsvImport');
        Route::resource('members', 'MemberController');

        // MemberRole
        Route::resource('member-roles', 'MemberRoleController');


        // Countries
        Route::delete('countries/destroy', 'CountriesController@massDestroy')->name('countries.massDestroy');
        Route::resource('countries', 'CountriesController');

        // Subscriptions
        Route::delete('subscriptions/destroy', 'SubscriptionsController@massDestroy')->name('subscriptions.massDestroy');
        Route::post('subscriptions/media', 'SubscriptionsController@storeMedia')->name('subscriptions.storeMedia');
        Route::post('subscriptions/ckmedia', 'SubscriptionsController@storeCKEditorImages')->name('subscriptions.storeCKEditorImages');
        Route::post('subscriptions/parse-csv-import', 'SubscriptionsController@parseCsvImport')->name('subscriptions.parseCsvImport');
        Route::post('subscriptions/process-csv-import', 'SubscriptionsController@processCsvImport')->name('subscriptions.processCsvImport');
        Route::resource('subscriptions', 'SubscriptionsController');

        // Member Subscriptions
        Route::delete('member-subscriptions/destroy', 'MemberSubscriptionsController@massDestroy')->name('member-subscriptions.massDestroy');
        Route::post('member-subscriptions/parse-csv-import', 'MemberSubscriptionsController@parseCsvImport')->name('member-subscriptions.parseCsvImport');
        Route::post('member-subscriptions/process-csv-import', 'MemberSubscriptionsController@processCsvImport')->name('member-subscriptions.processCsvImport');
        Route::resource('member-subscriptions', 'MemberSubscriptionsController');

        // Article Keyword
        Route::delete('article-keywords/destroy', 'ArticleKeywordController@massDestroy')->name('article-keywords.massDestroy');
        Route::resource('article-keywords', 'ArticleKeywordController');


// Article Category
        Route::delete('article-categories/destroy', 'ArticleCategoryController@massDestroy')->name('article-categories.massDestroy');
        Route::resource('article-categories', 'ArticleCategoryController');

        // Journals
        Route::resource('journals', 'JournalController');
        Route::get('journals/{journal}/settings', 'JournalController@settings')->name('journals.settings');
        Route::put('journals/{journal}/settings', 'JournalController@updateSettings')->name('journals.settings.update');
        Route::get('journals/{journal}/analytics', 'JournalController@analytics')->name('journals.analytics');

        // Journal Memberships
        Route::get('journal-memberships/{journal}', 'JournalMembershipController@index')->name('journal-memberships.index');
        Route::get('journal-memberships/{journal}/create', 'JournalMembershipController@create')->name('journal-memberships.create');
        Route::post('journal-memberships/{journal}', 'JournalMembershipController@store')->name('journal-memberships.store');
        Route::get('journal-memberships/{journal}/{member}/edit', 'JournalMembershipController@edit')->name('journal-memberships.edit');
        Route::put('journal-memberships/{journal}/{member}', 'JournalMembershipController@update')->name('journal-memberships.update');
        Route::delete('journal-memberships/{journal}/{member}', 'JournalMembershipController@destroy')->name('journal-memberships.destroy');
        Route::post('journal-memberships/{journal}/{member}/approve', 'JournalMembershipController@approve')->name('journal-memberships.approve');
        Route::post('journal-memberships/{journal}/{member}/reject', 'JournalMembershipController@reject')->name('journal-memberships.reject');
        Route::post('journal-memberships/{journal}/{member}/suspend', 'JournalMembershipController@suspend')->name('journal-memberships.suspend');
        Route::post('journal-memberships/{journal}/{member}/reactivate', 'JournalMembershipController@reactivate')->name('journal-memberships.reactivate');
        Route::post('journal-memberships/{journal}/bulk-update', 'JournalMembershipController@bulkUpdate')->name('journal-memberships.bulk-update');
        Route::get('journal-memberships/{journal}/statistics', 'JournalMembershipController@statistics')->name('journal-memberships.statistics');


        // ARticle Journal


        Route::delete('article-sub-categories/destroy', 'ArticleSubCategoryController@massDestroy')->name('article-sub-categories.massDestroy');
        Route::resource('article-sub-categories', 'ArticleSubCategoryController');

        // Article
        Route::delete('articles/destroy', 'ArticleController@massDestroy')->name('articles.massDestroy');
        Route::post('articles/media', 'ArticleController@storeMedia')->name('articles.storeMedia');
        Route::post('articles/ckmedia', 'ArticleController@storeCKEditorImages')->name('articles.storeCKEditorImages');
        // Route::post('articles/create', 'ArticleController@create')->name('articles.create');
        // Route::resource('articles', 'ArticleController')->except(['create']);
        Route::resource('articles', 'ArticleController');

        // About
        Route::get('abouts', 'AboutController@index')->name('abouts.index');
        Route::post('abouts', 'AboutController@update')->name('abouts.update');

        // Member Type
        Route::delete('member-types/destroy', 'MemberTypeController@massDestroy')->name('member-types.massDestroy');
        Route::resource('member-types', 'MemberTypeController');

        // Comment
        Route::delete('comments/destroy', 'CommentController@massDestroy')->name('comments.massDestroy');
        Route::post('comments/media', 'CommentController@storeMedia')->name('comments.storeMedia');
        Route::post('comments/ckmedia', 'CommentController@storeCKEditorImages')->name('comments.storeCKEditorImages');
        Route::resource('comments', 'CommentController');

        // Editorial Workflows
        Route::delete('editorial-workflows/destroy', 'EditorialWorkflowController@massDestroy')->name('editorial-workflows.massDestroy');
        Route::resource('editorial-workflows', 'EditorialWorkflowController');

        // Editorial Board
        Route::get('editorial-board/{journal}', 'EditorialBoardController@index')->name('editorial-board.index');
        Route::get('editorial-board/{journal}/create', 'EditorialBoardController@create')->name('editorial-board.create');
        Route::post('editorial-board/{journal}', 'EditorialBoardController@store')->name('editorial-board.store');
        Route::get('editorial-board/{journal}/{member}/edit', 'EditorialBoardController@edit')->name('editorial-board.edit');
        Route::put('editorial-board/{journal}/{member}', 'EditorialBoardController@update')->name('editorial-board.update');
        Route::delete('editorial-board/{journal}/{member}', 'EditorialBoardController@destroy')->name('editorial-board.destroy');
        Route::post('editorial-board/{journal}/reorder', 'EditorialBoardController@reorder')->name('editorial-board.reorder');
        Route::get('editorial-board/{journal}/analytics', 'EditorialBoardController@analytics')->name('editorial-board.analytics');

        // Editorial Workflow Stages (nested under workflows)
        Route::post('editorial-workflows/{workflow}/stages', 'EditorialWorkflowController@storeStage')->name('editorial-workflows.stages.store');
        Route::put('editorial-workflow-stages/{stage}', 'EditorialWorkflowController@updateStage')->name('editorial-workflow-stages.update');
        Route::delete('editorial-workflow-stages/{stage}', 'EditorialWorkflowController@destroyStage')->name('editorial-workflow-stages.destroy');

        // Assign article to workflow
        Route::post('editorial-workflows/{workflow}/assign-article', 'EditorialWorkflowController@assignArticle')->name('editorial-workflows.assign-article');
    });
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




// =============================
// User Routes
// =============================
require __DIR__.'/user.php';


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
