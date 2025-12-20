# Research Africa - Academic Journal Management Platform

Research Africa is a comprehensive Laravel-based platform designed to streamline academic journal management and scholarly publishing in Africa. The platform includes multiple subsystems including the AfriScribe proofreading service, article management, peer review workflows, and member services.

# Research Africa

Research Africa is a Laravel-based platform for managing academic journals, peer review workflows, and scholarly publishing in Africa. It provides tools for journal onboarding, user and role management, article submission, peer review, and editorial decision-making.

## Features

-   Journal onboarding and management
-   Article submission and categorization
-   Multi-stage peer review workflow
-   Role-based access for Admins, Editors, Reviewers, Authors, and Contributors
-   Editorial board and reviewer assignment
-   User registration and profile management
-   Email notifications for workflow events
-   Analytics for submissions and reviews

## Quick Start

1. **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/researchafrica.git
    cd researchafrica
    ```
2. **Install dependencies:**
    ```bash
    composer install
    npm install && npm run build
    ```
3. **Configure environment:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    # Edit .env for your database and mail settings
    ```
4. **Migrate and seed database:**
    ```bash
    php artisan migrate
    php artisan db:seed --class=UserTypesSeeder
    php artisan db:seed --class=JournalUsersSeeder
    ```
5. **Run the application:**
    ```bash
    php artisan serve
    ```

## Documentation

-   See the `docs/` folder for guides on onboarding journals, assigning reviewers, user types, and login/roles.

## Contributing

-   Fork, branch, commit, and open a pull request.
-   Follow PSR-12 and use Laravel Pint for formatting.

## License

MIT

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
MAIL_FROM_ADDRESS=noreply@researchafrica.com
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

## 📚 Documentation

The following documentation is available in the `docs/` folder:

-   [ONBOARDING_NEW_JOURNAL.md](docs/ONBOARDING_NEW_JOURNAL.md): How to onboard new journals on Research Africa
-   [ASSIGNING_PEER_REVIEWERS.md](docs/ASSIGNING_PEER_REVIEWERS.md): How to assign peer reviewers to manuscripts
-   [USER_TYPES.md](docs/USER_TYPES.md): Types of users and roles on the platform
-   [JOURNAL_USER_LOGIN_AND_ROLES.md](docs/JOURNAL_USER_LOGIN_AND_ROLES.md): Creating logins and assigning roles for journal users

Refer to these guides for step-by-step instructions on user management, onboarding, and peer review workflows.

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

-   **Article**: Main content model with metadata, file handling, and publication workflow
-   **Member**: User management with subscriptions, roles, and permissions
-   **User**: Base user model with authentication and profile management
-   **Role & Permission**: Role-based access control system
-   **Subscription**: Subscription tiers and billing management

### Content Management Models

-   **ArticleCategory**: Content organization and classification
-   **ArticleKeyword**: SEO and discoverability keywords
-   **ContentCategory & ContentTag**: Flexible content organization
-   **ContentPage**: Static content pages (About, FAQ, etc.)
-   **FaqCategory & FaqQuestion**: FAQ management system

### Review & Workflow Models

-   **Comment**: Article comments and review feedback
-   **EditorAccept**: Editorial acceptance workflow
-   **PublisherAccept**: Publisher approval process
-   **ReviewerAccept**: Peer review management
-   **ReviewerAcceptFinal**: Final review decisions
-   **ViewArticle**: Article view tracking and analytics

### Business & Analytics Models

-   **Bookmark**: User bookmarking system
-   **DownloadArticle**: Article download tracking
-   **PurchasedArticle**: Monetization and purchase history
-   **MemberSubscription**: Subscription management
-   **MemberType**: Member classification system

### System Models

-   **About**: About page content management
-   **Country & State**: Geographic data
-   **EmailVerify**: Email verification system
-   **ResetPassword**: Password reset functionality
-   **Setting**: Application configuration
-   **SubArticle**: Sub-article relationships

## 📧 Email Templates

The application includes comprehensive email templates for:

### Article Management Emails

-   **ArticleMail**: General article notifications and updates
-   **NewArticle**: New article submission confirmations
-   **PublishArticle**: Article publication notifications
-   **ForwardedArticle**: Article forwarding to editors/publishers

### Review & Workflow Emails

-   **EditorMail**: Editorial assignments and feedback
-   **ReviewerMail**: Peer review assignments and notifications
-   **PublisherMail**: Publisher notifications and approvals
-   **CommentMail**: Review comments and discussions
-   **AcceptedMail**: Acceptance confirmations

### User Management Emails

-   **EmailVerification**: Account verification emails
-   **ResetPassword**: Password reset functionality
-   **ContactUsMail**: Contact form submissions

### System Emails

-   **EmailVerify**: Email verification system
-   **ResetPassword**: Password reset notifications

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

-   **UserTest**: User authentication and authorization
-   **ArticleTest**: Article management functionality
-   **MemberTest**: Member and subscription features
-   **CommentTest**: Comment and review system
-   **FaqTest**: FAQ management
-   **AdminTest**: Administrative functions
-   **Browser Tests**: End-to-end browser testing with Laravel Dusk

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Style

-   Follow PSR-12 coding standards
-   Use Laravel Pint for code formatting: `php artisan pint`
-   Write tests for new features

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support, please contact the development team or create an issue in the repository.

## 🔄 Updates & Maintenance

### Regular Tasks

-   Monitor queue processing
-   Check disk space for file uploads
-   Review error logs
-   Update dependencies regularly

### Performance Optimization

-   Cache configuration: `php artisan config:cache`
-   Cache routes: `php artisan route:cache`
-   Optimize composer autoloader: `composer dump-autoload --optimize`

## 🌟 Features in Development

-   [ ] Payment gateway integration (Stripe/Paystack)
-   [ ] Advanced search functionality with filters
-   [ ] API documentation and developer portal
-   [ ] Mobile application (React Native)
-   [ ] Multi-language support (French, Portuguese, Arabic)
-   [ ] Advanced analytics dashboard
-   [ ] Real-time notifications with WebSockets
-   [ ] Article versioning system
-   [ ] Citation management tools
-   [ ] Integration with academic databases (Google Scholar, ORCID)
-   [ ] Automated plagiarism checking
-   [ ] Conference management module
-   [ ] Journal metrics and impact factor tracking
