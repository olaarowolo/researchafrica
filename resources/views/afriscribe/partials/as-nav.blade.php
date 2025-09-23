<!-- Navbar -->
<nav class="navbar">
    <a href="{{ route('afriscribe.welcome') }}" class="logo">
        @if(request()->routeIs('afriscribe.proofread*') || request()->routeIs('afriscribe.proofreading*'))
            <img src="{{ asset('afriscribe/img/afriscribe_proofread-logo-white.png') }}" alt="AfriScribe Proofreading Logo">
        @else
            <img src="{{ asset('afriscribe/img/afriscribe-logo-main-logo-white.png') }}" alt="AfriScribe Logo">
        @endif
    </a>
    <ul class="nav-links">
        <li><a href="{{ route('afriscribe.welcome') }}">Home</a></li>
        <li><a href="{{ route('afriscribe.about') }}">About</a></li>
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
