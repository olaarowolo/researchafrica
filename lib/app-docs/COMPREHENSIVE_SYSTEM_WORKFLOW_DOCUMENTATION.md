Come out# Research Africa Platform - Comprehensive Workflow Documentation

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Architecture & Technology Stack](#architecture--technology-stack)
3. [User Management System](#user-management-system)
4. [Article Management Workflow](#article-management-workflow)
5. [Editorial & Review Process](#editorial--review-process)
6. [Communication & Notification System](#communication--notification-system)
7. [Content Management System](#content-management-system)
8. [Subscription & Payment System](#subscription--payment-system)
9. [Admin Panel Functionality](#admin-panel-functionality)
10. [AfriScribe Module](#afriscribe-module)
11. [Database Schema & Relationships](#database-schema--relationships)
12. [Security & Permissions](#security--permissions)
13. [File Management & Media Handling](#file-management--media-handling)
14. [API Structure](#api-structure)
15. [Integration Points](#integration-points)

---

## System Overview

**Research Africa** is a comprehensive academic publishing platform built on Laravel that manages the entire lifecycle of academic articles from submission to publication. The system supports multiple journals, editorial workflows, member management, and subscription-based access to content.

### Core Purpose

-   **Multi-Journal Publishing Platform**: Supports multiple academic journals with isolated content and editorial teams
-   **Editorial Workflow Management**: Complete article review and approval process
-   **Member Management**: Comprehensive user management with role-based access control
-   **Content Delivery**: Subscription-based access to published articles and resources

### Key Stakeholders

-   **Authors**: Submit articles for publication
-   **Editors**: Review and manage article submissions
-   **Reviewers**: Provide expert review and feedback
-   **Publishers**: Final approval and publication management
-   **Subscribers**: Access published content
-   **Administrators**: System management and configuration

---

## Architecture & Technology Stack

### Backend Framework

-   **Laravel 9.x**: Primary application framework
-   **PHP 8.x**: Server-side scripting language
-   **MySQL**: Primary database system
-   **Redis**: Caching and session management

### Frontend Technologies

-   **Blade Templates**: Laravel's templating engine
-   **Bootstrap/CSS**: UI framework and styling
-   **jQuery**: JavaScript library for dynamic interactions
-   **Livewire**: Real-time component interactions (selected controllers)

### Media & File Management

-   **Spatie Media Library**: Comprehensive media management
-   **DOMPDF**: PDF generation and conversion
-   **PhpSpreadsheet**: Excel file handling
-   **File Storage**: Laravel's filesystem abstraction

### Authentication & Security

-   **Laravel Sanctum**: API authentication
-   **Laravel Gates/Policies**: Authorization system
-   **Bcrypt**: Password hashing
-   **CSRF Protection**: Cross-site request forgery protection

### Email & Notifications

-   **Laravel Mail**: Email system
-   **SMTP Configuration**: Email delivery
-   **Queue System**: Asynchronous email processing

---

## User Management System

### Member Model & Roles

#### User Types (Member Types)

```php
Member::MEMBER_ROLE = [
    '1' => 'Author',
    '2' => 'Editor',
    '3' => 'Reviewer',
    '4' => 'Account',
    '5' => 'Publisher',
    '6' => 'Reviewer Final'
];
```

#### Core Member Attributes

-   **Personal Information**: Name, title, gender, date of birth
-   **Contact Details**: Email, phone, address, country, state
-   **Profile Status**: Email verification, profile completion
-   **Registration Method**: Email registration or Google OAuth
-   **Profile Picture**: Avatar image management
-   **Membership Type**: Associated with subscription tiers

#### Member Relationships

```php
- member_type() → belongsTo(MemberType::class)
- country() → belongsTo(Country::class)
- state() → belongsTo(State::class)
- member_role() → belongsTo(MemberRole::class)
- memberArticles() → hasMany(Article::class)
- purchasedArticle() → hasMany(PurchasedArticle::class)
- bookmarks() → hasMany(Bookmark::class)
```

### Authentication Flow

1. **Registration**: Email-based or Google OAuth
2. **Email Verification**: Confirmation email required
3. **Profile Completion**: Additional information gathering
4. **Role Assignment**: Based on member type and permissions
5. **Session Management**: Laravel's authentication system

### Permission System

-   **Gate Definitions**: Route-level access control
-   **Policy Classes**: Model-level authorization
-   **Role-Based Access**: MemberRole model for granular permissions
-   **Admin/Regular User Distinction**: Separate authentication guards

---

## Article Management Workflow

### Article Lifecycle

#### 1. Article Creation & Submission

```php
Article::ARTICLE_STATUS = [
    '1' => 'Pending',     // Initial submission
    '2' => 'Reviewing',   // Under editorial review
    '3' => 'Published'    // Final publication
];
```

#### 2. Access Types

```php
Article::ACCESS_TYPE = [
    '1' => 'Open Access',   // Free to read
    '2' => 'Close Access'   // Subscription required
];
```

#### 3. Article Metadata

-   **Basic Information**: Title, author details, category
-   **Publication Data**: Volume, issue, DOI, publish dates
-   **Financial Data**: Access pricing for paid articles
-   **Keywords**: Searchable article classification
-   **File Attachments**: PDF documents, supporting files

### Article Controller Workflow

#### Creation Process

```php
// Step 1: Validate input
$input = $request->validated();

// Step 2: Handle file upload
if ($request->has('pdf_doc')) {
    $file_name = uniqid() . '_' . Str::of($request->title)->slug('_');
    $path = $this->saveArticlePdf($request->file('pdf_doc'), $file_name);
    $input['file_path'] = $path;
}

// Step 3: Create main article
$article = Article::create($input);

// Step 4: Handle status changes
if($request->article_status == 3){
    $article->published_online = now();
    $article->save();
}

// Step 5: Process keywords
$articleKeywords = $request->input('keywords', []);
$keywords = $this->keywords($articleKeywords);
$article->article_keywords()->sync($keywords);

// Step 6: Create sub-article for additional data
$sub_article = SubArticle::create($input);

// Step 7: Handle paper upload
if ($paper) {
    $paper = $this->manualStoreMedia($paper)['name'];
    $sub_article->addMedia(storage_path('tmp/uploads/' . basename($paper)))->toMediaCollection('upload_paper');
}
```

#### File Management

-   **PDF Generation**: Word-to-PDF conversion using DOMPDF
-   **File Storage**: Laravel filesystem with configurable disk
-   **Media Library**: Spatie integration for media management
-   **Temporary File Handling**: Upload processing workflow

### Article Categories System

-   **Hierarchical Structure**: Parent-child category relationships
-   **Journal Organization**: Categories used for journal classification
-   **Search & Discovery**: Category-based article filtering
-   **Admin Management**: CRUD operations through admin panel

---

## Editorial & Review Process

### Multi-Stage Review System

#### Stage 1: Editor Acceptance

```php
class EditorAccept extends Model
{
    // Editor reviews initial submission
    // Approves or rejects article
    // Provides initial feedback
}
```

#### Stage 2: Reviewer Assessment

```php
class ReviewerAccept extends Model
{
    // Expert reviewer assessment
    // Technical content evaluation
    // Recommendation for publication
}
```

#### Stage 3: Final Reviewer Approval

```php
class ReviewerAcceptFinal extends Model
{
    // Final quality assurance
    // Publication readiness confirmation
    // Final approval for publishing
}
```

#### Stage 4: Publisher Decision

```php
class PublisherAccept extends Model
{
    // Final publication decision
    // Scheduling and formatting
    // Public release authorization
}
```

### Editorial Workflow Logic

1. **Initial Submission**: Author submits article (Status: Pending)
2. **Editor Assignment**: Article assigned to editor (Status: Reviewing)
3. **Editorial Review**: Editor evaluates and makes initial decision
4. **Reviewer Assignment**: Expert reviewers assigned for technical assessment
5. **Review Process**: Multiple reviewers provide feedback
6. **Final Approval**: Final reviewer confirms quality
7. **Publication**: Publisher approves for public release (Status: Published)

### Comment System

```php
class Comment extends Model
{
    // Comments throughout review process
    // Editor feedback and corrections
    // Reviewer assessments
    // Author responses
}
```

---

## Communication & Notification System

### Email Templates & Notifications

#### 1. New Article Notification

```php
class NewArticle extends Mailable
{
    // Alerts editors about new submissions
    // Contains article details and author information
    // Triggers editorial workflow
}
```

#### 2. Editorial Notifications

```php
class EditorMail extends Mailable
    // Editor-specific notifications
    // Assignment confirmations
    // Status update alerts

class ReviewerMail extends Mailable
    // Reviewer assignment notifications
    // Review deadline reminders
    // Completion confirmations

class PublisherMail extends Mailable
    // Publisher notifications
    // Final approval requests
    // Publication confirmations
```

#### 3. User Communication

```php
class ArticleMail extends Mailable
    // Author notifications
    // Status update communications
    // Publication announcements

class CommentMail extends Mailable
    // Comment notifications
    // Response alerts
    // Discussion updates
```

#### 4. System Notifications

```php
class ContactUsMail extends Mailable
    // Contact form submissions
    // Support requests
    // General inquiries

class EmailVerification extends Mailable
    // Email verification process
    // Account confirmation
    // Security alerts
```

### Email Workflow Triggers

-   **Article Submission**: Editors notified of new submissions
-   **Status Changes**: Authors notified of progress updates
-   **Review Requests**: Reviewers notified of assignment
-   **Publication**: Public announcement of new articles
-   **Account Events**: Registration, verification, password reset

---

## Content Management System

### Dynamic Content Pages

```php
class ContentPage extends Model
{
    // CMS functionality for static pages
    // About pages, policies, terms
    // SEO-friendly URLs
    // Media-rich content support
}
```

### FAQ System

```php
class FaqCategory extends Model
    // FAQ organization
    // Category-based grouping

class FaqQuestion extends Model
    // Individual FAQ items
    // Searchable content
    // Admin management interface
```

### Blog-like Content

```php
class ContentCategory extends Model
    // Content organization
    // Hierarchical structure

class ContentTag extends Model
    // Tag-based content discovery
    // Related content suggestions
```

---

## Subscription & Payment System

### Subscription Management

```php
class Subscription extends Model
{
    // Subscription plans and pricing
    // Billing cycle management
    // Feature access control
}
```

### Member Subscriptions

```php
class MemberSubscription extends Model
{
    // Individual member subscriptions
    // Payment tracking
    // Access level management
}
```

### Payment Processing

-   **Article Purchases**: Individual article access
-   **Subscription Plans**: Recurring access plans
-   **Revenue Tracking**: Financial reporting
-   **Access Control**: Content unlocking based on payment

---

## Admin Panel Functionality

### Administrative Areas

#### User Management

-   **Member Management**: CRUD operations for all users
-   **Role Assignment**: Permission management
-   **Profile Oversight**: Account status monitoring
-   **Registration Analytics**: User growth tracking

#### Content Management

-   **Article Administration**: Full article lifecycle management
-   **Category Management**: Content organization
-   **Comment Moderation**: Review and approval system
-   **Media Management**: File and image handling

#### System Configuration

-   **Settings Management**: System-wide configuration
-   **Email Configuration**: SMTP and notification settings
-   **Security Settings**: Access control configuration
-   **Backup Management**: Data protection

#### Analytics & Reporting

-   **User Analytics**: Registration and engagement metrics
-   **Content Analytics**: Article views and downloads
-   **Financial Reporting**: Revenue and subscription data
-   **Performance Monitoring**: System health metrics

### Admin Controllers Structure

```php
// Core Management
UsersController.php          // User management
PermissionsController.php    // Permission system
RolesController.php          // Role management
SettingsController.php       // System settings

// Content Management
ArticlesController.php       // Article management
ContentPagesController.php   // CMS functionality
CommentsController.php       // Comment moderation
FaqController.php           // FAQ management

// Organization
CountriesController.php      // Geographic data
StatesController.php         // Administrative regions
MemberTypesController.php    // User type definitions
MemberRolesController.php    // Role definitions
```

---

## AfriScribe Module

### Purpose & Functionality

**AfriScribe** appears to be a specialized module within the Research Africa platform, likely designed for manuscript writing and submission assistance.

### AfriScribe Architecture

```php
app/Modules/AfriScribe/
├── Http/
│   ├── Controllers/
│   │   └── AfriscribeController.php
│   └── routes.php
└── [Additional module structure]
```

### AfriScribe Workflow

1. **Landing Page**: `/afriscribe/home` - Welcome interface
2. **Admin Dashboard**: `/afriscribe/admin` - Administrative interface
3. **Request Management**: Handle AfriScribe service requests
4. **Integration**: Seamless integration with main platform

### Routes Structure

```php
// AfriScribe specific routes
Route::redirect('/afriscribe', '/afriscribe/home');
Route::get('/afriscribe/home', function () {
    return view('afriscribe.welcome-form');
})->name('afriscribe.welcome');

Route::get('/afriscribe/admin', [\App\Modules\AfriScribe\Http\Controllers\AfriscribeController::class, 'dashboard'])
    ->name('afriscribe.admin.dashboard');
```

---

## Database Schema & Relationships

### Core Entity Relationships

#### Article Flow

```sql
articles (1) ←→ (n) sub_articles
articles (n) ←→ (n) article_keywords
articles (1) ←→ (1) editor_accepts
articles (1) ←→ (1) reviewer_accepts
articles (1) ←→ (1) reviewer_accept_finals
articles (1) ←→ (1) publisher_accepts
articles (1) ←→ (n) comments
```

#### Member Relationships

```sql
members (n) ←→ (n) articles
members (n) ←→ (n) purchased_articles
members (n) ←→ (n) bookmarks
members (1) ←→ (n) member_subscriptions
members (n) ←→ (1) member_types
members (n) ←→ (1) member_roles
members (n) ←→ (1) countries
members (n) ←→ (1) states
```

#### Content Organization

```sql
article_categories (n) ←→ (n) articles
article_categories (n) ←→ (n) article_sub_categories
content_categories (n) ←→ (n) content_pages
content_tags (n) ←→ (n) content_pages
faq_categories (n) ←→ (n) faq_questions
```

### Key Database Tables

#### Articles Table

```sql
articles:
- id, member_id, article_category_id
- title, author_name, other_authors
- access_type, amount, doi_link
- volume, issue_no, publish_date
- article_status, file_path, storage_disk
- created_at, updated_at, deleted_at
```

#### Members Table

```sql
members:
- id, email_address, password
- title, first_name, middle_name, last_name
- member_type_id, member_role_id
- country_id, state_id
- email_verified, verified, profile_completed
- created_at, updated_at, deleted_at
```

#### Editorial Tables

```sql
editor_accepts:
- id, article_id, member_id, created_at

reviewer_accepts:
- id, article_id, member_id, created_at

reviewer_accept_finals:
- id, article_id, member_id, created_at

publisher_accepts:
- id, article_id, member_id, created_at
```

---

## Security & Permissions

### Authentication Guards

```php
// Multiple authentication contexts
'web' => \App\Http\Middleware\Authenticate::class
'member' => \App\Http\Middleware\AuthenticateMember::class
'admin' => \App\Http\Middleware\AdminMiddleware::class
```

### Authorization System

```php
// Gate definitions for permissions
Gate::define('article_access', function ($user) {
    return $user->hasPermissionTo('article_access');
});

Gate::define('article_create', function ($user) {
    return $user->hasPermissionTo('article_create');
});

// Policy-based authorization
class ArticlePolicy
{
    public function view(User $user, Article $article) { }
    public function create(User $user) { }
    public function update(User $user, Article $article) { }
    public function delete(User $user, Article $article) { }
}
```

### Middleware Protection

-   **Route Protection**: Authentication requirements
-   **Permission Gates**: Fine-grained access control
-   **CSRF Protection**: Form security
-   **Input Validation**: Request sanitization
-   **File Upload Security**: Media file validation

---

## File Management & Media Handling

### Media Library Integration

```php
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use InteractsWithMedia;

    // Media collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('upload_paper')->singleFile();
        $this->addMediaCollection('ck-media');
        $this->addMediaCollection('pdf_doc');
    }
}
```

### File Processing Workflow

1. **Upload**: Temporary file storage
2. **Validation**: File type and size checks
3. **Processing**: PDF conversion if needed
4. **Storage**: Permanent file storage
5. **Media Registration**: Database record creation
6. **Access**: Secure file serving

### File Storage Configuration

```php
// Configurable storage disks
'local' => [
    'driver' => 'local',
    'root' => storage_path('app'),
],
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
],
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
],
```

---

## API Structure

### API Routes

```php
// API route files
routes/
├── api.php              // Main API routes
├── admin.php           // Admin API endpoints
├── member.php          // Member API endpoints
└── quote_requests.php  // Quote request endpoints
```

### API Authentication

-   **Laravel Sanctum**: Token-based authentication
-   **API Routes**: Protected endpoints
-   **Rate Limiting**: Request throttling
-   **CORS Configuration**: Cross-origin resource sharing

### API Endpoints Structure

```
/api/v1/
├── articles/           // Article management
├── members/           // User management
├── categories/        // Content categories
├── subscriptions/     // Subscription management
├── payments/          // Payment processing
└── analytics/         // Reporting endpoints
```

---

## Integration Points

### External Services

-   **Email Service**: SMTP configuration for notifications
-   **Payment Gateway**: Subscription and payment processing
-   **Google OAuth**: Social authentication
-   **Cloud Storage**: File storage and CDN

### Third-party Libraries

-   **DOMPDF**: PDF generation and conversion
-   **PhpSpreadsheet**: Excel file processing
-   **Spatie Packages**: Media and permissions management
-   **Carbon**: Date and time handling

### System Integrations

-   **Queue System**: Asynchronous job processing
-   **Cache System**: Performance optimization
-   **Logging System**: Error tracking and monitoring
-   **Backup System**: Data protection and recovery

---

## Workflow Summary

### Complete Article Lifecycle

1. **Submission Phase**

    - Author creates account and completes profile
    - Article submission with metadata and files
    - Automatic notifications to editors

2. **Editorial Review Phase**

    - Editor assignment and notification
    - Initial editorial review and decision
    - Reviewer assignment based on expertise

3. **Peer Review Phase**

    - Reviewer notifications and deadline tracking
    - Review submission and feedback compilation
    - Multiple reviewer coordination

4. **Final Review Phase**

    - Final reviewer assessment
    - Quality assurance checks
    - Publication readiness confirmation

5. **Publication Phase**

    - Publisher final approval
    - Article formatting and metadata completion
    - Public release and notification

6. **Post-Publication**
    - Access control based on subscription/payment
    - Analytics and performance tracking
    - Comment and discussion management

### User Journey Mapping

#### Author Journey

1. **Registration** → Email verification → Profile completion
2. **Article submission** → Status tracking → Review feedback
3. **Revision process** → Resubmission → Publication notification
4. **Post-publication** → Analytics access → Citation tracking

#### Editor Journey

1. **Assignment notification** → Article review → Initial decision
2. **Reviewer selection** → Feedback compilation → Decision making
3. **Author communication** → Revision management → Final recommendation
4. **Quality oversight** → Publication approval → Performance monitoring

#### Reviewer Journey

1. **Assignment notification** → Expertise confirmation → Review deadline
2. **Article assessment** → Feedback submission → Quality scoring
3. **Follow-up reviews** → Revision assessment → Final recommendation
4. **Ongoing participation** → Performance tracking → Recognition

#### Subscriber Journey

1. **Account creation** → Subscription selection → Payment processing
2. **Content access** → Search and discovery → Reading and downloading
3. **Personalization** → Bookmarking → Recommendation engine
4. **Engagement** → Comments → Citation sharing

---

## System Performance & Scalability

### Caching Strategy

-   **Route Caching**: Optimized route resolution
-   **Config Caching**: Configuration optimization
-   **View Caching**: Compiled blade templates
-   **Database Query Caching**: Frequently accessed data

### Database Optimization

-   **Indexing Strategy**: Optimized query performance
-   **Eager Loading**: Relationship query optimization
-   **Pagination**: Large dataset handling
-   **Query Optimization**: Efficient database operations

### File System Optimization

-   **CDN Integration**: Static asset delivery
-   **Image Optimization**: Automated image processing
-   **File Compression**: Storage and bandwidth optimization
-   **Lazy Loading**: On-demand content delivery

---

## Development & Deployment

### Development Environment

-   **Local Setup**: Laravel development server
-   **Database Seeding**: Test data generation
-   **Testing Framework**: PHPUnit and Dusk testing
-   **Code Quality**: Laravel Pint code formatting

### Deployment Process

-   **Environment Configuration**: Production settings
-   **Database Migration**: Schema updates
-   **Asset Compilation**: Frontend build process
-   **Queue Deployment**: Background job processing

### Monitoring & Maintenance

-   **Error Logging**: Comprehensive error tracking
-   **Performance Monitoring**: System health metrics
-   **Security Updates**: Regular security patches
-   **Backup Procedures**: Data protection protocols

---

## Conclusion

The Research Africa platform represents a comprehensive academic publishing ecosystem with sophisticated editorial workflows, robust user management, and scalable architecture. The system effectively handles the complete article lifecycle from submission to publication while maintaining security, performance, and user experience standards.

### Key Strengths

-   **Comprehensive Workflow**: Complete editorial process management
-   **Multi-role Support**: Sophisticated user role and permission system
-   **Scalable Architecture**: Built for growth and expansion
-   **Security Focus**: Robust authentication and authorization
-   **Integration Ready**: Extensible design for future enhancements

### Areas for Enhancement

-   **Multi-journal Isolation**: Improved journal-specific data separation
-   **Advanced Analytics**: Enhanced reporting and insights
-   **API Documentation**: Comprehensive API specification
-   **Mobile Optimization**: Enhanced mobile user experience
-   **Internationalization**: Multi-language support

This documentation provides a foundation for understanding, maintaining, and enhancing the Research Africa platform while ensuring continued growth and success in academic publishing.
