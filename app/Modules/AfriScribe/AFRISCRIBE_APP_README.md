# AfriScribe Backend Module - Academic Proofreading Service

## Overview

The AfriScribe module is a specialized backend component of the Research Africa platform, designed to handle professional academic proofreading services. This module provides a complete workflow for quote requests, document processing, and client communication with location-based pricing.

## 🏗️ Module Structure

```
app/Modules/AfriScribe/
├── Http/
│   ├── Controllers/
│   │   ├── AfriscribeController.php      # Main service controller
│   │   └── QuoteRequestController.php    # Quote request handling
│   ├── Middleware/                       # Module-specific middleware
│   └── routes.php                        # Module routing
└── Mail/
    ├── AfriscribeRequestMail.php         # Legacy email templates
    └── QuoteRequestMail.php              # Quote request notifications
```

## 🎯 Core Functionality

### 1. Quote Request Management
- **Dynamic Pricing**: Location-based pricing (UK/Nigeria)
- **Service Tiers**: Multiple proofreading service levels
- **File Processing**: Support for various document formats
- **Client Communication**: Automated email workflows

### 2. Document Processing
- **File Upload**: Secure document handling
- **Format Support**: PDF, Word, and text documents
- **Size Management**: Configurable file size limits
- **Storage**: Organized file storage system

### 3. Admin Dashboard
- **Request Management**: Complete request lifecycle
- **Status Tracking**: Real-time status updates
- **File Downloads**: Secure file access
- **Analytics**: Request statistics and reporting

## 🛠️ Controllers

### AfriscribeController

#### Public Methods
- `welcome()` - Display service landing page
- `manuscripts()` - Manuscripts service page
- `proofreading()` - Proofreading service details
- `about()` - About Us page information
- `processRequest()` - Handle proofreading requests
- `getRequests()` - Admin request listing
- `updateRequestStatus()` - Update request status

#### Admin Methods
- `getRequests()` - Retrieve all requests for admin
- `updateRequestStatus()` - Modify request status
- `downloadFile()` - Secure file download

### QuoteRequestController

#### Public Methods
- `create()` - Display quote request form
- `store()` - Process and store quote requests
- `getPricingData()` - Dynamic pricing API endpoint

#### Admin Methods
- `index()` - List all quote requests
- `show()` - Display specific quote details
- `updateStatus()` - Update quote status
- `downloadFile()` - Download attached files

## 📧 Email System

### QuoteRequestMail
- **Purpose**: Admin notifications for new quote requests
- **Recipient**: researchafripub@gmail.com
- **Content**: Complete request details and client information
- **Trigger**: New quote request submission

### QuoteRequestClientAcknowledgementMail
- **Purpose**: Client confirmation with CC
- **Recipient**: Client email address
- **CC**: olasunkanmiarowolo@gmail.com
- **Content**: Request acknowledgment and next steps
- **Trigger**: Successful quote request processing

## 🗄️ Database Models

### QuoteRequest Model
```php
class QuoteRequest extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'ra_service', 'product',
        'location', 'service_type', 'word_count', 'estimated_cost',
        'estimated_turnaround', 'addons', 'referral', 'message',
        'file_path', 'original_filename', 'status', 'admin_notes'
    ];

    protected $casts = [
        'addons' => 'array',
        'estimated_cost' => 'decimal:2'
    ];
}
```

### AfriscribeRequest Model (Legacy)
```php
class AfriscribeRequest extends Model
{
    protected $fillable = [
        'client_name', 'client_email', 'document_type',
        'service_type', 'word_count', 'deadline', 'status'
    ];
}
```

## 🛣️ Routing

### Public Routes
```php
Route::get('/afriscribe/welcome', [AfriscribeController::class, 'welcome']);
Route::get('/afriscribe/about', [AfriscribeController::class, 'about']);
Route::get('/afriscribe/manuscripts', [AfriscribeController::class, 'manuscripts']);
Route::get('/afriscribe/proofreading', [AfriscribeController::class, 'proofreading']);
Route::post('/afriscribe/request', [AfriscribeController::class, 'processRequest']);
Route::get('/afriscribe/quote-request', [QuoteRequestController::class, 'create']);
Route::post('/afriscribe/quote-request', [QuoteRequestController::class, 'store']);
Route::get('/afriscribe/pricing-data', [QuoteRequestController::class, 'getPricingData']);
```

### Admin Routes
```php
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    Route::get('/afriscribe/requests', [AfriscribeController::class, 'getRequests']);
    Route::put('/afriscribe/requests/{id}/status', [AfriscribeController::class, 'updateRequestStatus']);
    Route::get('/afriscribe/quote-requests', [QuoteRequestController::class, 'index']);
    Route::get('/afriscribe/quote-requests/{id}', [QuoteRequestController::class, 'show']);
    Route::put('/afriscribe/quote-requests/{id}/status', [QuoteRequestController::class, 'updateStatus']);
    Route::get('/afriscribe/quote-requests/{id}/download', [QuoteRequestController::class, 'downloadFile']);
});
```

## 💰 Pricing System

### Location-Based Pricing

#### UK Pricing
```php
'UK' => [
    'proofreading' => [
        'rate' => 0.02,        // £0.02 per word
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
    ]
]
```

#### Nigeria Pricing
```php
'Nigeria' => [
    'proofreading' => [
        'rate' => 8.00,        // ₦8 per word
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
    ]
]
```

### Pricing Calculation Logic

1. **Base Rate**: Location and service type determine starting rate
2. **Word Count**: Rate multiplied by total word count
3. **Service Multiplier**: Different rates for different service levels
4. **Add-ons**: Additional costs for rush service and plagiarism checks
5. **Rush Fee**: 50% surcharge for expedited processing

## 🔧 Configuration

### Environment Variables
```env
# Email Configuration
AFRISCRIBE_ADMIN_EMAIL=researchafripub@gmail.com
AFRISCRIBE_CC_EMAIL=olasunkanmiarowolo@gmail.com

# File Upload Settings
AFRISCRIBE_MAX_FILE_SIZE=10240  # 10MB in KB
AFRISCRIBE_UPLOAD_PATH=quote-requests
AFRISCRIBE_ALLOWED_TYPES=doc,docx,pdf,txt

# Pricing Configuration
AFRISCRIBE_DEFAULT_LOCATION=UK
AFRISCRIBE_CURRENCY_UK=GBP
AFRISCRIBE_CURRENCY_NG=NGN
```

### File Upload Configuration
```php
// Maximum file size: 10MB
'file' => 'required|file|mimes:doc,docx,pdf,txt|max:10240'

// Storage configuration
$filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-_.]/', '', $originalFilename);
$filePath = $file->storeAs('quote-requests', $filename, 'public');
```

## 🔐 Security Features

### File Upload Security
- **File Type Validation**: Strict MIME type checking
- **Size Limits**: Configurable maximum file sizes
- **Filename Sanitization**: Remove special characters
- **Secure Storage**: Organized directory structure

### Request Validation
- **Input Sanitization**: All form inputs validated
- **CSRF Protection**: Laravel built-in CSRF tokens
- **Rate Limiting**: Middleware for request throttling
- **Authentication**: Admin routes protected

### Email Security
- **Content Validation**: Email content sanitized
- **Recipient Verification**: Valid email format checking
- **Error Handling**: Failed emails logged without exposure

## 📊 Status Management

### Quote Request Statuses
- **pending**: Initial request state
- **in_progress**: Being processed by team
- **completed**: Work finished
- **cancelled**: Request cancelled
- **revision**: Requires client revisions

### Status Update Workflow
1. **Submission** → `pending`
2. **Admin Review** → `in_progress`
3. **Processing** → `completed`
4. **Client Feedback** → `revision` (if needed)
5. **Final Delivery** → `completed`

## 🧪 Testing

### Unit Tests
```php
// Test pricing calculation
public function testPricingCalculation()
{
    $controller = new QuoteRequestController();
    $pricing = $controller->getPricingData();

    $this->assertArrayHasKey('UK', $pricing);
    $this->assertArrayHasKey('Nigeria', $pricing);
}

// Test file upload
public function testFileUpload()
{
    Storage::fake('public');

    $response = $this->post('/afriscribe/quote-request', [
        'file' => UploadedFile::fake()->create('document.pdf', 1000)
    ]);

    $this->assertDatabaseHas('quote_requests', [
        'original_filename' => 'document.pdf'
    ]);
}
```

### Integration Tests
```php
// Test complete quote request flow
public function testQuoteRequestFlow()
{
    $this->post('/afriscribe/quote-request', $requestData)
        ->assertRedirect('/')
        ->assertSessionHas('success', 'Request submitted successfully');

    // Verify email sent
    Mail::assertSent(QuoteRequestMail::class);
    Mail::assertSent(QuoteRequestClientAcknowledgementMail::class);
}
```

## 🚀 Deployment

### Production Checklist
- [ ] Configure production email settings
- [ ] Set up file storage permissions
- [ ] Configure database backups
- [ ] Set up monitoring for failed emails
- [ ] Test file upload functionality
- [ ] Verify pricing calculations
- [ ] Check admin dashboard access

### Performance Optimization
- [ ] Enable query caching for pricing data
- [ ] Optimize file storage with CDN
- [ ] Set up background job processing
- [ ] Configure database indexing
- [ ] Enable compression for assets

## 🔍 Monitoring & Logging

### Log Channels
- **Email Logs**: Track email sending success/failure
- **File Upload Logs**: Monitor file processing
- **Error Logs**: Capture validation and processing errors
- **Admin Logs**: Track status changes and updates

### Key Metrics
- **Request Volume**: Number of quote requests per day
- **Conversion Rate**: Requests to completed projects
- **Response Time**: Average processing time
- **Error Rate**: Failed requests and emails

## 🤝 API Integration

### Pricing API Endpoint
```http
GET /afriscribe/pricing-data
Accept: application/json
```

Response:
```json
{
  "UK": {
    "proofreading": {
      "rate": 0.02,
      "rush_multiplier": 1.5,
      "plagiarism_check": 50.00
    }
  },
  "Nigeria": {
    "proofreading": {
      "rate": 8.00,
      "rush_multiplier": 1.5,
      "plagiarism_check": 20000.00
    }
  }
}
```

### Quote Request API
```http
POST /afriscribe/quote-request
Content-Type: multipart/form-data

{
  "name": "John Doe",
  "email": "john@example.com",
  "location": "UK",
  "service_type": "proofreading",
  "word_count": 1000,
  "file": "document.pdf"
}
```

## 🐛 Troubleshooting

### Common Issues

#### Email Not Sending
- Check mail configuration in `.env`
- Verify SMTP settings
- Check email logs for errors
- Ensure recipient email is valid

#### File Upload Failures
- Verify file size limits
- Check storage permissions
- Ensure upload directory exists
- Validate file types

#### Pricing Calculation Errors
- Check pricing data structure
- Verify location settings
- Test calculation logic
- Review service type mappings

### Debug Commands
```bash
# Check mail logs
tail -f storage/logs/laravel.log | grep mail

# Test file storage
php artisan storage:link
ls -la storage/app/public/

# Verify routes
php artisan route:list | grep afriscribe
```

## 📈 Future Enhancements

### Planned Features
- [ ] Real-time chat integration
- [ ] Payment gateway integration
- [ ] Advanced file collaboration
- [ ] API rate limiting
- [ ] Multi-language support
- [ ] Advanced analytics dashboard
- [ ] Automated quality checks
- [ ] Integration with plagiarism detection services

### Performance Improvements
- [ ] Database query optimization
- [ ] Caching for pricing data
- [ ] Background job processing
- [ ] File compression and optimization
- [ ] CDN integration for assets

## 📞 Support

For technical support or questions about the AfriScribe module:

- **Email**: support@afriscribe.com
- **Documentation**: See main Research Africa README.md
- **Issues**: Create issues in the main repository
- **Development**: Contact the development team

## 🔄 Version History

### v1.0.0 (Current)
- Complete quote request system
- Dynamic pricing calculation
- Email notification system
- Admin dashboard functionality
- File upload and management
- Comprehensive validation
- Security best practices

### v0.9.0 (Previous)
- Basic quote request form
- Simple pricing structure
- Email notifications
- File upload functionality

---

*This module is designed to be scalable, secure, and maintainable for the AfriScribe proofreading service within the Research Africa platform.*
