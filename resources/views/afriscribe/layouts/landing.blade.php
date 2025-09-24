@extends('afriscribe.layouts.app')

@section('title')
    @yield('page_title', 'AfriScribe - Academic Publishing Platform')
@endsection

@section('meta_description')
    @yield('page_description', 'Integrated solutions for manuscripts, proofreading, analytics, and beyond. Streamline your academic publishing workflow with AfriScribe.')
@endsection

@section('content')
    <!-- Hero Section -->
    @yield('hero', @include('afriscribe.partials.as-hero'))

    <!-- Services Section -->
    @yield('services_section', @include('afriscribe.partials.as-services'))

    <!-- Features Section -->
    @yield('features_section', @include('afriscribe.partials.as-features'))

    <!-- Custom sections can be yielded here -->
    @yield('custom_sections')

    <!-- Dashboard Section -->
    @yield('dashboard_section')

    <!-- CTA Section -->
    @yield('cta_section', @include('afriscribe.partials.as-cta'))

    <!-- Optional form section -->
    @yield('form_section')
@endsection

@section('custom-styles')
    @yield('page_styles')
@endsection

@section('scripts')
    @yield('page_scripts')
@endsection
