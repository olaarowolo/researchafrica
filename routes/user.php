<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', 'Members\PageController@home')->name('home');
Route::get('/mail', function () {
    return view('mail.article-mail');
});
Route::get('/email-verify', function () {
    return view('member.auth.email_verify');
})->name('email-verify');


Route::controller('AjaxController')->group(function () {
    Route::get('get-state/{id}', 'getStates');
    Route::post('keyword_delete', 'keywordDelete')->name('keyword_delete');
    Route::get('verify_transaction/{reference}', 'verifyPayment');
    Route::get('bookmark/{article}', 'bookmark')->name('bookmark');
    Route::get('get-journal/{journal}', 'getJournals');
    Route::get('download-article/{article}', 'downloadPdf')->name('download-article');
    Route::get('download-review/{article}', 'downloadPaperReview')->name('download-review-doc');
    Route::get('download-comment-review/{comment}', 'downloadCommentPaperReview')->name('download-comment-doc');
});

// AuthController
Route::group(['as' => 'member.', 'namespace' => 'Members'], function () {

    // Logout
    Route::post('member/logout', 'AuthController@logout')->name('log-out');

    // Login
    Route::get('login', 'AuthController@loginPage')->name('login');
    Route::post('login', 'AuthController@authenticate')->name('submit-login');

    // Register
    Route::get('register', 'AuthController@registerPage')->name('register');
    Route::post('register', 'AuthController@register')->name('submit-register');

    Route::post('verify_email', 'AuthController@verify')->name('verify_email');

    Route::get('forget-password', 'AuthController@forgetPassword')->name('forget-password');

    Route::post('email-password', 'AuthController@emailPassword')->name('email-password');

    Route::get('reset-password/{hash}', 'AuthController@resetPassword')->name('reset-password');
    Route::post('reset-password/{hash}', 'AuthController@resetPasswordSubmit')->name('reset-password-submit');


    Route::group(['prefix' => 'profile'], function () {
        // Profile
        Route::get('/', "ProfileSecurityController@profilePage")->name('profile');
        Route::get('edit', "ProfileSecurityController@editProfile")->name('profile.edit');
        Route::post('edit', "ProfileSecurityController@updateProfile")->name('profile.update');
        Route::post('password', "ProfileSecurityController@changePassword")->name('password.changePassword');
        Route::post('profile_picture', "ProfileSecurityController@profile_picture")->name('profile_picture');

        // Articles
        Route::get('article/under-review', "ArticleController@underReview")->name('articles.under-review');
        Route::post('publish-article/{article}', "ArticleController@publishArticle")->name('articles.publish');

        Route::resource('articles', 'ArticleController');

        // Sub Articles
        Route::resource('sub-articles', 'SubArticleController');

        // Editorial Workflows
        Route::get('editorial-workflows/dashboard', 'EditorialWorkflowController@dashboard')->name('editorial-workflows.dashboard');
        Route::get('editorial-workflows/my-articles', 'EditorialWorkflowController@myArticles')->name('editorial-workflows.my-articles');
        Route::post('articles/{article}/submit-for-review', 'EditorialWorkflowController@submitForReview')->name('articles.submit-for-review');
        Route::post('articles/{article}/request-revision', 'EditorialWorkflowController@requestRevision')->name('articles.request-revision');
        Route::post('articles/{article}/approve-stage', 'EditorialWorkflowController@approveStage')->name('articles.approve-stage');
        Route::post('articles/{article}/reject-stage', 'EditorialWorkflowController@rejectStage')->name('articles.reject-stage');
        Route::get('editorial-workflows/assigned-articles', 'EditorialWorkflowController@assignedArticles')->name('editorial-workflows.assigned-articles');

        // Editor
        Route::controller('EditorController')->group(function () {
            Route::get('editor', 'index')->name('editor.index');
            Route::post('send-review/{article}', 'sendReview')->name('send-review');
            Route::post('send-final-review/{article}', 'sendFinalReview')->name('send-final-review');
            Route::post('send-editor/{article}', 'sendEditor')->name('send-editor');
            Route::post('send-editor-back/{article}', 'sendToSecondEditor')->name('send-editor-back');
            Route::post('send-editor-third/{article}', 'sendToThirdEditor')->name('send-editor-third');
        });

        // Commenting
        Route::get('comments/{article}/{comment}', 'CommentController@index')->name('comments.index');
        Route::post('comments/{article}', 'CommentController@store')->name('comments.store');
        Route::post('comment-article/{article}', 'CommentController@commentArticleUpdate')->name('comment.article-update');

        Route::controller('Miscellaneous')->group(function () {
            // Editor Accept
            Route::post('editor-accept/{article}', 'EditorAccept')->name('editor.accept');
            Route::post('editor-accept-second/{article}', 'SecondEditorAccept')->name('editor.accept.second');
            Route::post('editor-accept-third/{article}', 'ThirdEditorAccept')->name('editor.accept.third');

            // Reviewer Accept
            Route::post('reviewer-accept/{article}', 'ReviewerAccept')->name('reviewer.accept');
            Route::post('reviewer-accept-final/{article}', 'ReviewerAcceptFinal')->name('reviewer.accept.final');

            // Plublisher Accept
            Route::post('publisher-accept/{article}', 'PublisherAccept')->name('publisher.accept');

            Route::get('view-bookmark', 'viewBookmark')->name('view-bookmark');
            Route::post('become-author', 'becomeAuthor')->name('become-author');
            Route::get('view/purchased', 'viewPublishArticle')->name('purchased-article');
            Route::post('amount/{article}', 'updateAmount')->name('update-amount');
        });




        Route::get('open-document/{article}', 'DocumentController@openDocument')->name('open.docx');
    });



    // Page Controller
    Route::controller('PageController')->group(function () {
        Route::get('about-us', 'about')->name('about');
        Route::get('faq', 'faq')->name('faq');
        Route::get('contact-us', 'contact')->name('contact');
        Route::get('search', 'search')->name('search');
        Route::get('search/advance', 'advanceSearchView')->name('advance-search');
        Route::post('search/advance', 'advanceSearch')->name('advance-search.submit');
        Route::get('view/article/{article}', 'viewArticle')->name('view-article');
        Route::get('{cat}/sub/{sub}/{journal}', 'catSub')->name('cat-sub');

        // Information

    });




    Route::controller('CategoryController')->group(function () {
        Route::get('{id}/journal', 'journal')->name('journal');
    });
});
Route::view('/ethics', 'member/pages/ethics');
Route::view('/terms', 'member/pages/terms');
Route::view('/policy', 'member/pages/policy');
Route::view('/cookiepolicy', 'member/pages/cookiepolicy');
Route::view('/infomation/authors', 'member/pages/authors');
Route::view('/infomation/editors', 'member/pages/editors');
Route::view('/infomation/researchers', 'member/pages/researchers');
Route::view('/infomation/reviewers', 'member/pages/reviewers');

Route::post('contact', 'Members\ContactController@contact')->name('user.contact');

Route::get('articles/download/{path}', function (string $path) {
    return Storage::disk('local')->download($path);
})->name('article.file');

Route::get('optimize-clear', function () {
    Artisan::call('optimize:clear');
    echo "optimize clear successfully";
});

Route::get('optimizer', function () {
    Artisan::call('optimize');
    echo "optimized successfully";
});

//Route::get('migrate', function(){
//    Artisan::call('migrate --path=database/migrations/2023_10_26_151818_add_reviewer_id_to_publisher_accepts.php');
//    echo 'migraated successfully';
//});
