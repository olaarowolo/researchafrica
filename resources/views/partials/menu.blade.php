<div class="c-sidebar c-sidebar-fixed c-sidebar-lg-show" id="sidebar">

    <div class="c-sidebar-brand d-md-down-none bg-light text-primary">
        <a class="w-100 h3 d-flex justify-content-around align-items-center" href="{{ route('admin.home') }}">
            <img src="{{ $setting && $setting->logo ? $setting->logo->getUrl() : '' }}" alt="logo" width="50"
                height="50">
            {{ $setting ? $setting->website_name : trans('panel.site_title') }}
        </a>
    </div>

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('admin.home') }}">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">

                </i>
                {{ trans('global.dashboard') }}
            </a>
        </li>
        @can('member_management_access')
        <li
            class="c-sidebar-nav-dropdown {{ request()->is('admin/members*') ? 'c-show' : '' }} {{ request()->is('admin/member-types*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-dropdown-toggle" href="#">
                <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                </i>
                {{ trans('cruds.memberManagement.title') }}
            </a>
            <ul class="c-sidebar-nav-dropdown-items">
                @can('member_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/members") || request()->is('admin/members/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.members.index') }}">
                        <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.member.title') }}
                    </a>
                </li>
                @endcan
                @can('member_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/member-roles") ||
                        request()->is('admin/member-roles/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.member-roles.index') }}">
                        <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                        </i>
                        Member Role
                    </a>
                </li>
                @endcan
                @can('member_type_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/member-types") ||
                        request()->is('admin/member-types/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.member-types.index') }}">
                        <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.memberType.title') }}
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan
        @can('article_management_access')
        <li
            class="c-sidebar-nav-dropdown {{ request()->is('admin/article-categories*') ? 'c-show' : '' }} {{ request()->is('admin/articles*') ? 'c-show' : '' }} {{ request()->is('admin/comments*') ? 'c-show' : '' }} {{ request()->is('admin/article-keywords*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-dropdown-toggle" href="#">
                <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                </i>
                {{ trans('cruds.articleManagement.title') }}
            </a>
            <ul class="c-sidebar-nav-dropdown-items">
                @can('article_category_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('admin/article-categories') || request()->is('admin/article-categories/*') ? 'c-active' : '' }}"
                        href="{{ route('admin.article-categories.index') }}">
                        <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.articleCategory.title') }}
                    </a>
                </li>
                @endcan
                @can('article_category_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('admin/article-sub-categories') || request()->is('admin/article-sub-categories/*')
																												    ? 'c-active'
																												    : '' }}" href="{{ route('admin.article-sub-categories.index') }}">
                        <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                        </i>
                        Article Journal
                    </a>
                </li>
                @endcan
                @can('article_category_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('admin/article-keywords') || request()->is('admin/article-keywords/*') ? 'c-active' : '' }}"
                        href="{{ route('admin.article-keywords.index') }}">
                        <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                        </i>
                        {{ __('Article Keyword') }}
                    </a>
                </li>
                @endcan
                @can('article_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/articles") ||
                        request()->is('admin/articles/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.articles.index') }}">
                        <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.article.title') }}
                    </a>
                </li>
                @endcan
                @can('comment_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/comments") ||
                        request()->is('admin/comments/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.comments.index') }}">
                        <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.comment.title') }}
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->is(" admin/abouts") || request()->is('admin/abouts/*')
                ? 'c-active'
                : '' }}"
                href="{{ route('admin.abouts.index') }}">
                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                </i>
                About Us
            </a>
        </li>
        @can('faq_management_access')
        <li
            class="c-sidebar-nav-dropdown {{ request()->is('admin/faq-categories*') ? 'c-show' : '' }} {{ request()->is('admin/faq-questions*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-dropdown-toggle" href="#">
                <i class="fa-fw fas fa-question c-sidebar-nav-icon">

                </i>
                {{ trans('cruds.faqManagement.title') }}
            </a>
            <ul class="c-sidebar-nav-dropdown-items">
                @can('faq_category_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/faq-categories") ||
                        request()->is('admin/faq-categories/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.faq-categories.index') }}">
                        <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.faqCategory.title') }}
                    </a>
                </li>
                @endcan
                @can('faq_question_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/faq-questions") ||
                        request()->is('admin/faq-questions/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.faq-questions.index') }}">
                        <i class="fa-fw fas fa-question c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.faqQuestion.title') }}
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan
        @can('content_management_access')
        <li
            class="c-sidebar-nav-dropdown {{ request()->is('admin/content-categories*') ? 'c-show' : '' }} {{ request()->is('admin/content-tags*') ? 'c-show' : '' }} {{ request()->is('admin/content-pages*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-dropdown-toggle" href="#">
                <i class="fa-fw fas fa-book c-sidebar-nav-icon">

                </i>
                {{ trans('cruds.contentManagement.title') }}
            </a>
            <ul class="c-sidebar-nav-dropdown-items">
                @can('content_category_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('admin/content-categories') || request()->is('admin/content-categories/*') ? 'c-active' : '' }}"
                        href="{{ route('admin.content-categories.index') }}">
                        <i class="fa-fw fas fa-folder c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.contentCategory.title') }}
                    </a>
                </li>
                @endcan
                @can('content_tag_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/content-tags") ||
                        request()->is('admin/content-tags/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.content-tags.index') }}">
                        <i class="fa-fw fas fa-tags c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.contentTag.title') }}
                    </a>
                </li>
                @endcan
                @can('content_page_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/content-pages") ||
                        request()->is('admin/content-pages/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.content-pages.index') }}">
                        <i class="fa-fw fas fa-file c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.contentPage.title') }}
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan
        @can('user_management_access')
        <li
            class="c-sidebar-nav-dropdown {{ request()->is('admin/permissions*') ? 'c-show' : '' }} {{ request()->is('admin/roles*') ? 'c-show' : '' }} {{ request()->is('admin/users*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-dropdown-toggle" href="#">
                <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                </i>
                {{ trans('cruds.userManagement.title') }}
            </a>
            <ul class="c-sidebar-nav-dropdown-items">
                @can('permission_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/permissions") ||
                        request()->is('admin/permissions/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.permissions.index') }}">
                        <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.permission.title') }}
                    </a>
                </li>
                @endcan
                @can('role_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/roles") || request()->is('admin/roles/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.roles.index') }}">
                        <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.role.title') }}
                    </a>
                </li>
                @endcan
                @can('user_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/users") || request()->is('admin/users/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.user.title') }}
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan
        @can('subscription_management_access')
        <li
            class="c-sidebar-nav-dropdown {{ request()->is('admin/subscriptions*') ? 'c-show' : '' }} {{ request()->is('admin/member-subscriptions*') ? 'c-show' : '' }}">
            <a class="c-sidebar-nav-dropdown-toggle" href="#">
                <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                </i>
                {{ trans('cruds.subscriptionManagement.title') }}
            </a>
            <ul class="c-sidebar-nav-dropdown-items">
                @can('subscription_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is(" admin/subscriptions") ||
                        request()->is('admin/subscriptions/*')
                        ? 'c-active'
                        : '' }}"
                        href="{{ route('admin.subscriptions.index') }}">
                        <i class="fa-fw fas fa-align-justify c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.subscription.title') }}
                    </a>
                </li>
                @endcan
                @can('member_subscription_access')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('admin/member-subscriptions') || request()->is('admin/member-subscriptions/*')
																												    ? 'c-active'
																												    : '' }}" href="{{ route('admin.member-subscriptions.index') }}">
                        <i class="fa-fw fas fa-align-justify c-sidebar-nav-icon">

                        </i>
                        {{ trans('cruds.memberSubscription.title') }}
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan
        @can('country_access')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->is(" admin/countries") || request()->is('admin/countries/*')
                ? 'c-active'
                : '' }}"
                href="{{ route('admin.countries.index') }}">
                <i class="fa-fw fas fa-flag c-sidebar-nav-icon">

                </i>
                {{ trans('cruds.country.title') }}
            </a>
        </li>
        @endcan
        @can('setting_access')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->is('admin/settings') || request()->is('admin/settings/*') ? 'c-active' : '' }}"
                href="{{ route('admin.settings.index') }}">
                <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                </i>
                {{ trans('cruds.setting.title') }}
            </a>
        </li>
        @endcan
        @if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
        @can('profile_password_edit')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'c-active' : '' }}"
                href="{{ route('profile.password.edit') }}">
                <i class="fa-fw fas fa-key c-sidebar-nav-icon">
                </i>
                {{ trans('global.change_password') }}
            </a>
        </li>
        @endcan
        @endif
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="javascript:void(0)"
                onclick="event.preventDefault(); document.getElementById('logoutLink').submit();">
                <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

                </i>
                {{ trans('global.logout') }}
                <form id="logoutLink" action="{{ route('admin.logout') }}" method="post">@csrf</form>
            </a>
        </li>
    </ul>

</div>
