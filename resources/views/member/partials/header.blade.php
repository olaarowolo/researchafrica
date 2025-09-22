<!-- Top Bar
     ============================================= -->
<div id="top-bar">
    <div class="container">

        <div class="kb-flex kb-justify-between kb-items-center">
            <div class="">

                <!-- Top Social
                               ============================================= -->
                <ul id="top-social">
                    <li>
                        <a href="{{ $setting->facebook_url ?? '' }}" class="h-bg-facebook" target="_blank">
                            <span class="ts-icon">
                                <i class="fa-brands fa-facebook-f"></i>
                            </span>
                            <span class="ts-text">
                                Facebook
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $setting->twitter_url ?? '' }}" class="h-bg-twitter" target="_blank">
                            <span class="ts-icon">
                                <i class="fa-brands fa-twitter"> </i>
                            </span>
                            <span class="ts-text">
                                Twitter
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $setting->instagram_url ?? '' }}" class="h-bg-instagram" target="_blank">
                            <span class="ts-icon">
                                <i class="fa-brands fa-instagram"> </i>
                            </span>
                            <span class="ts-text">
                                Instagram
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $setting->linkedin_url ?? '' }}" class="h-bg-linkedin" target="_blank">
                            <span class="ts-icon">
                                <i class="fa-brands fa-linkedin"> </i>
                            </span>
                            <span class="ts-text">
                                Linkedin
                            </span>
                        </a>
                    </li>
                </ul><!-- #top-social end -->

            </div>

            <div class="">

                <!-- Top Links
                               ============================================= -->
                <div class="top-links">
                    <ul class="top-links-container">
                        @auth('member')
                            <li class="top-links-item"><a href="{{ route('member.profile') }}"><i
                                        class="fa fa-user
                                            "></i>View Profile</a>
                            </li>
                        @else
                            <li class="top-links-item">
                                <a href="{{ route('member.register') }}">
                                    <i class="fa-sharp fa-solid fa-plus"></i>Create Account</a>
                            </li>
                            <li class="top-links-item">
                                <a href="{{ route('member.login') }}">
                                    <i class="fa-sharp fa-solid fa-right-to-bracket"></i>Sign In</a>
                            </li>
                        @endauth


                    </ul>
                </div><!-- .top-links end -->

            </div>
        </div>

    </div>
</div><!-- #top-bar end -->


<nav class="kb-bg-white kb-border-gray-200">
    <div class="kb-max-w-screen-xl kb-flex kb-flex-wrap kb-items-center kb-justify-between kb-mx-auto kb-p-4">
        <a href="/" class="kb-flex kb-items-center">
            <img src="{{ asset('images/logo.png') }}" class="kb-mr-3 kb-h-16 md:kb-h-24" alt="Research  Logo" />
        </a>
        <button type="button"
            class="kb-inline-flex kb-items-center kb-p-2 kb-ml-3 kb-text-sm kb-text-black kb-rounded-lg md:kb-hidden btnBar close"
            aria-expanded="false">
            <span class="kb-sr-only">Open main menu</span>
            <i class="fa fa-bars fa-2x" aria-hidden="true"></i>
        </button>
        <div class="kb-hidden kb-w-full md:kb-block md:kb-w-auto" id="navbar-default">
            <ul
                class="kb-font-medium kb-flex kb-flex-col kb-items-center kb-p-4 md:kb-p-0 kb-mt-4 kb-border kb-border-gray-100 kb-rounded-lg kb-bg-gray-50 md:kb-flex-row md:kb-space-x-8 md:kb-mt-0 md:kb-border-0 md:kb-bg-white">

                <li>
                    <a class="menu-link {{ request()->routeIs('home') ? 'active-a' : '' }}" href="{{ route('home') }}">
                        <div>Home</div>
                    </a>
                </li>

                <li class="nav-item dropdown mega-menu-item">
                    <a class="menu-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                        data-mdb-toggle="dropdown" aria-expanded="false">
                        Information for
                    </a>
                    <div class="dropdown-menu mega-menu" aria-labelledby="navbarDropdownMenuLink">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="/information/authors" class="dropdown-item"><h5>Authors</h5></a>
                                    <ul class="list-unstyled">
                                        <li><a href="/information/authors" class="dropdown-item">Author's Guidelines</a></li>
                                        <li><a href="/ethics" class="dropdown-item">Ethics Guidelines</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <a href="/information/editors" class="dropdown-item"><h5>Editors</h5></a>
                                </div>
                                <div class="col-md-3">
                                    <a href="/information/researchers" class="dropdown-item"><h5>Researchers</h5></a>
                                </div>
                                <div class="col-md-3">
                                    <a href="/information/reviewers" class="dropdown-item"><h5>Reviewers</h5></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown mega-menu-item">
                    <a class="menu-link dropdown-toggle" href="#" id="navbarDropdownRA" role="button"
                        data-mdb-toggle="dropdown" aria-expanded="false">
                        Services
                    </a>
                    <div class="dropdown-menu mega-menu" aria-labelledby="navbarDropdownRA">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                    <a href="/afriscribe/home" class="dropdown-item"><h5 class="mega-menu-title">AfriScribe</h5></a>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('afriscribe.welcome') }}" class="dropdown-item">AfriScribe Manuscripts Manager</a></li>
                        <li><a href="{{ route('afriscribe.welcome') }}" class="dropdown-item">AfriScribe Proofread</a></li>
                        <li><a href="{{ route('afriscribe.welcome') }}" class="dropdown-item">AfriScribe Insights</a></li>
                        <li><a href="{{ route('afriscribe.welcome') }}" class="dropdown-item">AfriScribe Connect</a></li>
                        <li><a href="{{ route('afriscribe.welcome') }}" class="dropdown-item">AfriScribe Archive</a></li>
                        <li><a href="{{ route('afriscribe.welcome') }}" class="dropdown-item">AfriScribe Editor</a></li>
                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mega-menu-title">Research Africa Services</h5>
                                     <ul class="list-unstyled">
                                        <li><a href="/services/consulting" class="dropdown-item">Consulting (Coming Soon)</a></li>
                                        <li><a href="/services/training" class="dropdown-item">Training (Coming Soon)</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Keep your other items -->
                <li>
                    <a class="menu-link {{ request()->routeIs('member.about') ? 'active-a' : '' }}"
                        href="{{ route('member.about') }}">
                        <div>About Us</div>
                    </a>
                </li>
                <li>
                    <a class="menu-link {{ request()->routeIs('member.faq') ? 'active-a' : '' }}"
                        href="{{ route('member.faq') }}">
                        <div>FAQ</div>
                    </a>
                </li>
                {{-- <li>
                    <a class="menu-link {{ request()->routeIs('member.contact') ? 'active-a' : '' }}"
                        href="{{ route('member.contact') }}">
                        <div>Contact</div>
                    </a>
                </li> --}}
                <li>
                    <a class="menu-link" href="http://blog.researchafricapublications.com">
                        <div>Blog</div>
                    </a>
                </li>
                <li>
                    @auth('member')
                        @if (auth('member')->user()->member_type_id == 1)
                            <a class="menu-link px-3 rounded text-light bg-dark kb-text-white hover:kb-text-white"
                                href="{{ route('member.articles.create') }}">
                                <div>Create Article</div>
                            </a>
                        @endif
                    @else
                        <a class="menu-link px-3 rounded text-light bg-dark kb-text-white hover:kb-text-white"
                            href="{{ route('member.login') }}">
                            <div>Create Article</div>
                        </a>
                    @endauth
                </li>
            </ul>
        </div>
        <style>
            /* Base styles for the new layout */
            .container {
                max-width: 1280px;
                margin-left: auto;
                margin-right: auto;
            }

            .nav-link {
                display: block;
                padding: 0.5rem 1rem;
                font-weight: 500;
                color: #4b5563; /* Gray-700 */
                transition: color 0.2s ease-in-out;
            }

            .nav-link:hover, .nav-link.active {
                color: #1f2937; /* Gray-900 */
                text-decoration: underline;
            }

            .nav-link.active {
                font-weight: 600; /* Semi-bold for active link */
            }

            .btn-primary {
                display: inline-block;
                padding: 0.5rem 1rem;
                border-radius: 0.375rem; /* Rounded-md */
                color: #fff;
                background-color: #1f2937; /* Gray-900 */
                transition: background-color 0.2s ease-in-out;
            }

            .btn-primary:hover {
                background-color: #374151; /* Gray-800 */
            }

            /* Mega Menu styles */
            .mega-menu {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                z-index: 10;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                border-top: 1px solid #e5e7eb; /* Gray-200 */
            }

            .mega-menu-title {
                font-weight: bold;
                color: #1f2937; /* Gray-900 */
                margin-bottom: 0.5rem;
            }

            .mega-menu-item {
                color: #4b5563; /* Gray-700 */
                display: block;
                padding: 0.25rem 0;
                transition: color 0.2s ease-in-out;
            }

            .mega-menu-item:hover {
                color: #000;
                text-decoration: underline;
            }

            /* Responsive behavior for mobile menu */
            @media (min-width: 768px) {
                .mega-menu {
                    /* Keep mega menu aligned with parent on desktop */
                    left: 50%;
                    transform: translateX(-50%);
                    width: max-content;
                }
            }
        </style>

    </div>
</nav>

<div class="bg-light fw-bold text-danger fs-4">
    <marquee direction="left" scrollamount="3">Are you in need of a website to host your academic journal? We offer
        state-of-the-art digital peer review, ensures ethical standards, and promotes academic rigour. Join the Research
        Africa community and promote your publications. Contact us to learn more about our services and start publishing
        today</marquee>
</div>


<!-- #header end -->
@push('component')
    <script>
        $('.btnBar').click(function(e) {
            e.preventDefault();

            let thisBar = $(this);

            if (thisBar.hasClass('close')) {
                thisBar.removeClass('close').addClass('open');
                $('#navbar-default').show();


            } else {
                thisBar.removeClass('open').addClass('close');

                $('#navbar-default').hide();
            }

        });
    </script>
@endpush
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-MK4S7W7B2F"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-MK4S7W7B2F');
</script>
