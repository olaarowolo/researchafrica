@extends('afriscribe.layouts.landing')

@section('page_title', 'AfriScribe Proofreading - Professional Academic Editing Services')

@section('page_description', 'Expert academic proofreading and editing services for researchers in Africa. Ensure your manuscript is publication-ready with our language, style, and formatting checks.')

@section('hero')
    @include('afriscribe.partials.as-hero-proofreading')
@endsection

@section('custom_sections')
    @include('afriscribe.partials.as-proofreading-overview')
    @include('afriscribe.partials.as-proofreading-pricing')
@endsection

@section('cta_section')
    @include('afriscribe.partials.as-proofreading-cta')
@endsection

@section('form_section')
    @include('afriscribe.partials.as-proofreading-form')
@endsection

@section('page_scripts')
<script>
// Additional scripts specific to the proofreading page
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for anchor links
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
@endsection
