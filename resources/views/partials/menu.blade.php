<style>
    /* Global Styles */
    body {
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, "Roboto", "Helvetica Neue", sans-serif;
    }

    /* Modern Sidebar Styling */
    .modern-sidebar {
        background: linear-gradient(135deg, #c8d0d8 0%, #b0b8c0 50%, #9ca0a5 100%);
        border-right: 1px solid #8c949c;
        transition: width 0.3s ease;
        padding: 0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.20);
    }

    /* Brand Styling */
    .c-sidebar-brand {
        padding: 1.5rem 1rem !important;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }

    .c-sidebar-brand a {
        color: #212529 !important;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        font-weight: 600;
    }

    .c-sidebar-brand img {
        transition: transform 0.3s ease-in-out;
    }

    /* Navigation Links */
    .c-sidebar-nav {
        padding: 1rem 0;
    }

    .c-sidebar-nav-item {
        margin-bottom: 0.75rem;
    }

    .c-sidebar-nav-link, .c-sidebar-nav-dropdown-toggle {
        color: #212529;
        position: relative;
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        padding: 0.75rem 1.25rem !important;
        border-radius: 8px;
        display: flex;
        align-items: center;
        text-decoration: none;
        font-weight: 500;
    }

    .c-sidebar-nav-link:hover,
    .c-sidebar-nav-link:focus,
    .c-sidebar-nav-dropdown-toggle:hover,
    .c-sidebar-nav-dropdown-toggle:focus {
        background-color: #e9ecef;
        color: #1a1a1a;
    }

    .c-sidebar-nav-link.is-active {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        color: #0d47a1 !important;
        font-weight: 600;
        border-left: 3px solid #0d47a1;
    }

    .c-sidebar-nav-link.is-active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 60%;
        width: 3px;
        background-color: #1565c0;
        border-radius: 0 5px 5px 0;
    }

    /* Dropdown Toggles */
    .c-sidebar-nav-dropdown.open > .c-sidebar-nav-dropdown-toggle {
        background-color: #e9ecef;
        color: #1a1a1a;
        font-weight: 600;
    }

    /* Dropdown Arrow Rotation */
    .dropdown-icon {
        transition: transform 0.2s ease-in-out;
    }

    .c-sidebar-nav-dropdown.open .dropdown-icon {
        transform: rotate(180deg);
    }

    /* Dropdown Menu Styling */
    .c-sidebar-nav-dropdown-items {
        padding: 0.5rem 0 0.5rem 1.5rem;
        background-color: #f8f9fa;
        border-left: 2px solid #dee2e6;
        margin-top: 0;
        margin-bottom: 0.5rem;
        list-style-type: none;
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out, visibility 0.2s ease-in-out;
        visibility: hidden;
        max-height: 0;
        overflow: hidden;
    }

    .c-sidebar-nav-dropdown-items .c-sidebar-nav-item {
        margin-bottom: 0.5rem;
    }

    .c-sidebar-nav-dropdown-items.show {
        display: block;
        opacity: 1;
        transform: translateY(0);
        visibility: visible;
        max-height: 500px;
    }

    .c-sidebar-nav-dropdown-items .c-sidebar-nav-link {
        border-radius: 0;
        padding-left: 2.5rem !important;
        color: #212529;
        font-weight: 500;
    }

    .c-sidebar-nav-dropdown-items .c-sidebar-nav-link:hover {
        color: #1a1a1a;
        background-color: #f8f9fa;
    }

    .c-sidebar-nav-link.is-child-active {
        color: #0d47a1 !important;
        font-weight: 600;
        background-color: #e3f2fd;
    }

    /* Icons */
    .c-sidebar-nav-icon {
        width: 24px;
        text-align: center;
        font-size: 1.1rem;
    }
</style>

<div class="c-sidebar c-sidebar-fixed c-sidebar-lg-show modern-sidebar shadow-lg" id="sidebar" role="navigation" aria-label="Main navigation">

    <!-- Sidebar Brand -->
    <div class="c-sidebar-brand d-md-down-none">
        <a class="w-100 d-flex align-items-center text-decoration-none" href="{{ route('admin.home') }}" aria-label="Go to dashboard">
            <img src="{{ $setting && $setting->logo ? $setting->logo->getUrl() : 'https://placehold.co/40x40/6c757d/ffffff?text=Logo' }}"
                 alt="Logo"
                 width="40"
                 height="40"
                 class="me-3 rounded-circle shadow-sm"
                 loading="lazy"
                 onerror="this.onerror=null;this.src='https://placehold.co/40x40/6c757d/ffffff?text=Logo';">
            <span class="h5 fw-bold mb-0">{{ $setting ? $setting->website_name : trans('panel.site_title') }}</span>
        </a>
    </div>

    <!-- Sidebar Navigation -->
    <ul class="c-sidebar-nav list-unstyled p-3 mb-0">
        <!-- Dashboard Link -->
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all {{ request()->is('admin/home*') ? 'is-active' : '' }}"
               href="{{ route('admin.home') }}"
               aria-label="Dashboard"
               aria-current="{{ request()->is('admin/home*') ? 'page' : 'false' }}">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt me-3" aria-hidden="true"></i>
                <span class="menu-text">{{ trans('global.dashboard') }}</span>
            </a>
        </li>

        <!-- AfriScribe Dropdown (collapsed by default) -->
        <li class="c-sidebar-nav-dropdown">
            <a class="c-sidebar-nav-dropdown-toggle d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all"
               href="#"
               data-dropdown="afriscribe-dropdown"
               aria-expanded="false"
               aria-label="AfriScribe">
                <i class="fa-fw fas fa-edit c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                <span class="menu-text">AfriScribe</span>
                <i class="fas fa-chevron-down ms-auto dropdown-icon" aria-hidden="true"></i>
            </a>
            <ul id="afriscribe-dropdown" class="c-sidebar-nav-dropdown-items list-unstyled shadow-sm rounded-2 mt-2" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/afriscribe*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.afriscribe.requests') }}"
                       aria-label="AfriScribe Admin"
                       style="color: #1565c0; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-cogs c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">AfriScribe Admin</span>
                    </a>
                </li>
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all"
                       href="{{ route('afriscribe.admin.dashboard') }}"
                       target="_blank"
                       aria-label="AfriScribe Dashboard"
                       style="color: #1565c0; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-tachometer-alt c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">AfriScribe Dashboard</span>
                    </a>
                </li>
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all"
                       href="{{ route('afriscribe.welcome') }}"
                       target="_blank"
                       aria-label="AfriScribe Landing Page"
                       style="color: #1565c0; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-external-link-alt c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">AfriScribe Home</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Research Africa Dropdown (collapsed by default) -->
        <li class="c-sidebar-nav-dropdown">
            <a class="c-sidebar-nav-dropdown-toggle d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all"
               href="#"
               data-dropdown="research-africa-dropdown"
               aria-expanded="false"
               aria-label="Research Africa">
                <i class="fa-fw fas fa-book c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                <span class="menu-text">Research Africa</span>
                <i class="fas fa-chevron-down ms-auto dropdown-icon" aria-hidden="true"></i>
            </a>
            <ul id="research-africa-dropdown" class="c-sidebar-nav-dropdown-items list-unstyled shadow-sm rounded-2 mt-2" style="background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                <!-- Member Management -->
                @can('member_management_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/members*') || request()->is('admin/member-types*') || request()->is('admin/member-roles*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.members.index') }}"
                       aria-label="{{ trans('cruds.memberManagement.title') }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-users c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.memberManagement.title') }}</span>
                    </a>
                </li>
                @endcan

                <!-- Article Categories -->
                @can('article_category_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/article-categories*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.article-categories.index') }}"
                       aria-label="{{ trans('cruds.articleCategory.title') }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-folder c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.articleCategory.title') }}</span>
                    </a>
                </li>
                @endcan

                <!-- Article Journal -->
                @can('article_category_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/article-sub-categories*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.article-sub-categories.index') }}"
                       aria-label="Article Journal"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-folder c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">Article Journal</span>
                    </a>
                </li>
                @endcan

                <!-- Article Keywords -->
                @can('article_category_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/article-keywords*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.article-keywords.index') }}"
                       aria-label="{{ __('Article Keyword') }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-tags c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ __('Article Keyword') }}</span>
                    </a>
                </li>
                @endcan

                <!-- Articles -->
                @can('article_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/articles*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.articles.index') }}"
                       aria-label="{{ trans('cruds.article.title') }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-file c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.article.title') }}</span>
                    </a>
                </li>
                @endcan

                <!-- Comments -->
                @can('comment_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/comments*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.comments.index') }}"
                       aria-label="{{ trans('cruds.comment.title') }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-comments c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.comment.title') }}</span>
                    </a>
                </li>
                @endcan

                <!-- About Us -->
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all {{ request()->is('admin/abouts*') ? 'is-active' : '' }}"
                       href="{{ route('admin.abouts.index') }}"
                       aria-label="About Us"
                       aria-current="{{ request()->is('admin/abouts*') ? 'page' : 'false' }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-user c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="menu-text">About Us</span>
                    </a>
                </li>

                <!-- FAQ Management -->
                @can('faq_management_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/faq-categories*') || request()->is('admin/faq-questions*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.faq-categories.index') }}"
                       aria-label="{{ trans('cruds.faqManagement.title') }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-question c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.faqManagement.title') }}</span>
                    </a>
                </li>
                @endcan

                <!-- Content Management -->
                @can('content_management_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/content-categories*') || request()->is('admin/content-tags*') || request()->is('admin/content-pages*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.content-categories.index') }}"
                       aria-label="{{ trans('cruds.contentManagement.title') }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-book c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.contentManagement.title') }}</span>
                    </a>
                </li>
                @endcan

                <!-- User Management -->
                @can('user_management_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/permissions*') || request()->is('admin/roles*') || request()->is('admin/users*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.permissions.index') }}"
                       aria-label="{{ trans('cruds.userManagement.title') }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-users c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.userManagement.title') }}</span>
                    </a>
                </li>
                @endcan

                <!-- Subscription Management -->
                @can('subscription_management_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/subscriptions*') || request()->is('admin/member-subscriptions*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.subscriptions.index') }}"
                       aria-label="{{ trans('cruds.subscriptionManagement.title') }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-cogs c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.subscriptionManagement.title') }}</span>
                    </a>
                </li>
                @endcan

                <!-- Countries -->
                @can('country_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/countries*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.countries.index') }}"
                       aria-label="{{ trans('cruds.country.title') }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-flag c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.country.title') }}</span>
                    </a>
                </li>
                @endcan

                <!-- Settings -->
                @can('setting_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/settings*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.settings.index') }}"
                       aria-label="{{ trans('cruds.setting.title') }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-cogs c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.setting.title') }}</span>
                    </a>
                </li>
                @endcan

                <!-- Change Password -->
                @if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                @can('profile_password_edit')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'is-active' : '' }}"
                       href="{{ route('profile.password.edit') }}"
                       aria-label="{{ trans('global.change_password') }}"
                       aria-current="{{ request()->is('profile/password*') ? 'page' : 'false' }}"
                       style="color: #2e7d32; border-radius: 8px; margin: 4px; padding: 10px 16px !important;">
                        <i class="fa-fw fas fa-key c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('global.change_password') }}</span>
                    </a>
                </li>
                @endcan
                @endif
            </ul>
        </li>

        <!-- Other Links and Dropdowns Follow a similar pattern... -->


        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all {{ request()->is("admin/abouts*") ? 'is-active' : '' }}"
               href="{{ route('admin.abouts.index') }}"
               aria-label="About Us"
               aria-current="{{ request()->is('admin/abouts*') ? 'page' : 'false' }}">
                <i class="fa-fw fas fa-user c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                <span class="menu-text">About Us</span>
            </a>
        </li>

        @can('faq_management_access')
        <li class="c-sidebar-nav-dropdown {{ request()->is('admin/faq-categories*') || request()->is('admin/faq-questions*') ? 'open' : '' }}">
            <a class="c-sidebar-nav-dropdown-toggle d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all"
               href="#"
               data-dropdown="faq-management-dropdown"
               aria-expanded="{{ request()->is('admin/faq-categories*') || request()->is('admin/faq-questions*') ? 'true' : 'false' }}"
               aria-label="{{ trans('cruds.faqManagement.title') }}">
                <i class="fa-fw fas fa-question c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                <span class="menu-text">{{ trans('cruds.faqManagement.title') }}</span>
                <i class="fas fa-chevron-down ms-auto dropdown-icon" aria-hidden="true"></i>
            </a>
            <ul id="faq-management-dropdown" class="c-sidebar-nav-dropdown-items list-unstyled shadow-sm rounded-2 mt-2 {{ request()->is('admin/faq-categories*') || request()->is('admin/faq-questions*') ? 'show' : '' }}">
                @can('faq_category_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is("admin/faq-categories*") ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.faq-categories.index') }}"
                       aria-label="{{ trans('cruds.faqCategory.title') }}"
                       aria-current="{{ request()->is('admin/faq-categories*') ? 'page' : 'false' }}">
                        <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.faqCategory.title') }}</span>
                    </a>
                </li>
                @endcan
                @can('faq_question_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is("admin/faq-questions*") ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.faq-questions.index') }}"
                       aria-label="{{ trans('cruds.faqQuestion.title') }}"
                       aria-current="{{ request()->is('admin/faq-questions*') ? 'page' : 'false' }}">
                        <i class="fa-fw fas fa-question c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.faqQuestion.title') }}</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('content_management_access')
        <li class="c-sidebar-nav-dropdown {{ request()->is('admin/content-categories*') || request()->is('admin/content-tags*') || request()->is('admin/content-pages*') ? 'open' : '' }}">
            <a class="c-sidebar-nav-dropdown-toggle d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all"
               href="#"
               data-dropdown="content-management-dropdown"
               aria-expanded="{{ request()->is('admin/content-categories*') || request()->is('admin/content-tags*') || request()->is('admin/content-pages*') ? 'true' : 'false' }}"
               aria-label="{{ trans('cruds.contentManagement.title') }}">
                <i class="fa-fw fas fa-book c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                <span class="menu-text">{{ trans('cruds.contentManagement.title') }}</span>
                <i class="fas fa-chevron-down ms-auto dropdown-icon" aria-hidden="true"></i>
            </a>
            <ul id="content-management-dropdown" class="c-sidebar-nav-dropdown-items list-unstyled shadow-sm rounded-2 mt-2 {{ request()->is('admin/content-categories*') || request()->is('admin/content-tags*') || request()->is('admin/content-pages*') ? 'show' : '' }}">
                @can('content_category_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/content-categories*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.content-categories.index') }}"
                       aria-label="{{ trans('cruds.contentCategory.title') }}"
                       aria-current="{{ request()->is('admin/content-categories*') ? 'page' : 'false' }}">
                        <i class="fa-fw fas fa-folder c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.contentCategory.title') }}</span>
                    </a>
                </li>
                @endcan
                @can('content_tag_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is("admin/content-tags*") ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.content-tags.index') }}"
                       aria-label="{{ trans('cruds.contentTag.title') }}"
                       aria-current="{{ request()->is('admin/content-tags*') ? 'page' : 'false' }}">
                        <i class="fa-fw fas fa-tags c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.contentTag.title') }}</span>
                    </a>
                </li>
                @endcan
                @can('content_page_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is("admin/content-pages*") ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.content-pages.index') }}"
                       aria-label="{{ trans('cruds.contentPage.title') }}"
                       aria-current="{{ request()->is('admin/content-pages*') ? 'page' : 'false' }}">
                        <i class="fa-fw fas fa-file c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.contentPage.title') }}</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('user_management_access')
        <li class="c-sidebar-nav-dropdown {{ request()->is('admin/permissions*') || request()->is('admin/roles*') || request()->is('admin/users*') ? 'open' : '' }}">
            <a class="c-sidebar-nav-dropdown-toggle d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all"
               href="#"
               data-dropdown="user-management-dropdown"
               aria-expanded="{{ request()->is('admin/permissions*') || request()->is('admin/roles*') || request()->is('admin/users*') ? 'true' : 'false' }}"
               aria-label="{{ trans('cruds.userManagement.title') }}">
                <i class="fa-fw fas fa-users c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                <span class="menu-text">{{ trans('cruds.userManagement.title') }}</span>
                <i class="fas fa-chevron-down ms-auto dropdown-icon" aria-hidden="true"></i>
            </a>
            <ul id="user-management-dropdown" class="c-sidebar-nav-dropdown-items list-unstyled shadow-sm rounded-2 mt-2 {{ request()->is('admin/permissions*') || request()->is('admin/roles*') || request()->is('admin/users*') ? 'show' : '' }}">
                @can('permission_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is("admin/permissions*") ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.permissions.index') }}"
                       aria-label="{{ trans('cruds.permission.title') }}"
                       aria-current="{{ request()->is('admin/permissions*') ? 'page' : 'false' }}">
                        <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.permission.title') }}</span>
                    </a>
                </li>
                @endcan
                @can('role_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is("admin/roles*") ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.roles.index') }}"
                       aria-label="{{ trans('cruds.role.title') }}"
                       aria-current="{{ request()->is('admin/roles*') ? 'page' : 'false' }}">
                        <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.role.title') }}</span>
                    </a>
                </li>
                @endcan
                @can('user_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is("admin/users*") ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.users.index') }}"
                       aria-label="{{ trans('cruds.user.title') }}"
                       aria-current="{{ request()->is('admin/users*') ? 'page' : 'false' }}">
                        <i class="fa-fw fas fa-user c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.user.title') }}</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('subscription_management_access')
        <li class="c-sidebar-nav-dropdown {{ request()->is('admin/subscriptions*') || request()->is('admin/member-subscriptions*') ? 'open' : '' }}">
            <a class="c-sidebar-nav-dropdown-toggle d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all"
               href="#"
               data-dropdown="subscription-management-dropdown"
               aria-expanded="{{ request()->is('admin/subscriptions*') || request()->is('admin/member-subscriptions*') ? 'true' : 'false' }}"
               aria-label="{{ trans('cruds.subscriptionManagement.title') }}">
                <i class="fa-fw fas fa-cogs c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                <span class="menu-text">{{ trans('cruds.subscriptionManagement.title') }}</span>
                <i class="fas fa-chevron-down ms-auto dropdown-icon" aria-hidden="true"></i>
            </a>
            <ul id="subscription-management-dropdown" class="c-sidebar-nav-dropdown-items list-unstyled shadow-sm rounded-2 mt-2 {{ request()->is('admin/subscriptions*') || request()->is('admin/member-subscriptions*') ? 'show' : '' }}">
                @can('subscription_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is("admin/subscriptions*") ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.subscriptions.index') }}"
                       aria-label="{{ trans('cruds.subscription.title') }}"
                       aria-current="{{ request()->is('admin/subscriptions*') ? 'page' : 'false' }}">
                        <i class="fa-fw fas fa-align-justify c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.subscription.title') }}</span>
                    </a>
                </li>
                @endcan
                @can('member_subscription_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link d-flex align-items-center py-2 text-decoration-none transition-all {{ request()->is('admin/member-subscriptions*') ? 'is-active is-child-active' : '' }}"
                       href="{{ route('admin.member-subscriptions.index') }}"
                       aria-label="{{ trans('cruds.memberSubscription.title') }}"
                       aria-current="{{ request()->is('admin/member-subscriptions*') ? 'page' : 'false' }}">
                        <i class="fa-fw fas fa-align-justify c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                        <span class="submenu-text">{{ trans('cruds.memberSubscription.title') }}</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('country_access')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all {{ request()->is("admin/countries*") ? 'is-active' : '' }}"
               href="{{ route('admin.countries.index') }}"
               aria-label="{{ trans('cruds.country.title') }}"
               aria-current="{{ request()->is('admin/countries*') ? 'page' : 'false' }}">
                <i class="fa-fw fas fa-flag c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                <span class="menu-text">{{ trans('cruds.country.title') }}</span>
            </a>
        </li>
        @endcan

        @can('setting_access')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all {{ request()->is('admin/settings*') ? 'is-active' : '' }}"
               href="{{ route('admin.settings.index') }}"
               aria-label="{{ trans('cruds.setting.title') }}"
               aria-current="{{ request()->is('admin/settings*') ? 'page' : 'false' }}">
                <i class="fa-fw fas fa-cogs c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                <span class="menu-text">{{ trans('cruds.setting.title') }}</span>
            </a>
        </li>
        @endcan

        @if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
        @can('profile_password_edit')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none transition-all {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'is-active' : '' }}"
               href="{{ route('profile.password.edit') }}"
               aria-label="{{ trans('global.change_password') }}"
               aria-current="{{ request()->is('profile/password*') ? 'page' : 'false' }}">
                <i class="fa-fw fas fa-key c-sidebar-nav-icon me-3" aria-hidden="true"></i>
                <span class="menu-text">{{ trans('global.change_password') }}</span>
            </a>
        </li>
        @endcan
        @endif

        

        <!-- Logout Link -->
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link d-flex align-items-center py-2 px-3 rounded-2 text-decoration-none text-danger transition-all"
               href="javascript:void(0)"
               onclick="event.preventDefault(); document.getElementById('logoutLink').submit();"
               aria-label="{{ trans('global.logout') }}">
                <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt me-3" aria-hidden="true"></i>
                <span class="menu-text">{{ trans('global.logout') }}</span>
                <form id="logoutLink" action="{{ route('admin.logout') }}" method="post">@csrf</form>
            </a>
        </li>
    </ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdown functionality
    const dropdownToggles = document.querySelectorAll('[data-dropdown]');

    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const targetId = this.getAttribute('data-dropdown');
            const target = document.getElementById(targetId);
            const parentDropdown = this.closest('.c-sidebar-nav-dropdown');

            if (target) {
                const isCurrentlyOpen = target.classList.contains('show');

                // Close all other dropdowns first
                document.querySelectorAll('.c-sidebar-nav-dropdown-items.show').forEach(openItem => {
                    if (openItem !== target) {
                        openItem.classList.remove('show');
                        openItem.closest('.c-sidebar-nav-dropdown').classList.remove('open');
                    }
                });

                // Toggle current dropdown
                if (isCurrentlyOpen) {
                    // Close dropdown
                    target.classList.remove('show');
                    parentDropdown.classList.remove('open');
                } else {
                    // Open dropdown immediately
                    target.classList.add('show');
                    parentDropdown.classList.add('open');
                }
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.c-sidebar-nav-dropdown')) {
            document.querySelectorAll('.c-sidebar-nav-dropdown-items.show').forEach(item => {
                item.classList.remove('show');
                item.closest('.c-sidebar-nav-dropdown').classList.remove('open');
            });
        }
    });

    // Handle keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.c-sidebar-nav-dropdown-items.show').forEach(item => {
                item.classList.remove('show');
                item.closest('.c-sidebar-nav-dropdown').classList.remove('open');
            });
        }
    });
});
</script>
