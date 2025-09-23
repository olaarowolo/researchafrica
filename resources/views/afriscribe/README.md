# AfriScribe - Academic Proofreading Service Module

AfriScribe is a specialized proofreading service module within the Research Africa platform, designed to provide professional academic document proofreading services with location-based pricing and comprehensive workflow management.

## 🌍 About AfriScribe

AfriScribe offers professional proofreading services for academic documents, research papers, theses, and dissertations. The service features:

- **Location-based pricing** (UK vs Nigeria rates)
- **Dynamic cost calculation** based on word count and service type
- **Professional proofreaders** with academic expertise
- **File processing** for multiple document formats
- **Automated client communication** and project tracking

## 🏗️ Module Architecture

### Backend Structure

```
app/Modules/AfriScribe/
├── AFRISCRIBE_APP_README.md
├── composer.json
├── Config/
│   └── afriscribe.php
├── Http/
│   ├── Controllers/
│   │   ├── AfriscribeController.php
│   │   └── QuoteRequestController.php
│   └── routes.php
├── Mail/
│   ├── AfriscribeClientAcknowledgementMail.php
│   ├── AfriscribeRequestMail.php
│   ├── QuoteRequestClientAcknowledgementMail.php
│   └── QuoteRequestMail.php
├── Models/
│   ├── AfriscribeRequest.php
│   └── QuoteRequest.php
└── Providers/
    └── AfriScribeServiceProvider.php
```

### Frontend Structure

```
resources/views/afriscribe/
├── afriscribe-proofread-order-form.blade.php
├── afriscribe-proofread-quote-form.blade.php
├── manuscripts.blade.php
├── old-welcome.blade.php
├── README.md
├── success.blade.php
├── welcome-form.blade.php
├── image/
│   └── README/
├── layouts/
│   ├── app.blade.php
│   ├── dashboard.blade.php
│   ├── form.blade.php
│   └── landing.blade.php
├── pages/
│   ├── about.blade.php
│   ├── dashboard.blade.php
│   ├── manuscripts.blade.php
│   ├── proofreading.blade.php
│   ├── quote-request.blade.php
│   └── welcome.blade.php
└── partials/
    ├── as-cta.blade.php
    ├── as-features.blade.php
    ├── as-footer.blade.php
    ├── as-hero-manuscripts.blade.php
    ├── as-hero-proofreading.blade.php
    ├── as-hero.blade.php
    ├── as-manuscripts-cta.blade.php
    ├── as-manuscripts-features.blade.php
    ├── as-manuscripts-form.blade.php
    ├── as-manuscripts-overview.blade.php
    ├── as-manuscripts-pricing.blade.php
    ├── as-nav.blade.php
    ├── as-pr-form.blade.php
    ├── as-proofreading-cta.blade.php
    ├── as-proofreading-form.blade.php
    ├── as-proofreading-overview.blade.php
    ├── as-proofreading-pricing.blade.php
    └── as-services.blade.php
```

### Public Assets

```
public/afriscribe/
├── files/
│   ├── AfriScribe-NG-RateCard.pdf
│   └── AfriScribe-UK-RateCard.pdf
└── img/
    ├── afriscribe_proofread_apple-touch-icon.png
    ├── afriscribe_proofread_favicon-landscape.png
    ├── afriscribe_proofread-favicon-96x96.png
    ├── afriscribe_proofread-favicon.ico
    ├── afriscribe_proofread-favicon.png
    ├── afriscribe_proofread-favicon.svg
    ├── afriscribe_proofread-logo-black.png
    ├── afriscribe_proofread-logo-white.png
    ├── afriscribe_proofread-web-app-manifest-192x192.png
    ├── afriscribe_proofread-web-app-manifest-512x512.png
    ├── afriscribe-logo-main-logo-black.png
    ├── afriscribe-logo-main-logo-white.png
    └── site.webmanifest
```


## 🎯 Key Features

### Proofreading Services
- **Document Analysis**: Automatic word count and complexity assessment
- **Pricing Calculator**: Real-time pricing based on location and service type
- **File Upload**: Support for PDF, Word, and other document formats
- **Quality Assurance**: Multi-tier review process
- **Client Communication**: Automated email notifications and updates

### Quote Request System
- **Dynamic Pricing**: Location-based pricing (UK/Nigeria rates)
- **Service Tiers**: Different levels of proofreading service
- **Instant Quotes**: Real-time cost calculation
- **Request Management**: Complete workflow from quote to completion

### User Experience
- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Interactive Forms**: Dynamic form validation and feedback
- **Professional UI**: Clean, academic-focused design
- **Accessibility**: WCAG compliant interface

## 🛠️ Technical Implementation

### Controllers

#### AfriscribeController
- **welcome()**: Landing page display
- **manuscripts()**: Manuscripts service page
- **proofreading()**: Proofreading service page
- **about()**: About Us page
- **processRequest()**: Handle proofreading requests
- **getRequests()**: Admin request management
- **updateRequestStatus()**: Status update functionality

#### QuoteRequestController
- **create()**: Quote request form
- **store()**: Process quote requests
- **index()**: Admin quote listing
- **show()**: Quote details view
- **updateStatus()**: Status management
- **getPricingData()**: Dynamic pricing API
- **downloadFile()**: File download handling

### Routes

```php
// Public routes
Route::get('/afriscribe/welcome', [AfriscribeController::class, 'welcome']);
Route::get('/afriscribe/about', [AfriscribeController::class, 'about']);
Route::get('/afriscribe/manuscripts', [AfriscribeController::class, 'manuscripts']);
Route::get('/afriscribe/proofreading', [AfriscribeController::class, 'proofreading']);
Route::post('/afriscribe/request', [AfriscribeController::class, 'processRequest']);
Route::get('/afriscribe/quote-request', [QuoteRequestController::class, 'create']);
Route::post('/afriscribe/quote-request', [QuoteRequestController::class, 'store']);
Route::get('/afriscribe/pricing-data', [QuoteRequestController::class, 'getPricingData']);

// Admin routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    Route::get('/afriscribe/requests', [AfriscribeController::class, 'getRequests']);
    Route::put('/afriscribe/requests/{id}/status', [AfriscribeController::class, 'updateRequestStatus']);
    Route::get('/afriscribe/quote-requests', [QuoteRequestController::class, 'index']);
    Route::get('/afriscribe/quote-requests/{id}', [QuoteRequestController::class, 'show']);
    Route::put('/afriscribe/quote-requests/{id}/status', [QuoteRequestController::class, 'updateStatus']);
    Route::get('/afriscribe/quote-requests/{id}/download', [QuoteRequestController::class, 'downloadFile']);
});
```

### Email System

#### QuoteRequestMail
- **Purpose**: Admin notifications for new quote requests
- **Recipient**: researchafripub@gmail.com
- **Content**: Request details and client information

#### QuoteRequestClientAcknowledgementMail
- **Purpose**: Client acknowledgment with CC
- **Recipient**: Client email + CC to olasunkanmiarowolo@gmail.com
- **Content**: Confirmation of request receipt and next steps

### Models

#### QuoteRequest
- **Purpose**: Quote request management
- **Key Fields**:
  - Client information (name, email, phone)
  - Document details (type, word count, deadline)
  - Pricing information (location, service type, total cost)
  - Status tracking (pending, in-progress, completed)

#### AfriscribeRequest
- **Purpose**: Legacy proofreading requests
- **Key Fields**:
  - Client details and document information
  - Processing status and timestamps

## 🎨 Frontend Components

### Layouts
- **app.blade.php**: Main layout with navigation and footer
- **landing.blade.php**: Landing page specific layout
- **form.blade.php**: Form-focused layout
- **dashboard.blade.php**: Admin dashboard layout

### Partials
```

### Creating a Dashboard Page

```blade
@extends('afriscribe.layouts.dashboard')

@section('page_title', 'Dashboard - Your Section')

@section('dashboard_title', 'Your Dashboard Title')

@section('dashboard_subtitle', 'Your dashboard description')

@section('dashboard_content')
    <!-- Your dashboard content here -->
@endsection
```

### Creating a Form Page

```blade
@extends('afriscribe.layouts.form')

@section('page_title', 'Contact Us')

@section('form_header')
    <h1>Contact Us</h1>
    <p>Get in touch with our team</p>
@endsection

@section('form_content')
    <!-- Your form content here -->
@endsection
```

## 🎯 Key Features

### 1. **Modular Design**
- Each section is a separate partial
- Easy to maintain and update
- Consistent styling across all pages

### 2. **Responsive Layout**
- Mobile-first design approach
- Responsive navigation and components
- Optimized for all screen sizes

### 3. **SEO Friendly**
- Meta tags support
- Title and description yielding
- Semantic HTML structure

### 4. **Extensible**
- Easy to add new layouts
- Custom sections can be yielded
- Additional styles and scripts support

### 5. **Interactive Elements**
- Form validation
- File upload with drag & drop
- Smooth scrolling navigation
- Hover effects and animations

## 📱 Responsive Breakpoints

- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

## 🎨 Color Scheme

- **Primary**: #0c1e35 (Dark Blue)
- **Secondary**: #f9b233 (Yellow/Orange)
- **Accent**: #e6a029 (Darker Yellow)
- **Background**: #f8f9fb (Light Gray)
- **Text**: #333 (Dark Gray)

## 🔧 Customization

### Adding New Partials
1. Create a new file in `partials/` directory
2. Include it in your layout using `@include()`
3. Add corresponding styles to the base layout

### Creating New Layouts
1. Create a new file in `layouts/` directory
2. Extend the base layout: `@extends('afriscribe.layouts.app')`
3. Define your sections and content areas
4. Add layout-specific styles

### Adding Routes
Make sure to add corresponding routes in your Laravel routes file:

```php
// Example routes
Route::get('/afriscribe', [AfriscribeController::class, 'welcome'])->name('afriscribe.welcome');
Route::get('/afriscribe/dashboard', [AfriscribeController::class, 'dashboard'])->name('afriscribe.dashboard');
Route::get('/afriscribe/quote', [AfriscribeController::class, 'quote'])->name('afriscribe.quote');
```

## 📋 Best Practices

1. **Keep partials focused**: Each partial should have a single responsibility
2. **Use semantic HTML**: Maintain proper heading hierarchy and structure
3. **Optimize images**: Use appropriate image formats and sizes
4. **Test responsiveness**: Ensure all layouts work on mobile devices
5. **Follow naming conventions**: Use consistent naming for partials and sections
6. **Document custom sections**: Add comments for complex custom implementations

## 🔄 Migration Guide

To migrate existing pages to use this layout system:

1. **Identify the page type**: Determine if it's a landing, dashboard, or form page
2. **Choose the appropriate layout**: Select the base layout that matches your needs
3. **Break down into sections**: Identify which parts can be converted to partials
4. **Create the new page**: Use the appropriate layout and include necessary partials
5. **Test thoroughly**: Ensure all functionality works as expected

## 🐛 Troubleshooting

### Common Issues:

1. **Styles not loading**: Check that the base layout CSS is being included
2. **Partials not found**: Verify file paths in `@include()` statements
3. **JavaScript not working**: Ensure scripts are in the correct `@section`
4. **Responsive issues**: Check media queries in the base layout

### Debug Tips:

- Use browser developer tools to inspect element hierarchy
- Check the source code to ensure sections are being yielded correctly
- Verify that all partial files exist and have correct syntax
- Test on different screen sizes to identify responsive issues

## 📞 Support

For questions or issues with the layout system, please refer to:
- Laravel Blade documentation
- Bootstrap CSS framework (if used)
- The main AfriScribe application documentation

---

*This layout system is designed to be flexible, maintainable, and scalable for the AfriScribe application.*
