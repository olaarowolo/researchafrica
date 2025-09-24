@extends('afriscribe.layouts.app')

@section('title', @yield('page_title', 'AfriScribe Dashboard'))

@section('meta_description', @yield('page_description', 'AfriScribe Admin Dashboard - Manage your academic publishing workflow'))

@section('content')
<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="logo">
            <img src="{{ asset('afriscribe/img/afriscribe-logo-white.png') }}" alt="AfriScribe Logo">
        </div>
        <ul>
            <li><a href="{{ route('afriscribe.dashboard') }}" class="{{ request()->routeIs('afriscribe.dashboard') ? 'active' : '' }}"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="{{ route('afriscribe.manuscripts') }}" class="{{ request()->routeIs('afriscribe.manuscripts*') ? 'active' : '' }}"><span class="icon">📝</span> Manuscripts</a></li>
            <li><a href="{{ route('afriscribe.proofread') }}" class="{{ request()->routeIs('afriscribe.proofread*') ? 'active' : '' }}"><span class="icon">✏️</span> Proofreading</a></li>
            <li><a href="{{ route('afriscribe.insights') }}" class="{{ request()->routeIs('afriscribe.insights*') ? 'active' : '' }}"><span class="icon">📊</span> Insights</a></li>
            <li><a href="{{ route('afriscribe.connect') }}" class="{{ request()->routeIs('afriscribe.connect*') ? 'active' : '' }}"><span class="icon">🤝</span> Connect</a></li>
            <li><a href="{{ route('afriscribe.archive') }}" class="{{ request()->routeIs('afriscribe.archive*') ? 'active' : '' }}"><span class="icon">📚</span> Archive</a></li>
            <li><a href="{{ route('afriscribe.editor') }}" class="{{ request()->routeIs('afriscribe.editor*') ? 'active' : '' }}"><span class="icon">✂️</span> Editor</a></li>
            <li><a href="{{ route('afriscribe.settings') }}" class="{{ request()->routeIs('afriscribe.settings*') ? 'active' : '' }}"><span class="icon">⚙️</span> Settings</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1>@yield('dashboard_title', 'Dashboard')</h1>
            <p>@yield('dashboard_subtitle', 'Manage your AfriScribe platform')</p>
        </div>

        <!-- Page Content -->
        @yield('dashboard_content')
    </main>
</div>
@endsection

@section('custom-styles')
.dashboard-container {
    display: flex;
    min-height: 100vh;
}

.dashboard-sidebar {
    width: 250px;
    background: linear-gradient(180deg, #0c1e35, #1a2e47);
    color: #fff;
    padding: 2rem 0;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.dashboard-sidebar .logo {
    text-align: center;
    margin-bottom: 2rem;
}

.dashboard-sidebar .logo img {
    height: 50px;
    width: auto;
    border-radius: 10px;
}

.dashboard-sidebar ul {
    list-style: none;
    padding: 0;
}

.dashboard-sidebar ul li {
    margin-bottom: 0.5rem;
}

.dashboard-sidebar ul li a {
    display: flex;
    align-items: center;
    padding: 1rem 2rem;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.dashboard-sidebar ul li a .icon {
    margin-right: 1rem;
    font-size: 1.2rem;
}

.dashboard-sidebar ul li a:hover,
.dashboard-sidebar ul li a.active {
    background: #f9b233;
    color: #0c1e35;
    border-left-color: #f9b233;
    transform: translateX(5px);
}

.dashboard-main {
    flex: 1;
    margin-left: 250px;
    padding: 2rem;
    background: #f8f9fa;
}

.dashboard-header {
    background: linear-gradient(135deg, #fff, #e9ecef);
    padding: 1.5rem 2rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    border-radius: 10px;
}

.dashboard-header h1 {
    margin: 0;
    color: #0c1e35;
    font-size: 2rem;
    font-weight: 700;
}

.dashboard-header p {
    margin: 0.5rem 0 0 0;
    color: #666;
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .dashboard-sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .dashboard-sidebar.open {
        transform: translateX(0);
    }

    .dashboard-main {
        margin-left: 0;
    }

    .dashboard-header {
        padding: 1rem;
    }
}

@yield('dashboard_styles')
@endsection

@section('scripts')
@yield('dashboard_scripts')
@endsection
