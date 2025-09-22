<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AfriScribe | Research Africa</title>
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
        }
    </style>
</head>

<body>

@include('afriscribe.partials.as-nav')

@include('afriscribe.partials.as-hero')

    <!-- Services -->
@include('afriscribe.partials.as-services')

    <!-- Features Section -->
@include('afriscribe.partials.as-features')

    <!-- CTA Section -->
@include('afriscribe.partials.as-cta')

{{-- @include('afriscribe.partials.as-pr-form') --}}
    <!-- Footer -->
    <footer>
        <p>&copy; 2025 AfriScribe | Research Africa. All Rights Reserved.</p>
    </footer>

    <script>
        function toggleMenu() {
            document.querySelector('.nav-links').classList.toggle('active');
        }

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
    </script>

</body>

</html>
