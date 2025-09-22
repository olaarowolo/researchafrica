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
- **Bootstrap**: Responsive UI framework
- **JavaScript**: Vanilla JS with jQuery support

### Key Dependencies

- **PDF Generation**: barryvdh/laravel-dompdf
- **Charts**: laraveldaily/laravel-charts
- **Media Management**: spatie/laravel-medialibrary
- **Document Processing**: phpoffice/phpword
- **Authentication**: Laravel Sanctum
- **Data Tables**: yajra/laravel-datatables-oracle

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
│   ├── Exceptions/                 # Exception handlers
│   ├── Http/
│   │   ├── Controllers/           # Main application controllers
│   │   │   ├── AfriscribeController.php    # AfriScribe proofreading service
│   │   │   ├── ArticleController.php        # Article management
│   │   │   ├── MemberController.php         # Member management
│   │   │   └── Admin/              # Administrative controllers
│   │   ├── Livewire/               # Livewire components
│   │   ├── Middleware/             # Route middleware
│   │   └── Requests/               # Form request validation
│   ├── Mail/                       # Email classes
│   │   ├── AfriscribeRequestMail.php       # Proofreading service emails
│   │   ├── ArticleMail.php                 # Article notifications
│   │   ├── CommentMail.php                 # Review comments
│   │   └── PublisherMail.php               # Publication notifications
│   ├── Models/                     # Eloquent models
│   │   ├── Article.php                     # Main article model
│   │   ├── Member.php                      # User management
│   │   ├── ArticleCategory.php             # Content organization
│   │   ├── Comment.php                     # Review system
│   │   └── Afriscribe/            # AfriScribe-specific models
│   ├── Providers/                  # Service providers
│   ├── Services/
│   │   ├── ArticleService.php              # Article business logic
│   │   └── AfriscribeService.php           # Proofreading service logic
│   └── View/Components/            # Reusable view components
├── config/                         # Configuration files
│   ├── afriscribe.php             # AfriScribe service configuration
│   ├── app.php                    # Application settings
│   └── mail.php                   # Email configuration
├── database/
│   ├── factories/                  # Model factories
│   ├── migrations/                 # Database migrations
│   └── seeders/                    # Database seeders
├── public/
│   ├── afriscribe/                # AfriScribe public assets
│   └── css/js/images/             # General assets
├── resources/
│   ├── css/                       # Stylesheets
│   ├── js/                        # JavaScript files
│   ├── lang/                      # Language files
│   └── views/
│       ├── layouts/               # Master layouts
│       ├── afriscribe/            # AfriScribe templates
│       │   ├── welcome.blade.php           # Service landing page
│       │   ├── request.blade.php           # Service request form
│       │   └── pricing.blade.php           # Pricing information
│       ├── articles/              # Article templates
│       └── auth/                  # Authentication views
├── routes/
│   ├── web.php                    # Main routes
│   ├── api.php                    # API routes
│   └── afriscribe.php             # AfriScribe-specific routes
├── storage/
│   ├── app/
│   │   ├── afriscribe_uploads/    # Proofreading service uploads
│   │   └── public/               # Public file storage
│   └── logs/                      # Application logs
├── tests/
│   ├── Feature/
│   │   ├── AfriscribeTest.php     # AfriScribe functionality tests
│   │   ├── ArticleTest.php        # Article management tests
│   │   └── AuthenticationTest.php # Auth system tests
│   └── Unit/                      # Unit tests
└── uploaded_pdf_articles/        # Article file storage
```

## 🔐 Key Models

- **Article**: Main content model with metadata and file handling
- **Member**: User management and subscriptions
- **ArticleCategory**: Content organization
- **Comment**: Review and feedback system
- **EditorAccept/PublisherAccept/ReviewerAccept**: Workflow management
- **PurchasedArticle**: Monetization tracking

## 📧 Email Templates

The application includes email templates for:

- Article submissions and confirmations
- Review assignments and feedback
- Publication notifications
- Proofreading service requests
- User registration and verification

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

- **AuthenticationTest**: User authentication and authorization
- **ArticleTest**: Article management functionality
- **MemberTest**: Member and subscription features
- **CommentTest**: Comment and review system
- **FaqTest**: FAQ management
- **AdminTest**: Administrative functions

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
- [ ] Advanced search functionality
- [ ] API documentation
- [ ] Mobile application
- [ ] Multi-language support
- [ ] Advanced analytics dashboard
