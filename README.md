# Research Africa - Academic Journal Management Platform

Research Africa is a comprehensive Laravel-based platform designed to streamline academic journal management and scholarly publishing in Africa. The platform includes multiple subsystems including the AfriScribe proofreading service, article management, peer review workflows, and member services.

## 🌍 About Research Africa

Research Africa serves as a comprehensive digital ecosystem for African scholarly communication, providing researchers, academics, and institutions with tools to publish, review, and access quality research content. The platform bridges the gap between academic research and professional publishing services.

### AfriScribe Proofreading Service

AfriScribe is a specialized subsystem within Research Africa that provides professional proofreading services for academic documents. Accessible via `/afriscribe-welcome`, it offers location-based pricing and document processing capabilities.

### Key Features

#### 📝 Article Management System

- **Article Submission**: Authors can submit research papers with comprehensive metadata
- **Multi-stage Review Process**: Editorial workflow with Editor → Reviewer → Publisher stages
- **Article Categories**: Organized content management with categories and keywords
- **Access Control**: Open Access and Closed Access publication options
- **DOI Integration**: Digital Object Identifier support for published articles

#### 👥 User Management & Roles

- **Member System**: User registration with different subscription tiers
- **Role-based Access**: Members, Editors, Publishers, and Reviewers
- **Permission Management**: Granular permissions for different user types
- **Profile Management**: Comprehensive user profiles and preferences

#### 🔍 Review & Quality Control

- **Peer Review System**: Structured review process with feedback mechanisms
- **Comment System**: Collaborative commenting on articles
- **Quality Assurance**: Multi-level approval workflow
- **Revision Tracking**: Track article revisions and updates

#### 💼 AfriScribe Proofreading Service

- **Professional Proofreading**: Integrated academic proofreading services
- **Location-based Pricing**: Different rates for UK and Nigeria clients
- **Dynamic Cost Calculation**: Real-time pricing based on word count and service type
- **File Processing**: Support for multiple document formats (PDF, Word, etc.)
- **Email Integration**: Automated client communication and file handling

#### 💰 Monetization Features

- **Subscription System**: Tiered membership with different access levels
- **Article Marketplace**: Paid access to premium research content
- **Download Tracking**: Analytics for article access and downloads
- **Purchase History**: Complete transaction and access history

## 🛠 Technical Stack

### Backend

- **Framework**: Laravel 10.x
- **PHP Version**: PHP 8.1 or higher
- **Database**: MySQL
- **Queue System**: Laravel Queue for background processing

### Frontend

- **Livewire**: Reactive components for dynamic interfaces
- **Tailwind CSS**: Utility-first CSS framework
- **Bootstrap**: Responsive UI framework
- **JavaScript**: Vanilla JS with jQuery support
- **Vite**: Fast build tool and development server
- **Blade Templates**: Laravel's templating engine

### Key Dependencies

- **PDF Generation**: barryvdh/laravel-dompdf
- **Charts**: laraveldaily/laravel-charts
- **Media Management**: spatie/laravel-medialibrary
- **Document Processing**: phpoffice/phpword
- **Authentication**: Laravel Sanctum
- **Data Tables**: yajra/laravel-datatables-oracle
- **File Management**: spatie/laravel-permission
- **Image Processing**: intervention/image
- **SEO Management**: artesaos/seotools
- **Backup**: spatie/laravel-backup
- **Queue Management**: Laravel Queue
- **Testing**: Laravel Dusk, PHPUnit
- **API Documentation**: Laravel API Resource

## 📋 Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL 5.7 or higher
- Node.js and npm (for frontend assets)

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/afriscribe.git
cd afriscribe
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup

```bash
# Configure your database in .env file
php artisan migrate
php artisan db:seed
```

### 5. Storage Setup

```bash
php artisan storage:link
mkdir -p storage/app/afriscribe_uploads
```

### 6. Frontend Assets

```bash
npm install
npm run build
```

### 7. Queue Configuration (Optional)

```bash
php artisan queue:work
```

## ⚙️ Configuration

### Email Configuration

Configure your mail settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@afriscribe.com
MAIL_FROM_NAME="${APP_NAME}"
```

### File Upload Configuration

Ensure proper permissions for storage directories:

```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## 🏃‍♂️ Usage

### Starting the Application

```bash
php artisan serve
```

### Running Tests

```bash
php artisan test
```

### Queue Processing

```bash
php artisan queue:work
```

## 📁 Project Structure

```
research-africa/
├── app/
│   ├── Console/                    # Artisan commands
│   │   ├── Commands/              # Custom Artisan commands
│   │   └── Kernel.php             # Console kernel
│   ├── Exceptions/                 # Exception handlers
│   │   └── Handler.php            # Global exception handler
│   ├── Http/
│   │   ├── Controllers/           # Main application controllers
│   │   │   ├── Auth/              # Authentication controllers
│   │   │   ├── Controller.php     # Base controller
│   │   │   └── Livewire/          # Livewire components
│   │   ├── Kernel.php             # HTTP kernel
│   │   ├── Middleware/            # Route middleware
│   │   │   ├── Authenticate.php   # Authentication middleware
│   │   │   └── RedirectIfAuthenticated.php
│   │   └── Requests/              # Form request validation
│   ├── Mail/                      # Email classes
│   │   ├── AcceptedMail.php       # Acceptance notifications
│   │   ├── ArticleMail.php        # Article notifications
│   │   ├── CommentMail.php        # Review comments
│   │   ├── ContactUsMail.php      # Contact form emails
│   │   ├── EditorMail.php         # Editor notifications
│   │   ├── EmailVerification.php  # Email verification
│   │   ├── ForwardedArticle.php   # Article forwarding
│   │   ├── NewArticle.php        # New article notifications
│   │   ├── PublishArticle.php     # Publication notifications
│   │   ├── PublisherMail.php      # Publisher notifications
│   │   ├── QuoteRequestClientAcknowledgementMail.php
│   │   ├── QuoteRequestMail.php   # Quote request emails
│   │   ├── ResetPassword.php     # Password reset
│   │   └── ReviewerMail.php       # Reviewer notifications
│   ├── Models/                    # Eloquent models
│   │   ├── About.php              # About page content
│   │   ├── AfriscribeRequest.php  # AfriScribe requests
│   │   ├── Article.php            # Main article model
│   │   ├── ArticleCategory.php    # Article categories
│   │   ├── ArticleKeyword.php     # Article keywords
│   │   ├── Bookmark.php           # User bookmarks
│   │   ├── Comment.php            # Article comments
│   │   ├── ContentCategory.php    # Content categories
│   │   ├── ContentPage.php        # Content pages
│   │   ├── ContentTag.php         # Content tags
│   │   ├── Country.php            # Country data
│   │   ├── DownloadArticle.php    # Article downloads
│   │   ├── EditorAccept.php       # Editor acceptances
│   │   ├── EmailVerify.php        # Email verification
│   │   ├── FaqCategory.php        # FAQ categories
│   │   ├── FaqQuestion.php        # FAQ questions
│   │   ├── Member.php             # User members
│   │   ├── MemberRole.php         # Member roles
│   │   ├── MemberSubscription.php # Member subscriptions
│   │   ├── MemberType.php         # Member types
│   │   ├── Permission.php         # User permissions
│   │   ├── PublisherAccept.php    # Publisher acceptances
│   │   ├── PurchasedArticle.php   # Purchased articles
│   │   ├── QuoteRequest.php       # Quote requests
│   │   ├── ResetPassword.php      # Password reset
│   │   ├── ReviewerAccept.php     # Reviewer acceptances
│   │   ├── ReviewerAcceptFinal.php # Final reviewer acceptances
│   │   ├── Role.php               # User roles
│   │   ├── Setting.php            # Application settings
│   │   ├── State.php              # State data
│   │   ├── SubArticle.php         # Sub articles
│   │   ├── Subscription.php       # Subscriptions
│   │   ├── User.php               # Users
│   │   └── ViewArticle.php        # Article views
│   ├── Modules/                   # Modular application structure
│   │   └── AfriScribe/           # AfriScribe proofreading module
│   │       ├── Http/
│   │       │   ├── Controllers/  # Module controllers
│   │       │   │   ├── AfriscribeController.php
│   │       │   │   └── QuoteRequestController.php
│   │       │   ├── Middleware/   # Module middleware
│   │       │   └── routes.php     # Module routes
│   │       └── Mail/             # Module-specific emails
│   │           ├── AfriscribeRequestMail.php
│   │           └── QuoteRequestMail.php
│   ├── Providers/                 # Service providers
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── BroadcastServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   ├── Services/                  # Business logic services
│   │   └── ArticleService.php    # Article business logic
│   └── View/                     # View components
│       └── Components/           # Blade components
├── bootstrap/                     # Laravel bootstrap files
│   ├── app.php                   # Application bootstrap
│   └── cache/                    # Cache files
├── config/                        # Configuration files
│   ├── app.php                   # Application settings
│   ├── auth.php                  # Authentication config
│   ├── broadcasting.php          # Broadcasting config
│   ├── cache.php                 # Cache configuration
│   ├── cors.php                  # CORS settings
│   ├── database.php              # Database configuration
│   ├── filesystems.php           # Filesystem config
│   ├── hashing.php               # Hash configuration
│   ├── logging.php               # Logging configuration
│   ├── mail.php                  # Email configuration
│   ├── panel.php                 # Admin panel config
│   ├── queue.php                 # Queue configuration
│   ├── sanctum.php               # Sanctum API config
│   ├── services.php              # Third-party services
│   ├── session.php               # Session configuration
│   └── view.php                  # View configuration
├── database/
│   ├── factories/                # Model factories
│   │   └── Modules/             # Module-specific factories
│   │       └── AfriScribe/
│   │           └── Models/
│   │               └── QuoteRequestFactory.php
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── public/                       # Public web assets
│   ├── afriscribe/              # AfriScribe public assets
│   │   ├── css/                 # AfriScribe stylesheets
│   │   ├── images/              # AfriScribe images
│   │   ├── js/                  # AfriScribe JavaScript
│   │   └── lib/                 # AfriScribe libraries
│   ├── css/                     # Global stylesheets
│   ├── favicon.ico              # Site favicon
│   ├── images/                  # Global images
│   ├── index.php                # Laravel entry point
│   ├── js/                      # Global JavaScript
│   ├── lib/                     # Global libraries
│   └── robots.txt               # SEO robots file
├── resources/                    # Source files
│   ├── css/                     # Source stylesheets
│   ├── js/                      # Source JavaScript
│   ├── lang/                    # Language files
│   └── views/                   # Blade templates
│       ├── afriscribe/          # AfriScribe views
│       │   ├── layouts/         # Layout templates
│       │   │   ├── app.blade.php           # Main application layout
│       │   │   ├── dashboard.blade.php     # Dashboard layout
│       │   │   ├── form.blade.php          # Form layout
│       │   │   └── landing.blade.php      # Landing page layout
│       │   ├── pages/           # Page templates
│       │   │   ├── about.blade.php         # About Us page
│       │   │   ├── dashboard.blade.php     # Dashboard page
│       │   │   ├── manuscripts.blade.php   # Manuscripts page
│       │   │   ├── proofreading.blade.php  # Proofreading page
│       │   │   ├── quote-request.blade.php # Quote request page
│       │   │   └── welcome.blade.php      # Welcome page
│       │   └── partials/        # Reusable partials
│       │       ├── as-cta.blade.php        # Call-to-action partial
│       │       ├── as-features.blade.php   # Features partial
│       │       ├── as-footer.blade.php     # Footer partial
│       │       ├── as-hero.blade.php       # Hero section partial
│       │       ├── as-nav.blade.php        # Navigation partial
│       │       ├── as-proofreading-form.blade.php
│       │       ├── as-services.blade.php   # Services partial
│       │       └── welcome-form.blade.php  # Welcome form partial
│       ├── components/          # Blade components
│       ├── layouts/            # Main layouts
│       └── vendor/             # Vendor views
├── routes/
│   ├── api.php                 # API routes
│   ├── channels.php            # Broadcasting channels
│   ├── console.php             # Console routes
│   ├── quote_requests.php      # Quote request routes
│   ├── user.php                # User routes
│   └── web.php                 # Web routes
├── storage/                    # Storage directories
│   ├── app/                    # Application storage
│   ├── framework/              # Framework storage
│   ├── logs/                   # Log files
│   └── uploaded_pdf_articles/  # Uploaded articles
├── tests/                      # Test files
│   ├── Browser/                # Browser tests
│   ├── CreatesApplication.php  # Test helper
│   ├── DuskTestCase.php        # Dusk test case
│   ├── Feature/                # Feature tests
│   │   ├── QuoteRequestTest.php
│   │   └── UserTest.php
│   ├── TestCase.php            # Base test case
│   └── Unit/                   # Unit tests
├── .editorconfig              # Editor configuration
├── .env.example               # Environment template
├── .gitattributes            # Git attributes
├── .gitignore                # Git ignore rules
├── .htaccess                 # Apache configuration
├── artisan                   # Artisan command line
├── composer.json             # PHP dependencies
├── composer.lock             # Dependency lock file
├── package.json              # Node.js dependencies
├── phpunit.xml              # PHPUnit configuration
├── README.md                 # Project documentation
├── tailwind.config.js        # Tailwind CSS config
├── vite.config.js            # Vite configuration
└── webpack.mix.js            # Laravel Mix configuration
```

## 🔐 Key Models

### Core Models
- **Article**: Main content model with metadata, file handling, and publication workflow
- **Member**: User management with subscriptions, roles, and permissions
- **User**: Base user model with authentication and profile management
- **Role & Permission**: Role-based access control system
- **Subscription**: Subscription tiers and billing management

### Content Management Models
- **ArticleCategory**: Content organization and classification
- **ArticleKeyword**: SEO and discoverability keywords
- **ContentCategory & ContentTag**: Flexible content organization
- **ContentPage**: Static content pages (About, FAQ, etc.)
- **FaqCategory & FaqQuestion**: FAQ management system

### Review & Workflow Models
- **Comment**: Article comments and review feedback
- **EditorAccept**: Editorial acceptance workflow
- **PublisherAccept**: Publisher approval process
- **ReviewerAccept**: Peer review management
- **ReviewerAcceptFinal**: Final review decisions
- **ViewArticle**: Article view tracking and analytics

### AfriScribe Module Models
- **AfriscribeRequest**: Proofreading service requests
- **QuoteRequest**: Quote request management with pricing
- **AfriscribeRequest**: Legacy AfriScribe request model

### Business & Analytics Models
- **Bookmark**: User bookmarking system
- **DownloadArticle**: Article download tracking
- **PurchasedArticle**: Monetization and purchase history
- **MemberSubscription**: Subscription management
- **MemberType**: Member classification system

### System Models
- **About**: About page content management
- **Country & State**: Geographic data
- **EmailVerify**: Email verification system
- **ResetPassword**: Password reset functionality
- **Setting**: Application configuration
- **SubArticle**: Sub-article relationships

## 📧 Email Templates

The application includes comprehensive email templates for:

### Article Management Emails
- **ArticleMail**: General article notifications and updates
- **NewArticle**: New article submission confirmations
- **PublishArticle**: Article publication notifications
- **ForwardedArticle**: Article forwarding to editors/publishers

### Review & Workflow Emails
- **EditorMail**: Editorial assignments and feedback
- **ReviewerMail**: Peer review assignments and notifications
- **PublisherMail**: Publisher notifications and approvals
- **CommentMail**: Review comments and discussions
- **AcceptedMail**: Acceptance confirmations

### AfriScribe Proofreading Emails
- **AfriscribeRequestMail**: Proofreading service requests (legacy)
- **QuoteRequestMail**: Quote request notifications to admin
- **QuoteRequestClientAcknowledgementMail**: Client acknowledgment with CC

### User Management Emails
- **EmailVerification**: Account verification emails
- **ResetPassword**: Password reset functionality
- **ContactUsMail**: Contact form submissions

### System Emails
- **EmailVerify**: Email verification system
- **ResetPassword**: Password reset notifications

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ArticleTest.php

# Run with coverage
php artisan test --coverage
```

### Test Categories

- **QuoteRequestTest**: AfriScribe quote request functionality
- **UserTest**: User authentication and authorization
- **ArticleTest**: Article management functionality
- **MemberTest**: Member and subscription features
- **CommentTest**: Comment and review system
- **FaqTest**: FAQ management
- **AdminTest**: Administrative functions
- **Browser Tests**: End-to-end browser testing with Laravel Dusk

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Style

- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting: `php artisan pint`
- Write tests for new features

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support, please contact the development team or create an issue in the repository.

## 🔄 Updates & Maintenance

### Regular Tasks

- Monitor queue processing
- Check disk space for file uploads
- Review error logs
- Update dependencies regularly

### Performance Optimization

- Cache configuration: `php artisan config:cache`
- Cache routes: `php artisan route:cache`
- Optimize composer autoloader: `composer dump-autoload --optimize`

## 🌟 Features in Development

- [ ] Payment gateway integration (Stripe/Paystack)
- [ ] Advanced search functionality with filters
- [ ] API documentation and developer portal
- [ ] Mobile application (React Native)
- [ ] Multi-language support (French, Portuguese, Arabic)
- [ ] Advanced analytics dashboard
- [ ] Real-time notifications with WebSockets
- [ ] Article versioning system
- [ ] Citation management tools
- [ ] Integration with academic databases (Google Scholar, ORCID)
- [ ] Automated plagiarism checking
- [ ] Conference management module
- [ ] Journal metrics and impact factor tracking
