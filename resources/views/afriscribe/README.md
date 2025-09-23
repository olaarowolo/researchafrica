# AfriScribe Extendable Layout System

This directory contains the Blade views for the AfriScribe module. It includes a **proposed** modular, extendable layout system (`layouts/`, `partials/`) designed for consistency and maintainability.

> **Important:** While this README describes a modular system, some key pages like `welcome-form.blade.php` are currently implemented as large, standalone files with inline CSS and JavaScript. They do not yet adhere to the layout system. The `partials/` are used in some places, but not universally. This documentation serves as both a guide to the existing structure and a blueprint for future refactoring.

## 📁 Current Directory Structure

```text
resources/views/afriscribe/
├── layouts/                    # Master layout templates (Proposed)
│   ├── app.blade.php          # Base layout with common elements
│   ├── landing.blade.php      # Landing page layout
│   ├── dashboard.blade.php    # Admin dashboard layout
│   └── form.blade.php         # Form-focused layout
├── pages/                     # Example page implementations (Proposed)
│   ├── welcome.blade.php      # Landing page example
│   ├── dashboard.blade.php    # Dashboard page example
│   └── quote-request.blade.php # Form page example
├── partials/                  # Reusable components (Partially implemented)
│   ├── as-nav.blade.php       # General: Navigation component
│   ├── as-footer.blade.php     # General: Footer component
│   ├── as-hero.blade.php      # General: Hero section for main landing page
│   ├── as-services.blade.php   # General: Services grid for main landing page
│   ├── as-features.blade.php   # General: Features grid for main landing page
│   ├── as-cta.blade.php        # General: Call-to-action for main landing page
│   │
│   ├── as-pr-form.blade.php    # Proofreading: The dynamic quote/order form component
│   ├── as-hero-proofreading.blade.php   # Proofreading: Page-specific hero section
│   ├── as-proofreading-overview.blade.php # Proofreading: Page-specific overview section
│   ├── as-proofreading-pricing.blade.php  # Proofreading: Page-specific pricing section
│   ├── as-proofreading-cta.blade.php      # Proofreading: Page-specific CTA section
│   ├── as-proofreading-form.blade.php     # Proofreading: Page-specific simple interest form
│   │
│   ├── as-hero-manuscripts.blade.php    # Manuscripts: Page-specific hero section
│   ├── as-manuscripts-overview.blade.php # Manuscripts: Page-specific overview section
│   ├── as-manuscripts-features.blade.php # Manuscripts: Page-specific features section
│   ├── as-manuscripts-pricing.blade.php  # Manuscripts: Page-specific pricing section
│   ├── as-manuscripts-cta.blade.php      # Manuscripts: Page-specific CTA section
│   └── as-manuscripts-form.blade.php     # Manuscripts: Page-specific simple interest form
├── welcome-form.blade.php     # Main landing page with integrated quote form (monolithic)
├── afriscribe-proofread-order-form.blade.php  # Standalone page for the dynamic order form
├── afriscribe-proofread-quote-form.blade.php  # Legacy/simple quote form page
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

This section details the reusable Blade components. They are grouped into "General", "Proofreading", and "Manuscripts" categories.

### General Partials

These are used across multiple pages.

- **`partials/as-nav.blade.php`**: Responsive navigation bar with logo, menu items, and mobile hamburger menu.
- **`partials/as-footer.blade.php`**: Simple footer with copyright information.
- **`partials/as-hero.blade.php`**: Main hero banner with title, description, and a primary call-to-action button.
- **`partials/as-services.blade.php`**: Grid layout for displaying service cards with icons and descriptions.
- **`partials/as-features.blade.php`**: A grid for highlighting key features, typically on a dark background.
- **`partials/as-cta.blade.php`**: A prominent call-to-action section with a gradient background.

### Proofreading Partials

These are specific to the "AfriScribe Proofread" product pages.

- **`partials/as-pr-form.blade.php`**: The core dynamic proofreading order form. Includes location-based pricing, service selection, word count input, file uploads, and real-time cost calculation.
- **`partials/as-hero-proofreading.blade.php`**: A hero section tailored for the proofreading service page.
- **`partials/as-proofreading-overview.blade.php`**: An overview section explaining the proofreading service.
- **`partials/as-proofreading-pricing.blade.php`**: A section detailing the pricing tiers for proofreading.
- **`partials/as-proofreading-cta.blade.php`**: A call-to-action specific to starting a proofreading request.
- **`partials/as-proofreading-form.blade.php`**: A simple interest/contact form for the proofreading service.

### Manuscripts Partials

These are specific to the "AfriScribe Manuscripts" product pages.

- **`partials/as-hero-manuscripts.blade.php`**: A hero section tailored for the manuscripts service page.
- **`partials/as-manuscripts-overview.blade.php`**: An overview section explaining the manuscripts platform.
- **`partials/as-manuscripts-features.blade.php`**: A section highlighting the features of the manuscripts platform.
- **`partials/as-manuscripts-pricing.blade.php`**: A section detailing the pricing for the manuscripts service.
- **`partials/as-manuscripts-cta.blade.php`**: A call-to-action specific to the manuscripts service.
- **`partials/as-manuscripts-form.blade.php`**: A simple interest/contact form for the manuscripts service.

## 🏗️ Recommended Structure & Workflow

This project uses a modular structure to promote code reuse and maintainability. The goal is to move away from monolithic files (like `welcome-form.blade.php`) and towards a component-based architecture.

### 1. **Layouts (`layouts/`)**
- **Purpose**: These are the master templates. A layout defines the main HTML structure (like `<html>`, `<head>`, `<body>`, header, footer) and yields sections for content.
- **Usage**: Every page should `@extends` a layout. Choose the one that best fits the page's purpose (e.g., `landing.blade.php` for a marketing page, `dashboard.blade.php` for an admin page).

### 2. **Partials (`partials/`)**
- **Purpose**: These are small, reusable pieces of UI. A partial could be a navigation bar, a footer, a specific card, or a form section. They should not contain any page-specific logic.
- **Usage**: Partials are included within layouts or pages using `@include()`. They are the building blocks of your pages.

### 3. **Pages (`pages/`)**
- **Purpose**: These are the final views that a user sees. A page's primary job is to extend a layout and compose various partials to build the final UI.
- **Usage**: Create a new file in this directory for each new page (e.g., `about-us.blade.php`). This file will contain `@section` directives to inject content into the chosen layout.

### Workflow for Creating a New Page
1.  **Define the Page**: Determine the page's purpose and what layout it should use (e.g., a new "Contact Us" page would use `layouts/landing.blade.php`).
2.  **Create the Page File**: Create a new file in the `pages/` directory (e.g., `pages/contact.blade.php`).
3.  **Extend the Layout**: In the new file, start by extending the chosen layout: `@extends('afriscribe.layouts.landing')`.
4.  **Identify Reusable Components**: Look at the design and identify sections that already exist as partials (e.g., hero, CTA).
5.  **Create New Partials (if needed)**: If a section is unique but might be reused later, create a new file in `partials/`.
6.  **Compose the Page**: Use `@section` and `@include` to assemble the partials and add unique content to the page.
7.  **Add Route**: Finally, add a route in your routes file to point to your new page view.

This structure makes the codebase cleaner, easier to navigate, and faster to develop with over time.

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
