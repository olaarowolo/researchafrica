<!-- Navbar -->
<nav class="navbar">
    <a href="{{ route('afriscribe.welcome') }}" class="logo">
        <img src="{{ asset('afriscribe/img/afriscribe-logo-white.png') }}" alt="AfriScribe Logo">
    </a>
    <ul class="nav-links">
        <li><a href="{{ route('afriscribe.welcome') }}">Home</a></li>
        <li><a href="/afriscribe/home/#services">Products</a></li>
        <li><a href="/afriscribe/home/#features">Features</a></li>
        <li><a href="{{ route('afriscribe.quote-request.create') }}">Get Quote</a></li>
    </ul>
    <div class="hamburger" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>

<script>
    // Mobile menu toggle function (called by onclick in HTML)
    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        if (navLinks) {
            navLinks.classList.toggle('active');
        }
    }

    // Smooth scrolling for anchor links
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
