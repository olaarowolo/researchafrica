<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Editorial Workflow API routes
    Route::group(['prefix' => 'editorial-workflows'], function () {
        Route::get('/stats', 'EditorialWorkflowController@getStats');
        Route::get('/overdue', 'EditorialWorkflowController@getOverdueArticles');

        // Article workflow actions
        Route::post('articles/{article}/assign-workflow', 'EditorialWorkflowController@assignWorkflow');
        Route::post('articles/{article}/submit', 'EditorialWorkflowController@submitForReview');
        Route::post('articles/{article}/start-review', 'EditorialWorkflowController@startReview');
        Route::post('articles/{article}/move-next', 'EditorialWorkflowController@moveToNextStage');
        Route::post('articles/{article}/request-revision', 'EditorialWorkflowController@requestRevision');
        Route::post('articles/{article}/approve', 'EditorialWorkflowController@approveStage');
        Route::post('articles/{article}/reject', 'EditorialWorkflowController@rejectArticle');
        Route::post('articles/{article}/publish', 'EditorialWorkflowController@publishArticle');
        Route::post('articles/{article}/assign-reviewers', 'EditorialWorkflowController@assignReviewers');
    });
});
