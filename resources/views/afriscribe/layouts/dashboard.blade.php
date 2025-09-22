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
            <li><a href="{{ route('afriscribe.dashboard') }}" class="{{ request()->routeIs('afriscribe.dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('afriscribe.manuscripts') }}" class="{{ request()->routeIs('afriscribe.manuscripts*') ? 'active' : '' }}">Manuscripts</a></li>
            <li><a href="{{ route('afriscribe.proofread') }}" class="{{ request()->routeIs('afriscribe.proofread*') ? 'active' : '' }}">Proofreading</a></li>
            <li><a href="{{ route('afriscribe.insights') }}" class="{{ request()->routeIs('afriscribe.insights*') ? 'active' : '' }}">Insights</a></li>
            <li><a href="{{ route('afriscribe.connect') }}" class="{{ request()->routeIs('afriscribe.connect*') ? 'active' : '' }}">Connect</a></li>
            <li><a href="{{ route('afriscribe.archive') }}" class="{{ request()->routeIs('afriscribe.archive*') ? 'active' : '' }}">Archive</a></li>
            <li><a href="{{ route('afriscribe.editor') }}" class="{{ request()->routeIs('afriscribe.editor*') ? 'active' : '' }}">Editor</a></li>
            <li><a href="{{ route('afriscribe.settings') }}" class="{{ request()->routeIs('afriscribe.settings*') ? 'active' : '' }}">Settings</a></li>
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

.dashboard-header h1 {
    margin: 0;
    color: #0c1e35;
}

.dashboard-header p {
    margin: 0.5rem 0 0 0;
    color: #666;
}

@yield('dashboard_styles')
@endsection

@section('scripts')
@yield('dashboard_scripts')
@endsection
