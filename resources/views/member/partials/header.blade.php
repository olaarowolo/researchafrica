<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0BeIjyytfqxuMPsV0VDKY4GM2u07V7oxA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    @include('member.partials.topbar-padding-style')
    <style>
        /* Show mega-menu when the parent group has 'is-open' class */
        .group.is-open .mega-menu {
            display: block;
        }

        /* Rotate arrow when the parent group has 'is-open' class */
        .group.is-open svg {
            transform: rotate(180deg);
        }

        /* Disable transition for instant state change */
        .no-transition {
            transition: none !important;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased">
    @php
        $navigation = [
            [
                'title' => 'Home',
                'url' => '/',
                'icon' => 'fas fa-home',
                'target_blank' => false,
            ],
            [
                'title' => 'Information For',
                'icon' => '',
                'target_blank' => false,
                'mega_menu_position' => 'left-0',
                'mega_menu_cols' => 'grid-cols-2 md:grid-cols-4',
                'children' => [
                    [
                        'title' => 'Authors',
                        'url' => '/information/authors',
                        'children' => [
                            ['title' => 'Author’s Guidelines', 'url' => '/information/authors', 'icon' => 'fas fa-pen'],
                            ['title' => 'Ethics Guidelines', 'url' => '/ethics', 'icon' => 'fas fa-balance-scale'],
                        ],
                    ],
                    [
                        'title' => 'Editors',
                        'url' => '/information/editors',
                        'children' => [
                            ['title' => 'Editorial Policy', 'url' => '/information/editors', 'icon' => 'fas fa-edit'],
                            [
                                'title' => 'Responsibilities',
                                'url' => '/information/editors',
                                'icon' => 'fas fa-user-tie',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Researchers',
                        'url' => '/information/researchers',
                        'children' => [
                            [
                                'title' => 'Submit Paper',
                                'url' => '/information/researchers',
                                'icon' => 'fas fa-file-upload',
                            ],
                            [
                                'title' => 'Opportunities',
                                'url' => '/information/researchers',
                                'icon' => 'fas fa-hand-holding-dollar',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Reviewers',
                        'url' => '/information/reviewers',
                        'children' => [
                            [
                                'title' => 'Review Guidelines',
                                'url' => '/information/reviewers',
                                'icon' => 'fas fa-check-circle',
                            ],
                            [
                                'title' => 'Join as Reviewer',
                                'url' => '/information/reviewers',
                                'icon' => 'fas fa-user-plus',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Services',
                'icon' => '',
                'target_blank' => false,
                'mega_menu_position' => 'left-1/2 -translate-x-1/2',
                'mega_menu_cols' => 'grid-cols-2',
                'children' => [
                    [
                        'title' => 'AfriScribe',
                        'url' => 'https://afriscribe.org',
                        'target_blank' => true,
                    ],
                    [
                        'title' => 'Research Africa Services',
                        'url' => '#',
                        'children' => [
                            [
                                'title' => 'Consulting (Coming Soon)',
                                'url' => '/services/consulting',
                                'icon' => 'fas fa-chalkboard-teacher',
                            ],
                            [
                                'title' => 'Training (Coming Soon)',
                                'url' => '/services/training',
                                'icon' => 'fas fa-school',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Journals',
                'url' => '/journals',
                'icon' => 'fas fa-book-open',
                'target_blank' => false,
            ],
            [
                'title' => 'FAQ',
                'url' => '/faq',
                'icon' => 'fas fa-question-circle',
                'target_blank' => false,
            ],
            [
                'title' => 'Blog',
                'url' => 'http://blog.researchafricapublications.com',
                'icon' => 'fas fa-blog',
                'target_blank' => true,
            ],
        ];
    @endphp

    <!-- Top Bar -->
    <div id="top-bar" class="fixed top-0 left-0 w-full z-50">
        <div class="container max-w-7xl mx-auto bg-white/80 backdrop-blur-lg shadow-2xl rounded-2xl">
            <div class="kb-flex kb-justify-between kb-items-center">
                <div class="">
                    <!-- Top Social -->
                    <ul id="top-social">
                        <li>
                            <a href="{{ $setting->facebook_url ?? '#' }}" class="h-bg-facebook" target="_blank">
                                <span class="ts-icon"><i class="fa-brands fa-facebook-f"></i></span>
                                <span class="ts-text">Facebook</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $setting->twitter_url ?? '#' }}" class="h-bg-twitter" target="_blank">
                                <span class="ts-icon"><i class="fa-brands fa-twitter"> </i></span>
                                <span class="ts-text">Twitter</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $setting->instagram_url ?? '#' }}" class="h-bg-instagram" target="_blank">
                                <span class="ts-icon"><i class="fa-brands fa-instagram"> </i></span>
                                <span class="ts-text">Instagram</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $setting->linkedin_url ?? '#' }}" class="h-bg-linkedin" target="_blank">
                                <span class="ts-icon"><i class="fa-brands fa-linkedin"> </i></span>
                                <span class="ts-text">Linkedin</span>
                            </a>
                        </li>
                    </ul><!-- #top-social end -->
                </div>
                <div class="">
                    <!-- Top Links -->
                    <div class="top-links">
                        <ul class="top-links-container">
                            @auth('member')
                                <li class="top-links-item"><a href="{{ route('member.profile') }}"
                                        class="text-xs sm:text-sm"><i class="fa fa-user"></i>View Profile</a></li>
                            @else
                                <li class="top-links-item"><a href="{{ route('member.register') }}"
                                        class="text-xs sm:text-sm"><i class="fa-sharp fa-solid fa-plus"></i>Create
                                        Account</a></li>
                                <li class="top-links-item"><a href="{{ route('member.login') }}"
                                        class="text-xs sm:text-sm"><i class="fa-sharp fa-solid fa-right-to-bracket"></i>Sign
                                        In</a></li>
                            @endauth
                        </ul>
                    </div><!-- .top-links end -->
                </div>
            </div>
        </div>
    </div>
    <!-- #top-bar end -->

    <div class="">
        <!-- Nav Bar -->
        <header class="p-4 sticky top-0 z-50">
            <div
                class="max-w-7xl mx-auto bg-white/80 backdrop-blur-lg shadow-2xl rounded-2xl px-4 py-3 flex justify-between lg:justify-start items-center text-sm font-semibold">

                <div class="flex items-center space-x-2 lg:hidden">
                    <button id="menu-toggle" class="text-gray-600 focus:outline-none">
                        <svg id="menu-icon-menu" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="menu-icon-x" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <a href="/" class="flex items-center lg:mr-8">
                    <img src="/images/logo-ls-blk.png" alt="Research Africa Logo" class="h-12 w-auto" />
                </a>

                <ul id="menu"
                    class="hidden lg:flex items-center justify-center flex-grow space-x-6 lg:space-x-8 font-light text-gray-700 text-sm md:text-xs sm:text-xs">
                    @foreach ($navigation as $item)
                        @include('member.partials.desktop-nav-item', ['item' => $item])
                    @endforeach
                </ul>
                <div class="flex items-center space-x-4 text-gray-600 lg:ml-auto">
                    <a href="/login"
                        class="bg-gradient-to-r from-gray-900 to-black text-white px-3 py-2 rounded-lg font-semibold hover:from-black hover:to-gray-800 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5 hidden lg:block text-xs">Submit
                        Paper</a>

                    <a href="/login" class="flex flex-col items-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="hidden sm:inline">Access</span>
                    </a>
                    <a href="#" class="flex flex-col items-center relative">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="hidden sm:inline">Cart</span>
                    </a>
                </div>
            </div>
        </header>
        <!-- Nav-bar end -->
    </div>

    <div id="mobile-menu-dropdowns"
        class="hidden lg:hidden top-full left-0 z-5 bg-white w-full min-h-[calc(100vh-4rem">

        @php
            $generalNav = array_filter($navigation, fn($item) => empty($item['children']));
            $dropdownNav = array_filter($navigation, fn($item) => !empty($item['children']));
        @endphp

        <div id="general-nav" class="p-4 border-b border-gray-200">
            <ul class="space-y-3 text-base font-medium text-gray-700">
                @foreach ($generalNav as $item)
                    @include('member.partials.mobile-nav-item', ['item' => $item])
                @endforeach
                <li class="pt-4 border-t border-gray-200">
                    <a href="/login"
                        class="block text-center py-2 px-4 bg-gradient-to-r from-gray-900 to-black text-white rounded-lg font-semibold hover:bg-black transition">Submit
                        Paper</a>
                </li>
            </ul>
        </div>

        @foreach ($dropdownNav as $item)
            @include('member.partials.mobile-nav-item', ['item' => $item])
        @endforeach

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileDropdowns = document.getElementById('mobile-menu-dropdowns');
            const menuToggle = document.getElementById('menu-toggle');
            const iconX = document.getElementById('menu-icon-x');
            const iconMenu = document.getElementById('menu-icon-menu');

            // --- 1. Main Menu Toggle Logic ---

            menuToggle.addEventListener('click', () => {
                // Toggle visibility of the mobile menu
                mobileDropdowns.classList.toggle('hidden');

                // Toggle the icons
                if (mobileDropdowns.classList.contains('hidden')) {
                    // Menu is CLOSED, show 'Menu' icon
                    iconX.classList.add('hidden');
                    iconMenu.classList.remove('hidden');
                } else {
                    // Menu is OPEN, show 'X' icon
                    iconMenu.classList.add('hidden');
                    iconX.classList.remove('hidden');
                }
            });

            // --- 2. Dropdown Toggle Logic (Updated to include 'services') ---

            function setupDropdown(toggleId, contentId, arrowId) {
                const toggle = document.getElementById(toggleId);
                const content = document.getElementById(contentId);
                const arrow = document.getElementById(arrowId);

                if (!toggle || !content || !arrow) return; // Safety check

                toggle.addEventListener('click', (event) => {
                    event.stopPropagation(); // Prevent event from bubbling up to menu-toggle

                    // Toggle visibility of the content (using the custom class)
                    content.classList.toggle('hidden');

                    // Toggle the arrow rotation (using Tailwind's rotate-180 utility)
                    arrow.classList.toggle('rotate-180');
                });
            }

            // Dynamically set up dropdowns based on the navigation data
            document.querySelectorAll('[id$="-toggle"]').forEach(toggleElement => {
                const slug = toggleElement.id.replace('-toggle', '');
                setupDropdown(`${slug}-toggle`, `${slug}-content`, `${slug}-arrow`);
            });
        });

        // --- 3. Desktop Dropdown Click Logic ---
        document.addEventListener('DOMContentLoaded', () => {
            const menuGroups = document.querySelectorAll('.relative.group');
            let closeTimer;

            menuGroups.forEach(group => {
                const toggle = group.querySelector('[data-menu-toggle]');
                const content = group.querySelector('[data-menu-content]');
                const arrowSvg = group.querySelector('svg');

                if (!toggle || !content) return;

                toggle.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const wasOpen = group.classList.contains('is-open');

                    // Close all other menus
                    menuGroups.forEach(g => g.classList.remove('is-open'));
                    // Reset all arrows
                    document.querySelectorAll('.relative.group svg').forEach(svg => {
                        svg.classList.add('no-transition');
                    });

                    // If it was closed, open it.
                    if (!wasOpen) {
                        group.classList.add('is-open');
                        // Allow animation only when opening
                        arrowSvg.classList.remove('no-transition');
                    }
                });

                // Stop clicks inside the menu from closing it
                content.addEventListener('click', (event) => {
                    event.stopPropagation();
                });

                // When mouse leaves the entire group (li + dropdown)
                group.addEventListener('mouseleave', () => {
                    // Set a short delay before closing. This allows the cursor
                    // to move from the button to the dropdown content.
                    closeTimer = setTimeout(() => {
                        group.classList.remove('is-open');
                        if (arrowSvg) {
                            // Prevent animation on close
                            arrowSvg.classList.add('no-transition');
                        }
                    }, 50); // 50ms delay
                });

                group.addEventListener('mouseenter', () => {
                    // When re-entering the menu area, clear any pending close timers
                    clearTimeout(closeTimer);
                });
            });

            // Close all menus if clicking anywhere else on the page
            document.addEventListener('click', () => {
                document.querySelectorAll('.group.is-open').forEach(openGroup => {
                    openGroup.classList.remove('is-open');
                });
            });
        });
    </script>

</body>

</html>
