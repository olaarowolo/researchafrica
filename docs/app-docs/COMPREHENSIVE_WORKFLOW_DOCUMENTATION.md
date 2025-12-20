# Research Africa - Comprehensive Workflow and Functionality Documentation

## Table of Contents

1. [System Overview](#system-overview)
2. [Architecture & Technology Stack](#architecture--technology-stack)
3. [User Management System](#user-management-system)
4. [Article Management Workflow](#article-management-workflow)
5. [AfriScribe Proofreading Service](#afriscribe-proofreading-service)
6. [Email Notification System](#email-notification-system)
7. [Role-Based Access Control](#role-based-access-control)
8. [File Management System](#file-management-system)
9. [Database Schema](#database-schema)
10. [Admin Dashboard Features](#admin-dashboard-features)
11. [Member Portal Features](#member-portal-features)
12. [API Endpoints](#api-endpoints)
13. [Testing Framework](#testing-framework)
14. [Configuration & Deployment](#configuration--deployment)
15. [Security Features](#security-features)
16. [Performance Optimizations](#performance-optimizations)

---

## System Overview

Research Africa is a comprehensive Laravel-based academic journal management platform designed to streamline scholarly publishing in Africa. The system integrates multiple subsystems:

### Core Components:

-   **Main Journal Platform**: Article submission, peer review, and publication management
-   **AfriScribe Module**: Professional proofreading and editing services
-   **Member Management**: User registration, authentication, and subscription handling
-   **Admin Dashboard**: Comprehensive administrative controls and analytics
-   **Email System**: Automated notifications and communications

### Key Features:

-   Multi-stage peer review workflow (Editor → Reviewer → Publisher)
-   Role-based access control with granular permissions
-   File upload and media management
-   Subscription-based monetization
-   Location-based pricing for AfriScribe services
-   Comprehensive audit trails and logging

---

## Architecture & Technology Stack

### Backend Framework

-   **Laravel 10.x**: PHP framework providing MVC architecture
-   **PHP 8.1+**: Modern PHP features and performance improvements
-   **MySQL**: Primary database for all data persistence
-   **Laravel Sanctum**: API authentication and session management

### Frontend Technologies

-   **Livewire 2.12**: Reactive components for dynamic interfaces
-   **Tailwind CSS**: Utility-first CSS framework
-   **Bootstrap**: Responsive UI components
-   **Vite**: Modern build tool and development server
-   **Blade Templates**: Laravel's templating engine

### Key Dependencies

```json
{
    "barryvdh/laravel-dompdf": "^2.0", // PDF generation
    "laraveldaily/laravel-charts": "^0.1.29", // Data visualization
    "spatie/laravel-medialibrary": "^10.7", // Media management
    "phpoffice/phpword": "^1.0", // Word document processing
    "artesaos/seotools": "dev-master", // SEO management
    "nuovo/spreadsheet-reader": "^0.5.11" // CSV import functionality
}
```

### Project Structure

```
research-africa/
├── app/
│   ├── Http/Controllers/          # Application controllers
│   ├── Models/                    # Eloquent models
│   ├── Modules/AfriScribe/       # Modular AfriScribe service
│   ├── Mail/                     # Email classes
│   ├── Http/Requests/            # Form validation
│   └── Services/                 # Business logic services
├── database/migrations/           # Database schema
├── resources/views/              # Blade templates
├── routes/                       # Route definitions
├── public/                       # Static assets
└── storage/                      # File storage
```

---

## User Management System

### User Types & Roles

#### 1. Regular Members (Authors)

-   **Registration**: Email-based signup with verification
-   **Profile Management**: Complete personal and academic information
-   **Article Submission**: Upload and manage research papers
-   **Subscription Management**: Access premium content and features

#### 2. Administrative Roles

-   **Editors**: Review and approve submitted articles
-   **Reviewers**: Conduct peer reviews and provide feedback
-   **Publishers**: Final approval and publication decisions
-   **Super Admins**: Full system access and configuration

### Member Registration Workflow

```php
// Registration Process
1. User fills registration form
2. System validates input data
3. Creates member record with hashed password
4. Generates email verification token
5. Sends verification email
6. User clicks verification link
7. Account activated and accessible
```

### Authentication System

```php
// Authentication Flow
1. User submits login credentials
2. System validates against database
3. Creates authenticated session
4. Redirects to appropriate dashboard
5. Session management with Laravel Sanctum
```

### Member Profile Management

**Fields Managed:**

-   Personal Information (Name, Title, Gender, DOB)
-   Contact Details (Email, Phone, Address)
-   Academic Information (Institution, Position)
-   Profile Picture (Media Library integration)
-   Preferences and Settings

**Member Types:**

-   Authors (Type 1): Can submit articles
-   Editors (Type 2): Can review and edit content
-   Reviewers (Type 3): Can review articles
-   Subscribers (Type 4): Premium access members

---

## Article Management Workflow

### Article Submission Process

#### 1. Initial Submission

```php
// Article Creation Workflow
1. Member logs into dashboard
2. Navigates to "Submit Article"
3. Fills article metadata form:
   - Title and abstract
   - Author information
   - Keywords and categories
   - File upload (PDF/DOC)
4. System validates and stores article
5. Status set to "Pending" (Status 1)
6. Email notifications sent to editors
```

#### 2. Article Status Progression

| Status | Name      | Description               | Transitions |
| ------ | --------- | ------------------------- | ----------- |
| 1      | Pending   | Awaiting initial review   | → Reviewing |
| 2      | Reviewing | Under peer review process | → Published |
| 3      | Published | Live and accessible       | Final state |

#### 3. Multi-Stage Review Process

```php
// Review Workflow
Stage 1: Editorial Review
- Editor assigned to article
- Initial quality assessment
- Forward to reviewer or reject

Stage 2: Peer Review
- Reviewer evaluates content
- Provides feedback and comments
- Recommends acceptance/rejection

Stage 3: Publisher Decision
- Final approval by publisher
- Sets publication date
- Makes article live
```

### Article Model Relationships

```php
// Key Relationships
Article belongsTo Member (author)
Article belongsTo ArticleCategory
Article hasMany Comments
Article hasMany SubArticle
Article hasMany ArticleKeyword (many-to-many)
Article hasOne ViewArticle (analytics)
Article hasOne DownloadArticle (tracking)
```

### File Management

**Supported Formats:**

-   PDF documents (primary format)
-   Word documents (.doc, .docx)
-   Media files for rich content

**Storage Strategy:**

-   Local storage with Laravel Filesystem
-   Media Library for organized file management
-   Automatic PDF conversion from Word
-   File size optimization and validation

### Access Control

**Open Access Articles:**

-   Freely available to all users
-   No payment required
-   Full download permissions

**Closed Access Articles:**

-   Subscription or purchase required
-   Restricted download access
-   Monetization through payments

---

## AfriScribe Proofreading Service

### Service Overview

AfriScribe is a specialized subsystem providing professional academic proofreading services integrated into the Research Africa platform.

### Service Types

#### 1. Proofreading Services

-   **Standard Proofreading**: Basic grammar and style correction
-   **Advanced Proofreading**: In-depth language editing
-   **Express Service**: Rush delivery options

#### 2. Location-Based Pricing

-   **UK Clients**: Higher pricing tier
-   **Nigeria Clients**: Local pricing
-   **Other Regions**: Standard international rates

### Quote Request Workflow

```php
// Quote Request Process
1. Client visits /afriscribe/home
2. Fills service request form:
   - Document details
   - Word count
   - Service type
   - Turnaround time
3. System calculates pricing
4. Creates AfriScribeRequest record
5. Sends confirmation emails:
   - Admin notification
   - Client acknowledgment
6. Admin reviews and responds
```

### AfriScribe Request Management

```php
// Request Status Flow
STATUS_PENDING → STATUS_PROCESSING → STATUS_COMPLETED

- Pending: New request awaiting review
- Processing: Work in progress
- Completed: Service delivered
```

### File Processing

**Supported File Types:**

-   PDF documents
-   Word documents (.doc, .docx)
-   Plain text files

**Processing Features:**

-   Automatic file validation
-   Virus scanning
-   Secure file storage
-   Version tracking

---

## Email Notification System

### Email Classes Architecture

The system uses Laravel's Mail system with custom Mailable classes for different notification types.

#### 1. User Management Emails

```php
// Email Types
EmailVerification: Account verification links
ResetPassword: Password reset functionality
ContactUsMail: Contact form submissions
```

#### 2. Article Management Emails

```php
ArticleMail: General article notifications
NewArticle: New submission confirmations
PublishArticle: Publication announcements
EditorMail: Editorial assignments
ReviewerMail: Review invitations
CommentMail: Review feedback
```

#### 3. AfriScribe Service Emails

```php
AfriscribeRequestMail: Service requests to admin
QuoteRequestMail: Quote notifications
QuoteRequestClientAcknowledgementMail: Client confirmations
```

#### 4. Workflow Emails

```php
AcceptedMail: Acceptance confirmations
ForwardedArticle: Article forwarding
PublisherMail: Publisher notifications
```

### Email Configuration

```php
// Mail Configuration (.env)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@afriscribe.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Email Sending Workflow

```php
// Email Process
1. Trigger event (user registration, article submission)
2. Create Mailable instance with data
3. Queue email for background processing
4. Send via configured mail driver
5. Log delivery status and errors
6. Handle bounces and failures
```

---

## Role-Based Access Control

### Permission System

The system implements granular permissions using Laravel Gates and Policies.

#### Core Permissions

```php
// User Management
member_access, member_create, member_edit, member_show, member_delete
user_access, user_create, user_edit, user_show, user_delete

// Article Management
article_access, article_create, article_edit, article_show, article_delete
article_review, article_publish, article_download

// System Administration
permission_access, permission_create, permission_edit, permission_show, permission_delete
role_access, role_create, role_edit, role_show, role_delete

// Content Management
content_access, content_create, content_edit, content_show, content_delete
faq_access, faq_create, faq_edit, faq_show, faq_delete
```

### Role Hierarchy

```php
// Role Definitions
Super Admin: Full system access
Admin: Administrative functions
Editor: Article review and editing
Reviewer: Peer review capabilities
Author: Article submission
Subscriber: Read-only access
```

### Middleware Protection

```php
// Route Protection
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin-only routes
});

Route::middleware(['auth:member'])->group(function () {
    // Member-only routes
});
```

---

## File Management System

### Media Library Integration

Using Spatie's Laravel Media Library for comprehensive file management.

#### File Collections

```php
// Media Collections
profile_picture: User profile images
upload_paper: Article document uploads
ck-media: CKEditor rich media content
correction_upload: Review correction files
afriscribe_uploads: Proofreading service files
```

### File Upload Workflow

```php
// Upload Process
1. Validate file type and size
2. Generate unique filename
3. Store in appropriate collection
4. Generate thumbnails (if applicable)
5. Link to parent model
6. Return media URLs
```

### File Storage Strategy

```php
// Storage Configuration
Local Storage: Default storage driver
Public Disk: Web-accessible files
Private Storage: Secure file access
Cloud Integration: Ready for cloud storage
```

### File Security

-   **Virus Scanning**: Automatic malware detection
-   **Access Control**: Role-based file permissions
-   **Secure URLs**: Temporary signed URLs
-   **File Validation**: Type and content validation

---

## Database Schema

### Core Tables

#### Users & Authentication

```sql
users: System administrators
members: Journal members/authors
password_resets: Password reset tokens
email_verifies: Email verification tokens
```

#### Content Management

```sql
articles: Main article records
sub_articles: Article versions/edits
comments: Review comments and feedback
article_categories: Content classification
article_keywords: SEO and discoverability
```

#### Workflow Management

```sql
editor_accepts: Editorial approvals
reviewer_accepts: Peer review decisions
publisher_accepts: Final publication approval
reviewer_accept_finals: Final review status
```

#### User Management

```sql
member_types: User classifications
member_roles: Role definitions
member_subscriptions: Subscription tracking
countries, states: Geographic data
```

#### AfriScribe Module

```sql
afriscribe_requests: Proofreading service requests
quote_requests: Service quote management
```

#### System Tables

```sql
permissions: Granular access control
roles: Role definitions
permission_role_pivot: Role-permission mapping
role_user_pivot: User-role assignments
settings: System configuration
media: File management (Spatie)
```

### Key Relationships

```php
// Primary Relationships
Member hasMany Article
Article belongsTo Member
Article belongsTo ArticleCategory
Article hasMany Comment
Comment belongsTo Article
Member belongsTo MemberType
Member belongsTo Country
Article hasMany SubArticle
```

---

## Admin Dashboard Features

### Dashboard Analytics

```php
// Key Metrics
- Total Users: All registered members
- Total Articles: All submitted papers
- Total Members: Active subscribers
- Total Comments: Review feedback
- Pending Articles: Awaiting review
- Published Articles: Live content
```

### Content Management

#### Article Management

```php
// Admin Article Functions
- View all articles with filters
- Edit article metadata
- Manage article status
- Upload/replace files
- Assign to editors
- Publish articles
```

#### User Management

```php
// Admin User Functions
- View all members
- Edit member profiles
- Manage subscriptions
- Handle email verification
- Reset passwords
- Delete/deactivate accounts
```

#### Content Pages

```php
// CMS Functions
- Manage About pages
- FAQ system administration
- Content categories
- SEO settings
- Media library
```

### System Configuration

```php
// Settings Management
- Site-wide configuration
- Email templates
- Payment settings
- Security options
- Backup settings
```

---

## Member Portal Features

### Dashboard Overview

```php
// Member Dashboard
- Recent articles
- Review assignments
- Subscription status
- Profile completion
- Activity feed
```

### Article Submission

```php
// Submission Process
1. Upload manuscript
2. Provide metadata
3. Select category
4. Add keywords
5. Set access type
6. Submit for review
```

### Profile Management

```php
// Profile Features
- Personal information
- Academic details
- Profile picture upload
- Email preferences
- Password changes
```

### Review System

```php
// Review Workflow
Assigned Review → Review Article → Submit Feedback → Recommend Action
```

### Comment System

```php
// Collaborative Features
- Article commenting
- Review discussions
- File attachments
- Threaded responses
```

---

## API Endpoints

### Authentication Endpoints

```php
// Member Authentication
POST /api/login          # User login
POST /api/register       # User registration
POST /api/logout         # User logout
POST /api/forgot-password # Password reset request
```

### Article Endpoints

```php
// Article Management
GET  /api/articles       # List articles
POST /api/articles       # Create article
GET  /api/articles/{id}  # Get article details
PUT  /api/articles/{id}  # Update article
DELETE /api/articles/{id} # Delete article
```

### AfriScribe Endpoints

```php
// Proofreading Service
POST /api/afriscribe/quote     # Request quote
GET  /api/afriscribe/requests  # List requests
PUT  /api/afriscribe/{id}/status # Update status
```

### Admin Endpoints

```php
// Admin Functions
GET  /api/admin/dashboard      # Dashboard data
GET  /api/admin/users          # User management
PUT  /api/admin/users/{id}     # Update user
GET  /api/admin/articles       # Article management
```

---

## Testing Framework

### Test Structure

```php
// Test Categories
Feature Tests: HTTP endpoint testing
Unit Tests: Individual component testing
Browser Tests: End-to-end testing (Laravel Dusk)
```

### Key Test Files

```php
// Core Tests
tests/Feature/QuoteRequestTest.php    # AfriScribe functionality
tests/Feature/UserTest.php            # User management
tests/Feature/ArticleTest.php         # Article workflow
tests/Browser/                        # UI/UX testing
```

### Running Tests

```bash
# Test Commands
php artisan test                    # Run all tests
php artisan test --coverage        # With coverage report
php artisan dusk                   # Browser testing
```

---

## Configuration & Deployment

### Environment Setup

```bash
# Installation Steps
1. Clone repository
2. Install dependencies: composer install
3. Configure environment: cp .env.example .env
4. Generate key: php artisan key:generate
5. Migrate database: php artisan migrate
6. Seed data: php artisan db:seed
7. Link storage: php artisan storage:link
8. Build assets: npm install && npm run build
```

### Production Deployment

```bash
# Production Commands
php artisan optimize              # Optimize for production
php artisan config:cache         # Cache configuration
php artisan route:cache          # Cache routes
php artisan view:cache           # Cache views
composer dump-autoload --optimize # Optimize autoloader
```

### Queue Configuration

```php
// Queue Setup for Background Jobs
php artisan queue:work           # Start queue worker
php artisan queue:listen         # Listen for jobs
php artisan schedule:run         # Run scheduled tasks
```

---

## Security Features

### Authentication Security

```php
// Security Measures
- Password hashing (bcrypt)
- CSRF protection
- Session security
- Rate limiting
- Two-factor authentication ready
```

### File Security

```php
// File Protection
- Virus scanning on upload
- File type validation
- Secure file storage
- Access control
- Signed URLs for downloads
```

### Data Protection

```php
// Privacy Features
- Data encryption
- Secure backups
- Audit logging
- GDPR compliance ready
- Anonymization options
```

---

## Performance Optimizations

### Database Optimizations

```php
// Performance Features
- Eager loading relationships
- Database indexing
- Query optimization
- Connection pooling
- Caching strategies
```

### Caching Strategy

```php
// Caching Layers
Route caching
Configuration caching
View caching
Query result caching
Session optimization
```

### File Optimization

```php
// Asset Optimization
- Minification
- Compression
- CDN ready
- Lazy loading
- Image optimization
```

---

## Monitoring & Maintenance

### Logging System

```php
// Log Categories
Application logs: General application events
Error logs: System errors and exceptions
Access logs: User activity tracking
Email logs: Communication tracking
```

### Backup Strategy

```php
// Backup Components
Database backups
File storage backups
Configuration backups
Log file rotation
```

### Performance Monitoring

```php
// Metrics Tracking
Response times
Database performance
Memory usage
Queue processing
Email delivery rates
```

---

## Conclusion

The Research Africa platform is a comprehensive academic journal management system built on modern web technologies. Its modular architecture, robust workflow management, and comprehensive feature set make it suitable for academic institutions, research organizations, and publishing companies across Africa and beyond.

The system's strength lies in its integration of multiple services (article management, proofreading services, user management) into a cohesive platform that streamlines the entire academic publishing process from manuscript submission to final publication.

Key strengths include:

-   **Scalable Architecture**: Built to handle growth and increased load
-   **Security First**: Comprehensive security measures throughout
-   **User Experience**: Intuitive interfaces for all user types
-   **Flexibility**: Modular design allows for easy customization
-   **Integration Ready**: APIs and webhooks for third-party integrations
-   **Compliance**: Built with academic standards and regulations in mind

The platform continues to evolve with new features planned including payment gateway integration, advanced search capabilities, mobile applications, and enhanced analytics dashboards.
