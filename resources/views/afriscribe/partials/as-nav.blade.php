    <!-- Navbar -->
    <nav class="navbar">
        <a href="{{ route('afriscribe.welcome') }}" class="logo">
            <img src="{{ asset('afriscribe/img/afriscribe-logo-white.png') }}" alt="AfriScribe Logo">
        </a>
        <ul class="nav-links">
            <li><a href="{{ route('afriscribe.welcome') }}">Home</a></li>
            <li><a href="#services">Products</a></li>
            <li><a href="#features">Features</a></li>
            <li><a href="{{ route('afriscribe.quote-request.create') }}">Get Quote</a></li>
        </ul>
        <div class="hamburger" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>