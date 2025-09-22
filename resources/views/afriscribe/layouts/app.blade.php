<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta Tags -->
    <title>@yield('title', 'AfriScribe | Research Africa')</title>
    <meta name="title" content="@yield('meta_title', 'AfriScribe | Research Africa')">
    <meta name="description" content="@yield('meta_description', 'Integrated solutions for manuscripts, proofreading, analytics, and beyond. Streamline your academic publishing workflow with AfriScribe.')">
    <meta name="keywords" content="@yield('meta_keywords', 'academic publishing, manuscripts, proofreading, peer review, Africa, research, scholarly articles, journal publishing')">
    <meta name="author" content="Research Africa">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">

    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical_url', request()->url())">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', request()->url())">
    <meta property="og:title" content="@yield('og_title', 'AfriScribe | Research Africa')">
    <meta property="og:description" content="@yield('og_description', 'Integrated solutions for manuscripts, proofreading, analytics, and beyond. Streamline your academic publishing workflow with AfriScribe.')">
    <meta property="og:image" content="@yield('og_image', asset('afriscribe/img/afriscribe-logo.png'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="AfriScribe">
    <meta property="og:locale" content="en_US">

    <!-- Twitter -->
    <meta property="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta property="twitter:url" content="@yield('twitter_url', request()->url())">
    <meta property="twitter:title" content="@yield('twitter_title', 'AfriScribe | Research Africa')">
    <meta property="twitter:description" content="@yield('twitter_description', 'Integrated solutions for manuscripts, proofreading, analytics, and beyond. Streamline your academic publishing workflow with AfriScribe.')">
    <meta property="twitter:image" content="@yield('twitter_image', asset('afriscribe/img/afriscribe-logo.png'))">
    <meta property="twitter:site" content="@yield('twitter_site', '@afriscribe')">
    <meta property="twitter:creator" content="@yield('twitter_creator', '@afriscribe')">

    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('afriscribe/img/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('afriscribe/img/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('afriscribe/img/favicon-96x96.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('afriscribe/img/favicon.svg') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('afriscribe/img/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('afriscribe/img/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('afriscribe/img/favicon.svg') }}" color="#0c1e35">

    <!-- Theme Colors -->
    <meta name="theme-color" content="#0c1e35">
    <meta name="msapplication-TileColor" content="#0c1e35">
    <meta name="msapplication-config" content="{{ asset('afriscribe/img/browserconfig.xml') }}">

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- DNS prefetch for external resources -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            line-height: 1.6;
            background: #f8f9fb;
            color: #333;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: #0c1e35;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
        }

        .navbar .logo img {
            height: 60px;
            width: auto;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }

        .navbar ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .navbar ul li a:hover {
            color: #f9b233;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 5px;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: #fff;
            border-radius: 2px;
            transition: 0.3s;
        }

        /* Mobile Menu */
        .nav-links {
            display: flex;
        }

        .nav-links.active {
            display: block;
            position: absolute;
            top: 60px;
            right: 0;
            background: #0c1e35;
            width: 200px;
            padding: 1rem;
            flex-direction: column;
        }

        .nav-links.active li {
            margin-bottom: 1rem;
        }

        /* Hero */
        .hero {
            text-align: center;
            padding: 6rem 2rem 4rem;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                url('https://www.star-vietnam.com.vn/Data/Sites/1/News/31/glenn-carstens-peters-npxxwgq33zq-unsplash.jpg') no-repeat center/cover;
            color: #fff;
            min-height: 60vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            background: #f9b233;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 25px;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            max-width: 250px;
            margin: 0 auto;
        }

        .btn:hover {
            background: #e6a029;
            transform: translateY(-2px);
        }

        /* Services Section */
        .services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            text-align: center;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin-bottom: 1rem;
            color: #0c1e35;
            font-size: 1.5rem;
        }

        .card p {
            color: #666;
            line-height: 1.6;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            background: #f9b233;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: #0c1e35;
        }

        /* Features Section */
        .features {
            background: #0c1e35;
            color: #fff;
            padding: 4rem 2rem;
            text-align: center;
        }

        .features h2 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .feature-item {
            padding: 1rem;
        }

        .feature-item h4 {
            margin-bottom: 0.5rem;
            color: #f9b233;
        }

        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, #f9b233, #e6a029);
            color: #0c1e35;
            padding: 4rem 2rem;
            text-align: center;
        }

        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-btn {
            background: #0c1e35;
            color: #fff;
            padding: 1rem 2rem;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .cta-btn:hover {
            background: #1a3a5c;
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background: #0c1e35;
            color: #fff;
            text-align: center;
            padding: 2rem;
        }

        footer p {
            margin: 0;
        }

        /* Content Sections */
        .content-section {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .content-section h1, .content-section h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #0c1e35;
        }

        .content-section p {
            text-align: center;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .dashboard-sidebar {
            width: 250px;
            background: #0c1e35;
            color: #fff;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .dashboard-sidebar .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .dashboard-sidebar .logo img {
            height: 50px;
            width: auto;
        }

        .dashboard-sidebar ul {
            list-style: none;
            padding: 0;
        }

        .dashboard-sidebar ul li {
            margin-bottom: 0.5rem;
        }

        .dashboard-sidebar ul li a {
            display: block;
            padding: 1rem 2rem;
            color: #fff;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .dashboard-sidebar ul li a:hover,
        .dashboard-sidebar ul li a.active {
            background: #f9b233;
            color: #0c1e35;
        }

        .dashboard-main {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
        }

        .dashboard-header {
            background: #fff;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        /* Form Layout */
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h3 {
            color: #0c1e35;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #0c1e35;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #f9b233;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .submit-btn {
            background: #f9b233;
            color: #0c1e35;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: #e6a029;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media(max-width: 768px) {
            .navbar ul {
                display: none;
                flex-direction: column;
            }

            .hamburger {
                display: flex;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .services {
                grid-template-columns: 1fr;
                padding: 2rem 1rem;
            }

            .features h2 {
                font-size: 2rem;
            }

            .cta h2 {
                font-size: 2rem;
            }

            .dashboard-container {
                flex-direction: column;
            }

            .dashboard-sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .dashboard-main {
                margin-left: 0;
            }

            .form-container {
                padding: 2rem 1rem;
            }
        }

        /* Custom styles for specific pages */
        @yield('custom-styles')
    </style>

    <!-- Custom Head Content -->
    @yield('head')
</head>

<body>
    <!-- Navigation -->
    @include('afriscribe.partials.as-nav')

    <!-- Page Content -->
    @yield('content')

    <!-- Footer -->
    @include('afriscribe.partials.as-footer')

    <!-- Scripts -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Mobile menu toggle is now handled in the as-nav partial
    </script>

    <!-- Custom Scripts -->
    @yield('scripts')
</body>

</html>
