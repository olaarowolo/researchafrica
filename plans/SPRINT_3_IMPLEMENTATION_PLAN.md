# Sprint 3 Implementation Plan - Core Multi-Journal Functionality

**Sprint Duration**: Weeks 7-9 (3 weeks)
**Priority**: High
**Risk Level**: Medium
**Status**: 🚀 **READY TO START**

---

## Executive Summary

Sprint 3 leverages the multi-tenancy database architecture built in Sprint 2 to deliver the first user-facing multi-journal features. This sprint focuses on creating the routes, controllers, and views necessary to browse journals and view articles within a journal's context. It also adapts the article submission process for the new multi-journal system.

### Sprint 2 Achievements Recap ✅

-   ✅ Multi-tenancy database structure implemented
-   ✅ `JournalEditorialBoard` and `JournalMembership` models and tables created
-   ✅ `Article`, `ArticleCategory`, and `Member` models enhanced with journal relationships
-   ✅ `JournalContextService` and middleware for data isolation created
-   ✅ All database migrations for Sprint 2 are ready

### Sprint 3 Goals 🎯

1.  **Journal-Specific Routes**: Create routes to display journal homepages and articles.
2.  **Journal Controller**: Implement a controller to handle requests for journals and articles.
3.  **Journal Views**: Develop views to display journal-specific content.
4.  **Update Article Submission**: Modify the article submission process to be journal-aware.

---

## Sprint 3 Detailed Implementation Plan

### Phase 1: Journal Browsing (Week 1)

#### Task 1.1: Create Journal Routes

**File**: `routes/web.php`

-   Create a route for `/journals/{journal_slug}` that maps to `JournalController@show`.
-   Create a route for `/journals/{journal_slug}/articles/{article}` that maps to `JournalController@showArticle`.
-   Group these routes under the `journal.context` middleware.

#### Task 1.2: Create JournalController

**File**: `app/Http/Controllers/JournalController.php`

-   Create a new controller `JournalController`.
-   Implement the `show` method:
    -   It will receive the `{journal_slug}`.
    -   The `SetJournalContext` middleware will have already set the current journal.
    -   Fetch articles belonging to the current journal using the relationships defined in Sprint 2.
    -   Pass the journal and its articles to a new view.
-   Implement the `showArticle` method:
    -   It will receive `{journal_slug}` and `{article}`.
    -   Ensure the requested article belongs to the specified journal. If not, return a 404 error.
    -   Pass the article to the article view.

#### Task 1.3: Create Journal Views

**File**: `resources/views/journal/show.blade.php`

-   Create a new view to display a journal's homepage.
-   The view will display the journal's name, description, and a list of articles.
-   Article links should point to the new journal-scoped article URL.

**File**: `resources/views/journal/article.blade.php`

-   Create or modify an existing article view to display an article within the context of a journal.
-   This view should display the article's content, author, and other details.

### Phase 2: Journal-Aware Submissions (Week 2)

#### Task 2.1: Update Submission Form

-   Modify the article submission form to include a dropdown (`<select>`) of available journals for the user to submit to.
-   This list should be populated from the `article_categories` table where `is_journal` is true.

#### Task 2.2: Update Submission Controller

-   Update the controller that handles article submissions.
-   The controller should now expect a `journal_id` from the submission form.
-   When creating the new `Article`, associate it with the selected journal by setting the `journal_id`.

### Phase 3: Testing and Refinement (Week 3)

#### Task 3.1: Write Feature Tests

-   Create feature tests to verify the new journal routes and functionality.
-   Test that `/journals/{journal_slug}` displays the correct journal and its articles.
-   Test that `/journals/{journal_slug}/articles/{article}` displays the correct article and returns 404 for incorrect journal-article combinations.
-   Test that the article submission process correctly associates the article with the selected journal.

#### Task 3.2: User Acceptance Testing

-   Manually test the new journal features to ensure they are working as expected.
-   Verify that data isolation is working correctly and that journals only display their own articles.