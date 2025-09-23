# AfriScribe Module - Academic Proofreading Service

## 📚 Overview

The AfriScribe module is a comprehensive academic proofreading service platform designed specifically for African researchers and scholars. It provides professional proofreading, editing, and formatting services with location-based pricing for UK and Nigeria clients.

### 🎯 Purpose

AfriScribe bridges the gap between academic research and professional publishing by offering:
- **Professional Proofreading**: Expert academic proofreading services
- **Location-Based Pricing**: Competitive rates for UK and Nigerian clients
- **Streamlined Workflow**: Easy submission and tracking system
- **Quality Assurance**: Multi-tier review process
- **Client Communication**: Automated email notifications and updates

## ✨ Features

### 🔧 Core Functionality
- **Dynamic Pricing Calculator**: Real-time cost estimation based on location, service type, and word count
- **File Upload System**: Support for multiple document formats (PDF, Word, Text)
- **Service Type Management**: Proofreading, Editing, and Formatting services
- **Client Portal**: User-friendly interface for service requests
- **Admin Dashboard**: Complete request management and tracking system

### 💰 Pricing Structure

The pricing is implemented directly in the frontend and may differ from the backend configuration. The user-facing rates are as follows:

#### United Kingdom (GBP)
- **Student-Friendly Proofreading**: £15 per 1,000 words
- **Research Editing**: £25 per 1,000 words
- **Publication-Ready Academic Edit**: £40 per 1,000 words
- **Packages**: Flat fees available (e.g., Basic Scholar Package: £120)
- **Rush Service**: £150 flat fee
- **Plagiarism Check**: Custom quote

#### Nigeria (NGN)
- **Student-Friendly Proofreading**: ₦2,000 per 1,000 words
- **Research Editing**: ₦3,500 per 1,000 words
- **Publication-Ready Academic Edit**: ₦5,000 per 1,000 words
- **Packages**: Flat fees available (e.g., Basic Scholar Package: ₦20,000)
- **Rush Service**: ₦10,000 flat fee
- **Plagiarism Check**: ₦5,000 flat fee

### 📧 Communication System
- **Client Acknowledgment**: Automatic confirmation emails
- **Admin Notifications**: Instant alerts for new requests
- **Status Updates**: Real-time communication throughout the process
- **File Attachments**: Secure document handling

## 🚀 Installation & Setup

### Prerequisites
- Laravel 10.x application
- PHP 8.1 or higher
- Composer
- MySQL database
- SMTP server configuration

### 1. Module Installation

```bash
# Copy the AfriScribe module to your Laravel app
cp -r app/Modules/AfriScribe /path/to/your/laravel/app/app/Modules/

# Install dependencies
composer require research-africa/afriscribe-module

# Or add to composer.json manually
"require": {
    "research-africa/afriscribe-module": "dev-main"
}
```

### 2. Database Setup

```bash
# Run migrations
php artisan migrate

# The module includes:
# - afriscribe_requests table
# - quote_requests table
```

### 3. Storage Configuration

```bash
# Create storage directories
mkdir -p storage/app/afriscribe_uploads
mkdir -p storage/app/quote-requests

# Create symbolic link
php artisan storage:link
```

### 4. Email Configuration

Configure your mail settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@afriscribe.com
MAIL_FROM_NAME="AfriScribe"

# AfriScribe specific settings
AFRISCRIBE_ADMIN_EMAIL=researchfripub@gmail.com
AFRISCRIBE_MAX_UPLOAD_SIZE=10240
AFRISCRIBE_UPLOAD_DISK=public
```

### 5. Module Registration

The module auto-registers via the `AfriScribeServiceProvider`. Ensure it's loaded in your application.

## ⚙️ Configuration

### Publishing Configuration

```bash
# Publish AfriScribe configuration
php artisan vendor:publish --tag=afriscribe-config

# Publish assets
php artisan vendor:publish --tag=afriscribe-assets
```

### Configuration File (`config/afriscribe.php`)

```php
return [
    'admin_email' => env('AFRISCRIBE_ADMIN_EMAIL', 'researchfripub@gmail.com'),

    'upload' => [
        'max_size' => env('AFRISCRIBE_MAX_UPLOAD_SIZE', 10240), // 10MB
        'allowed_extensions' => ['pdf', 'doc', 'docx', 'txt'],
        'disk' => env('AFRISCRIBE_UPLOAD_DISK', 'public'),
    ],

    'pricing' => [
        'UK' => [
            'proofreading' => [
                'rate' => 0.02, // £0.02 per word
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 50.00
            ],
            'copy_editing' => [
                'rate' => 0.03,
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 50.00
            ],
            'substantive_editing' => [
                'rate' => 0.05,
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 50.00
            ],
        ],
        'Nigeria' => [
            'proofreading' => [
                'rate' => 8.00, // ₦8 per word
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 20000.00
            ],
            'copy_editing' => [
                'rate' => 12.00,
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 20000.00
            ],
            'substantive_editing' => [
                'rate' => 20.00,
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 20000.00
            ],
        ]
    ],

    'services' => [
        'proofreading' => 'Proofreading',
        'editing' => 'Editing',
        'formatting' => 'Formatting',
    ],

    'features' => [
        'file_upload' => true,
        'dynamic_pricing' => true,
        'email_notifications' => true,
        'admin_panel' => true,
    ],

    'notifications' => [
        'admin' => [
            'enabled' => true,
            'email' => env('AFRISCRIBE_ADMIN_EMAIL', 'researchfripub@gmail.com'),
        ],
        'client' => [
            'enabled' => true,
            'acknowledgment' => true,
        ],
    ],
];
```

## 📋 Usage

### Client Interface

#### 1. Landing Page
- **Route**: `/afriscribe`
- **Features**: Service overview, pricing information, rate card downloads
- **Template**: `resources/views/afriscribe/welcome.blade.php`

#### 2. Quote Request Form
- **Route**: `/afriscribe/quote-request`
- **Features**:
  - Dynamic pricing calculator
  - Location-based service selection
  - File upload capability
  - Real-time cost preview
- **Template**: `resources/views/afriscribe/afriscribe-proofread-order-form.blade.php`

#### 3. Request Submission
- **Route**: `/afriscribe/quote-request` (POST)
- **Process**:
  1. Form validation
  2. File upload and storage
  3. Database record creation
  4. Email notifications (admin + client)
  5. Success response

### Admin Interface

#### 1. Request Management
- **Route**: `/admin/afriscribe/quote-requests`
- **Features**:
  - View all quote requests
  - Filter by status (pending, quoted, accepted, etc.)
  - Download attached files
  - Update request status

#### 2. Status Management
- **Route**: `/admin/afriscribe/quote-requests/{id}/status` (PUT)
- **Statuses**:
  - `pending`: Initial request state
  - `quoted`: Quote provided to client
  - `accepted`: Client accepted quote
  - `rejected`: Request declined
  - `completed`: Work finished

## 🔌 API Endpoints

### Public Endpoints

```php
GET    /afriscribe/home                    // Landing page
GET    /afriscribe/quote-request      // Quote request form
POST   /afriscribe/quote-request      // Submit quote request
GET    /afriscribe/pricing-data       // Get pricing information
```

### Admin Endpoints (Protected)

```php
GET    /admin/afriscribe/requests                    // List all requests
PUT    /admin/afriscribe/requests/{id}/status        // Update request status
GET    /admin/afriscribe/quote-requests              // List quote requests
GET    /admin/afriscribe/quote-requests/{id}         // View specific request
PUT    /admin/afriscribe/quote-requests/{id}/status  // Update quote status
GET    /admin/afriscribe/quote-requests/{id}/download // Download attached file
```

## 📧 Email System

### Email Templates

1. **Admin Notification** (`emails.quote_request`)
   - Sent to admin when new request is submitted
   - Includes client details and attached file

2. **Client Acknowledgment** (`emails.quote_request_client_acknowledgement`)
   - Sent to client confirming receipt
   - Includes request details and next steps

### Email Configuration

```php
// In QuoteRequestController
Mail::to('researchfripub@gmail.com')->send(new QuoteRequestMail($quoteRequest));
Mail::to($quoteRequest->email)->send(new QuoteRequestClientAcknowledgementMail($quoteRequest));
```

## 📁 File Structure

```
app/Modules/AfriScribe/
├── Config/
│   └── afriscribe.php              # Module configuration
├── Http/
│   ├── Controllers/
│   │   ├── AfriscribeController.php        # Main controller
│   │   └── QuoteRequestController.php      # Quote management
│   └── routes.php                  # Module routes
├── Mail/
│   ├── AfriscribeRequestMail.php           # Admin notification
│   ├── AfriscribeClientAcknowledgementMail.php # Client confirmation
│   ├── QuoteRequestMail.php                # Quote request email
│   └── QuoteRequestClientAcknowledgementMail.php # Quote acknowledgment
├── Models/
│   ├── AfriscribeRequest.php               # Basic request model
│   └── QuoteRequest.php                    # Advanced quote model
├── Providers/
│   └── AfriScribeServiceProvider.php       # Service provider
└── composer.json                   # Module package definition
```

## 🎨 Frontend Integration

### JavaScript Integration

```javascript
// Dynamic pricing calculation
function calculateCost(location, serviceType, wordCount) {
    const pricing = {
        'UK': {
            'proofreading': 0.02,
            'copy_editing': 0.03,
            'substantive_editing': 0.05
        },
        'Nigeria': {
            'proofreading': 8,
            'copy_editing': 12,
            'substantive_editing': 20
        }
    };

    const rate = pricing[location][serviceType];
    return Math.ceil(wordCount / 1000) * rate * 1000;
}
```

### Form Handling

```html
<!-- Location-based service selection -->
<select id="location" onchange="updateServices()">
    <option value="UK">United Kingdom</option>
    <option value="Nigeria">Nigeria</option>
</select>

<!-- Dynamic cost preview -->
<div id="cost-preview" class="alert alert-info">
    Estimated Cost: £<span id="estimated-cost">0.00</span>
</div>
```

## 🔐 Security Features

- **File Upload Validation**: Type and size restrictions
- **Input Sanitization**: All form inputs validated and sanitized
- **CSRF Protection**: Laravel's built-in CSRF protection
- **Rate Limiting**: Request throttling for API endpoints
- **Secure File Storage**: Files stored outside web root

## 📊 Database Schema

### Afriscribe Requests Table
```sql
CREATE TABLE afriscribe_requests (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    service_type VARCHAR(50),
    message TEXT,
    file_path VARCHAR(255),
    original_filename VARCHAR(255),
    status VARCHAR(20) DEFAULT 'pending',
    admin_notes TEXT,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Quote Requests Table
```sql
CREATE TABLE quote_requests (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    ra_service VARCHAR(255),
    product VARCHAR(255),
    location VARCHAR(50),
    service_type VARCHAR(50),
    word_count INT,
    addons JSON,
    referral VARCHAR(255),
    message TEXT,
    original_filename VARCHAR(255),
    file_path VARCHAR(255),
    status VARCHAR(20) DEFAULT 'pending',
    estimated_cost DECIMAL(10,2),
    estimated_turnaround VARCHAR(255),
    admin_notes TEXT,
    quoted_at TIMESTAMP NULL,
    accepted_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🧪 Testing

### Running Tests
```bash
# Run AfriScribe specific tests
php artisan test --filter=AfriScribe

# Run with coverage
php artisan test --coverage --filter=AfriScribe
```

### Test Coverage
- Controller functionality
- Model relationships
- Email sending
- File upload handling
- Form validation
- API endpoints

## 🚀 Deployment

### Production Checklist
- [ ] Configure production mail settings
- [ ] Set up file storage permissions
- [ ] Configure admin email address
- [ ] Set up monitoring for failed emails
- [ ] Configure backup for uploaded files
- [ ] Set up SSL certificate
- [ ] Configure rate limiting
- [ ] Set up error logging

### Environment Variables
```env
# Production environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Mail configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls

# AfriScribe specific
AFRISCRIBE_ADMIN_EMAIL=admin@yourdomain.com
LOG_LEVEL=error
```

## 🔧 Troubleshooting

### Common Issues

1. **File Upload Fails**
   - Check storage permissions
   - Verify disk configuration
   - Check file size limits

2. **Emails Not Sending**
   - Verify SMTP configuration
   - Check mail driver settings
   - Review error logs

3. **Pricing Not Calculating**
   - Check JavaScript console for errors
   - Verify pricing configuration
   - Ensure location selection is working

4. **Admin Panel Access Issues**
   - Verify user permissions
   - Check route middleware
   - Confirm authentication

### Debug Mode
Enable debug mode temporarily:
```php
// In AfriscribeController
\Log::info('Debug: ', $request->all());
```

## 📈 Future Enhancements

### Planned Features
- [ ] Payment gateway integration (Stripe/PayStack)
- [ ] Multi-currency support
- [ ] Advanced analytics dashboard
- [ ] API documentation
- [ ] Mobile application
- [ ] Multi-language support
- [ ] Advanced search functionality
- [ ] Client portal with request history
- [ ] Automated follow-up system
- [ ] Integration with academic databases

### Performance Optimizations
- [ ] File caching for pricing data
- [ ] Queue system for email sending
- [ ] Database query optimization
- [ ] CDN integration for assets
- [ ] Image optimization for rate cards

## 📞 Support

For technical support or questions:
- **Email**: researchfripub@gmail.com
- **Documentation**: [Link to documentation]
- **Issues**: [GitHub Issues](https://github.com/your-repo/issues)

## 📄 License

This module is part of the Research Africa platform and is licensed under the MIT License.

---

**AfriScribe** - Elevating the Quality of Scholarly Writing in Africa

*Built with ❤️ for the African research community*
