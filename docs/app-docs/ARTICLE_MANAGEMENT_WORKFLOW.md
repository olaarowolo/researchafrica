# Research Africa - Article Management Workflow

## Table of Contents

1. [Workflow Overview](#workflow-overview)
2. [Article Status System](#article-status-system)
3. [Submission Process](#submission-process)
4. [Editorial Review Stage](#editorial-review-stage)
5. [Peer Review Stage](#peer-review-stage)
6. [Publisher Decision Stage](#publisher-decision-stage)
7. [Publication and Access Control](#publication-and-access-control)
8. [Notification System](#notification-system)
9. [File Management](#file-management)
10. [Role-Based Access Control](#role-based-access-control)
11. [API Endpoints](#api-endpoints)
12. [Database Schema](#database-schema)

---

## Workflow Overview

The Research Africa platform implements a comprehensive multi-stage article management workflow designed to ensure quality control and peer review standards for academic publishing. The system follows a structured progression from initial submission through final publication.

### Key Participants:

-   **Authors/Members**: Submit articles for publication
-   **Editors**: Conduct initial review and quality assessment
-   **Reviewers**: Perform peer review and provide detailed feedback
-   **Publishers**: Make final publication decisions
-   **Administrators**: Oversee system operations and manage content

### Workflow Stages:

```
Author Submission → Editorial Review → Peer Review → Publisher Decision → Publication
```

---

## Article Status System

The platform uses a three-tier status system to track article progress through the publication pipeline:

### Status Definitions:

#### 1. Pending (Status 1)

-   **Description**: Article newly submitted, awaiting initial editorial review
-   **Transitions**: → Reviewing (2) or Rejected
-   **Permissions**: Author can edit, Editors can review
-   **Notifications**: New article notifications sent to editors

#### 2. Reviewing (Status 2)

-   **Description**: Article under active peer review process
-   **Transitions**: → Published (3) or Rejected
-   **Permissions**: Author cannot edit, Reviewers can comment
-   **Notifications**: Review notifications sent to reviewers and authors

#### 3. Published (Status 3)

-   **Description**: Article approved and made publicly available
-   **Transitions**: Final state (no further transitions)
-   **Permissions**: Read-only access, can be purchased/downloaded
-   **Notifications**: Publication announcements sent to authors and subscribers

---

## Submission Process

### Author Submission Workflow

#### 1. Article Creation (Member/Author Side)

```php
// Member Article Controller - store() method
public function store(StoreArticleRequest $request)
{
    abort_unless($this->author(), Response::HTTP_UNAUTHORIZED);

    $input = $request->validated();
    $input['member_id'] = auth('member')->id();

    // Process keywords
    $articleKeywords = $request->input('articleKeywords', []);
    $keywords = $this->keywords($articleKeywords);

    // Handle access type and pricing
    if ($request->access_type == 1) {
        $input['amount'] = null; // Open access, no charge
    }

    // Create article record
    $article = Article::create($input);
    $article->article_keywords()->sync($keywords);

    // Create sub-article for version tracking
    $input['article_id'] = $article->id;
    $input['status'] = 1; // Set to pending

    $sub = SubArticle::create($input);

    // Handle file upload
    $paper = $request->file('upload_paper');
    if ($paper) {
        $paper = $this->manualStoreMedia($paper)['name'];
        $sub->addMedia(storage_path('tmp/uploads/' . basename($paper)))
            ->toMediaCollection('upload_paper');
    }

    // Send notifications
    $full_name = auth('member')->user()->first_name . ' ' . auth('member')->user()->last_name;
    $this->articleMail($full_name);
    $this->allEditor($article);

    // Create editorial acceptance record
    EditorAccept::create([
        'article_id' => $article->id,
    ]);

    return redirect()->route('member.profile')
        ->with('success', 'Article Uploaded Successfully, please wait for review');
}
```

#### 2. Submission Validation

**Required Fields:**

-   Title and abstract
-   Author information
-   Article category
-   Keywords
-   File upload (PDF/DOC/DOCX)
-   Access type selection

**Access Types:**

-   **Open Access (1)**: Free to read and download
-   **Closed Access (2)**: Requires payment or subscription

#### 3. File Upload Process

```php
// Media uploading workflow
1. Validate file type (PDF, DOC, DOCX)
2. Check file size limits
3. Generate unique filename
4. Store in temporary location
5. Process through Media Library
6. Link to SubArticle model
7. Generate thumbnails if applicable
```

---

## Editorial Review Stage

### Editor Assignment and Review

#### 1. Editorial Notification System

```php
// MailArticleTrait - allEditor() method
public function allEditor($article, $editor_id = "")
{
    $editors = $editor_id ?
        Member::where('member_type_id', 2)->where('id', $editor_id)->get() :
        Member::where('member_type_id', 2)->get();

    $sender = auth('member')->user();

    foreach ($editors as $editor) {
        try {
            if ($article->last->status == 1) {
                // Send new article notification
                Mail::to($editor->email_address)
                    ->send(new NewArticle($article, $editor));
            } else {
                // Send forwarded article notification
                Mail::to($editor->email_address)
                    ->send(new ForwardedArticle($article, $editor, $sender));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    return true;
}
```

#### 2. Editorial Review Actions

**Editor Responsibilities:**

-   Initial quality assessment
-   Check for plagiarism and authenticity
-   Verify completeness of submission
-   Assign to peer reviewers
-   Make acceptance/rejection recommendations

**Editorial Decision Points:**

```php
// Editorial actions available:
- Send to reviewer (advance to peer review)
- Request revisions (return to author)
- Reject article (terminate process)
- Forward to second editor
```

#### 3. Editorial Acceptance Tracking

```php
// EditorAccept Model
class EditorAccept extends Model
{
    protected $fillable = [
        'article_id',
        'member_id', // Editor who accepted
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
```

---

## Peer Review Stage

### Reviewer Assignment Process

#### 1. Multiple Reviewer System

The platform supports multiple types of reviewers:

-   **Primary Reviewers (Type 3)**: Initial peer review
-   **Final Reviewers (Type 6)**: Final review and validation

#### 2. Review Assignment Workflow

```php
// Member Article Controller - show() method
public function show(Article $article)
{
    // Check access permissions
    abort_unless(
        $article->member_id == auth('member')->id() ||
        $this->editor() || $this->reviewer() ||
        $this->reviewerFinal() || $this->publisher(),
        Response::HTTP_UNAUTHORIZED
    );

    $article->load('member', 'article_category', 'comments');

    // Get available reviewers by type
    $reviewer1 = Member::where('member_type_id', 3)->get();     // Primary reviewers
    $reviewer2 = Member::where('member_type_id', 6)->get();     // Final reviewers
    $publishers = Member::where('member_type_id', 5)->get();    // Publishers

    return view('member.articles.show', compact('article', 'reviewer1', 'reviewer2', 'publishers'));
}
```

#### 3. Reviewer Notification System

```php
// MailArticleTrait - Reviewer notifications
public function allReviewer($article)
{
    $reviewers = Member::where('member_type_id', 3)->get();
    $sender = auth('member')->user();

    foreach ($reviewers as $reviewer) {
        try {
            Mail::to($reviewer->email_address)
                ->send(new ForwardedArticle($article, $reviewer, $sender));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    return true;
}
```

### Review Process

#### 1. Comment and Feedback System

```php
// Comment Model
class Comment extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',           // Reviewer who commented
        'parent_id',         // For threaded comments
        'user_type',         // Member or Admin
        'comment',           // Review comment
        'correction_upload', // Uploaded review files
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(Member::class, 'user_id');
    }
}
```

#### 2. Reviewer Acceptance Workflow

```php
// ReviewerAccept Model
class ReviewerAccept extends Model
{
    protected $fillable = [
        'article_id',
        'member_id',         // Reviewer ID
        'reviewer_id',       // Assigned reviewer
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
```

#### 3. Review Actions Available

**Reviewer Capabilities:**

-   Add detailed comments
-   Upload review documents
-   Recommend acceptance/rejection
-   Request revisions
-   Forward to final reviewer

---

## Publisher Decision Stage

### Final Review Process

#### 1. Publisher Assignment

```php
// Publisher workflow
1. Final reviewers complete their assessment
2. Publishers receive notification of completed reviews
3. Publishers review all feedback and comments
4. Publishers make final acceptance/rejection decision
5. If accepted, set publication date and access settings
```

#### 2. Publisher Acceptance Model

```php
// PublisherAccept Model
class PublisherAccept extends Model
{
    protected $fillable = [
        'article_id',
        'member_id',         // Publisher who accepted
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
```

#### 3. Publication Decision Workflow

```php
// Member Article Controller - publishArticle() method
public function publishArticle(Article $article)
{
    // Validate pricing for closed access articles
    if ($article->access_type == 2 && ($article->amount == 0 || $article->amount == null)) {
        return back()->withErrors([
            'message' => ['Amount Can\'t Be Empty, Please Set Amount']
        ]);
    }

    // Update article status and publication date
    $article->last->update(['status' => 10]);
    $article->update([
        'article_status' => 3,        // Published
        'published_online' => now()
    ]);

    // Send publication notification
    $this->publishMail($article);

    return back()->with('success', 'Article published successfully');
}
```

---

## Publication and Access Control

### Making Articles Live

#### 1. Publication Process

```php
// Publication workflow
1. Final approval by publisher
2. Set publication date
3. Update article status to "Published" (3)
4. Make article publicly accessible
5. Send publication notifications
6. Update download/view counters
```

#### 2. Access Control Implementation

**Open Access Articles:**

-   Immediately available to all users
-   No authentication required for viewing
-   Free download permissions
-   Listed in public article directory

**Closed Access Articles:**

-   Require user authentication
-   May require subscription or one-time payment
-   Restricted download based on user permissions
-   Metadata visible, full text restricted

#### 3. Article Display and Download

```php
// Article Model - File access methods
public function pdfPaper()
{
    $paper = $this->file_path ?
        Storage::disk($this->storage_disk)->get($this->file_path) :
        $this->convertPaperToPdf();
    return $paper;
}

private function convertPaperToPdf()
{
    // Convert Word documents to PDF for consistent display
    // Handle multiple file formats
    // Return processed PDF content
}
```

---

## Notification System

### Email Template System

The platform uses Laravel's Mail system with specialized Mailable classes for each workflow stage:

#### 1. Author Notifications

```php
// ArticleMail - Submission confirmation
class ArticleMail extends Mailable
{
    public function build()
    {
        return $this->subject('Article Submission Received')
                    ->view('emails.article-mail');
    }
}

// PublishArticle - Publication announcement
class PublishArticle extends Mailable
{
    public function build()
    {
        return $this->subject('Article Published')
                    ->view('emails.publish-article');
    }
}
```

#### 2. Editorial Notifications

```php
// NewArticle - New submission to editors
class NewArticle extends Mailable
{
    public function build()
    {
        return $this->subject('New Article for Review')
                    ->view('emails.new-article');
    }
}

// ForwardedArticle - Article forwarding
class ForwardedArticle extends Mailable
{
    public function build()
    {
        return $this->subject('Article Forwarded for Review')
                    ->view('emails.forwarded-article');
    }
}
```

#### 3. Review Notifications

```php
// CommentMail - Review feedback to authors
class CommentMail extends Mailable
{
    public function build()
    {
        return $this->subject('Review Comments Received')
                    ->view('emails.comment-mail');
    }
}

// AcceptedMail - Acceptance notifications
class AcceptedMail extends Mailable
{
    public function build()
    {
        return $this->subject('Article Status Update')
                    ->view('emails.accepted-mail');
    }
}
```

---

## File Management

### Media Library Integration

The platform uses Spatie's Laravel Media Library for comprehensive file management:

#### 1. File Upload Process

```php
// MediaUploadingTrait
trait MediaUploadingTrait
{
    public function manualStoreMedia($file)
    {
        // Generate unique filename
        $fileName = uniqid() . '_' . Str::of($file->getClientOriginalName())->slug('_');

        // Store temporarily
        $file->storeAs('tmp/uploads', $fileName);

        return ['name' => $fileName];
    }
}
```

#### 2. File Collections

```php
// Different file types handled:
upload_paper:     // Article manuscripts
profile_picture:  // User profile images
ck-media:         // Rich media content
correction_upload: // Review correction files
```

#### 3. File Processing Features

-   **Automatic format conversion** (Word to PDF)
-   **Virus scanning** on upload
-   **Thumbnail generation** for images
-   **File size optimization**
-   **Secure file storage**

---

## Role-Based Access Control

### Permission System

#### 1. Gate Definitions

```php
// Article access gates
Gate::define('article_access', function ($user) {
    return $user->can('article_access');
});

Gate::define('article_create', function ($user) {
    return $user->can('article_create');
});

Gate::define('article_edit', function ($user, $article) {
    return $user->can('article_edit') ||
           ($user->member && $user->member->id == $article->member_id);
});
```

#### 2. Role-Based Permissions

```php
// Author permissions:
- Create articles
- Edit own articles (when not under review)
- View submission status
- Receive notifications

// Editor permissions:
- Review submitted articles
- Assign reviewers
- Forward articles
- Add editorial comments

// Reviewer permissions:
- Access assigned articles
- Add review comments
- Upload review documents
- Recommend decisions

// Publisher permissions:
- Final review access
- Publication decisions
- Access control settings
- Revenue management
```

---

## API Endpoints

### Article Management APIs

#### 1. Author Endpoints

```php
// Article submission
POST /api/articles
GET  /api/articles/{id}
PUT  /api/articles/{id}
DELETE /api/articles/{id}

// Article status
GET /api/articles/{id}/status
POST /api/articles/{id}/publish

// File operations
POST /api/articles/{id}/upload
GET /api/articles/{id}/download
```

#### 2. Review Endpoints

```php
// Review management
POST /api/articles/{id}/assign-reviewer
POST /api/articles/{id}/review
POST /api/articles/{id}/comments
GET /api/articles/{id}/reviews
```

#### 3. Admin Endpoints

```php
// Admin article management
GET /api/admin/articles
PUT /api/admin/articles/{id}/status
GET /api/admin/articles/{id}/analytics
POST /api/admin/articles/{id}/assign-editor
```

---

## Database Schema

### Core Article Tables

#### 1. Articles Table

```sql
CREATE TABLE articles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id BIGINT UNSIGNED,
    article_category_id BIGINT UNSIGNED,
    access_type TINYINT, -- 1=Open, 2=Closed
    title VARCHAR(255),
    author_name VARCHAR(255),
    other_authors TEXT,
    corresponding_authors VARCHAR(255),
    institute_organization VARCHAR(255),
    amount DECIMAL(10,2), -- Price for closed access
    doi_link VARCHAR(255),
    volume VARCHAR(50),
    issue_no VARCHAR(50),
    publish_date DATE,
    published_online TIMESTAMP,
    is_recommended BOOLEAN DEFAULT FALSE,
    storage_disk VARCHAR(50),
    file_path VARCHAR(255),
    article_status TINYINT, -- 1=Pending, 2=Reviewing, 3=Published
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP,

    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (article_category_id) REFERENCES article_categories(id)
);
```

#### 2. Sub Articles Table

```sql
CREATE TABLE sub_articles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id BIGINT UNSIGNED,
    status TINYINT, -- Workflow status
    upload_paper VARCHAR(255), -- Media library reference
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (article_id) REFERENCES articles(id)
);
```

#### 3. Review Workflow Tables

```sql
-- Editorial acceptance
CREATE TABLE editor_accepts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id BIGINT UNSIGNED,
    member_id BIGINT UNSIGNED, -- Editor ID
    created_at TIMESTAMP,

    FOREIGN KEY (article_id) REFERENCES articles(id),
    FOREIGN KEY (member_id) REFERENCES members(id)
);

-- Reviewer acceptance
CREATE TABLE reviewer_accepts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id BIGINT UNSIGNED,
    member_id BIGINT UNSIGNED, -- Reviewer ID
    reviewer_id BIGINT UNSIGNED, -- Assigned reviewer
    created_at TIMESTAMP,

    FOREIGN KEY (article_id) REFERENCES articles(id)
);

-- Final review acceptance
CREATE TABLE reviewer_accept_finals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id BIGINT UNSIGNED,
    member_id BIGINT UNSIGNED,
    reviewer_id BIGINT UNSIGNED,
    created_at TIMESTAMP
);

-- Publisher acceptance
CREATE TABLE publisher_accepts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id BIGINT UNSIGNED,
    member_id BIGINT UNSIGNED, -- Publisher ID
    created_at TIMESTAMP
);
```

#### 4. Comments and Reviews

```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED, -- Reviewer ID
    user_type VARCHAR(50), -- 'App\Models\Member'
    parent_id BIGINT UNSIGNED NULL, -- For threaded comments
    comment TEXT,
    correction_upload VARCHAR(255), -- Media library reference
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (article_id) REFERENCES articles(id),
    FOREIGN KEY (parent_id) REFERENCES comments(id)
);
```

---

## Workflow State Diagram

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   PENDING   │───▶│  REVIEWING  │───▶│  PUBLISHED  │     │   REJECTED  │
│  (Status 1) │    │  (Status 2) │    │  (Status 3) │     │   (Status 0)│
└─────────────┘    └─────────────┘    └─────────────┘     └─────────────┘
       │                  │                  │                   │
       │                  │                  │                   │
       ▼                  ▼                  ▼                   ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐     ┌─────────────┐
│Editor Review│    │Peer Review  │    │Access Control│     │Process End  │
│   Stage     │    │   Stage     │    │   Stage      │     │   Stage     │
└─────────────┘    └─────────────┘    └─────────────┘     └─────────────┘
       │                  │                  │
       │                  │                  │
       ▼                  ▼                  ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Assign     │    │  Review     │    │ Make Public │
│ Reviewers   │    │ Comments    │    │ Available   │
└─────────────┘    └─────────────┘    └─────────────┘
```

---

## Conclusion

The Research Africa article management workflow is designed to maintain high academic standards while providing a streamlined experience for authors, reviewers, and publishers. The system's strength lies in its comprehensive notification system, robust file management, and flexible role-based access control.

Key workflow benefits:

-   **Quality Assurance**: Multi-stage review process ensures content quality
-   **Transparency**: All stakeholders receive appropriate notifications
-   **Flexibility**: Supports both open and closed access publishing models
-   **Efficiency**: Automated workflows reduce manual administrative tasks
-   **Security**: Comprehensive access control and file security measures
-   **Scalability**: Modular design allows for easy expansion and customization

The workflow successfully balances the need for rigorous peer review with the practical requirements of modern academic publishing, making it suitable for journals, conference proceedings, and other scholarly publications.
