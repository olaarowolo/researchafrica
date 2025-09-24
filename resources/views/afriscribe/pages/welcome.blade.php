@extends('afriscribe.layouts.landing')

@section('page_title', 'AfriScribe - Powering Africa\'s Academic Publishing')

@section('page_description', 'Integrated solutions for manuscripts, proofreading, analytics, and beyond. Streamline your academic publishing workflow with AfriScribe\'s comprehensive platform designed for African academic institutions.')

@section('hero')
    @include('afriscribe.partials.as-hero')
@endsection

@section('services_section')
    @include('afriscribe.partials.as-services')
@endsection

@section('features_section')
    @include('afriscribe.partials.as-features')
@endsection

@section('cta_section')
    @include('afriscribe.partials.as-cta')
@endsection

@section('dashboard_section')
    @include('afriscribe.partials.as-dashboard')
@endsection

@section('page_scripts')
<script>
// Additional scripts specific to the welcome page
document.addEventListener('DOMContentLoaded', function() {
    // Add any welcome page specific JavaScript here
    console.log('Welcome page loaded');
});
</script>
@endsection
