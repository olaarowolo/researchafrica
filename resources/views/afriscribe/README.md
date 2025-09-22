# AfriScribe Extendable Layout System

This directory contains a modular, extendable layout system for the AfriScribe application. The system is built using Laravel Blade templates and provides different layout types for various page needs.

## 📁 Current Directory Structure

```
resources/views/afriscribe/
├── layouts/                    # Master layout templates
│   ├── app.blade.php          # Base layout with common elements
│   ├── landing.blade.php      # Landing page layout
│   ├── dashboard.blade.php    # Admin dashboard layout
│   └── form.blade.php         # Form-focused layout
├── pages/                     # Example page implementations
│   ├── welcome.blade.php      # Landing page example
│   ├── dashboard.blade.php    # Dashboard page example
│   └── quote-request.blade.php # Form page example
├── partials/                  # Reusable components
│   ├── as-nav.blade.php       # Navigation component
│   ├── as-hero.blade.php      # Hero section component
│   ├── as-services.blade.php   # Services section component
│   ├── as-features.blade.php   # Features section component
│   ├── as-cta.blade.php        # Call-to-action component
│   ├── as-footer.blade.php     # Footer component
│   └── as-pr-form.blade.php    # Proofreading form component
├── afriscribe-proofread-order-form.blade.php  # Order form page
├── afriscribe-proofread-quote-form.blade.php  # Quote form page
├── welcome-form.blade.php     # Welcome form page
├── welcome.blade.php          # Welcome page
└── README.md                  # This file
```

## 🎨 Layout Types

### 1. Base Layout (`layouts/app.blade.php`)
The foundation layout that includes:
- HTML structure and head section
- Common CSS styles
- Navigation component
- Footer component
- JavaScript functionality
- Section yielding for content

### 2. Landing Layout (`layouts/landing.blade.php`)
Perfect for marketing pages and public-facing content:
- Extends the base layout
- Includes hero, services, features, and CTA sections
- Allows custom sections to be yielded
- Ideal for: Home page, product pages, about pages

### 3. Dashboard Layout (`layouts/dashboard.blade.php`)
For admin and internal pages:
- Extends the base layout
- Includes sidebar navigation
- Dashboard header section
- Main content area
- Ideal for: Admin panels, user dashboards, management pages

### 4. Form Layout (`layouts/form.blade.php`)
Specialized for forms and data entry:
- Extends the base layout
- Form-specific styling
- File upload functionality
- Form validation scripts
- Ideal for: Contact forms, quote requests, data entry

## 🧩 Partials (Components)

### Navigation (`partials/as-nav.blade.php`)
- Responsive navigation bar
- Logo and menu items
- Mobile hamburger menu
- Smooth scrolling for anchor links

### Hero Section (`partials/as-hero.blade.php`)
- Hero banner with title and description
- Call-to-action button
- Background image support

### Services (`partials/as-services.blade.php`)
- Grid layout for service cards
- Icons and descriptions
- Hover effects

### Features (`partials/as-features.blade.php`)
- Feature highlights section
- Grid layout with icons
- Dark background styling

### CTA Section (`partials/as-cta.blade.php`)
- Call-to-action section
- Primary button
- Gradient background

### Footer (`partials/as-footer.blade.php`)
- Simple footer with copyright
- Contact information
- Social links (can be added)

### Proofreading Form (`partials/as-pr-form.blade.php`)
- Specialized form for proofreading requests
- File upload functionality
- Service type selection
- Contact information fields

## 🚀 How to Use

### Creating a New Landing Page

```blade
@extends('afriscribe.layouts.landing')

@section('page_title', 'Your Page Title')

@section('page_description', 'Your page description for SEO')

@section('hero')
    @include('afriscribe.partials.as-hero')
@endsection

@section('services_section')
    @include('afriscribe.partials.as-services')
@endsection

@section('custom_sections')
    <!-- Add your custom sections here -->
@endsection

@section('page_scripts')
<script>
    // Page-specific JavaScript
</script>
@endsection
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
