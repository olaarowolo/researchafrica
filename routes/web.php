<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| AfriScribe Routes
|--------------------------------------------------------------------------
*/
// Public AfriScribe landing page and related routes
Route::redirect('/afriscribe', '/afriscribe/home');
Route::get('/afriscribe/home', function () { return view('afriscribe.welcome-form'); })->name('afriscribe.welcome');

// New route for AfriScribe admin dashboard
Route::get('/afriscribe/admin', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'dashboard'])->name('afriscribe.admin.dashboard');

// Logout route for AfriScribe
Route::get('/afriscribe/logout', function () {
    Auth::logout();
    return redirect('/afriscribe/home');
})->name('afriscribe.logout');
 
/*
|--------------------------------------------------------------------------
| AfriScribe Module Routes
|--------------------------------------------------------------------------
*/

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

        /*
        |--------------------------------------------------------------------------
        | AfriScribe Module Routes
        |--------------------------------------------------------------------------
        */
        require __DIR__.'/../app/Modules/AfriScribe/Http/routes.php';

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


        // ARticle Journal


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

require 'user.php';

require 'user.php';
