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

    // Form validation and submission
    const form = document.getElementById('proofreading-interest-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;

            // Get form data
            const formData = new FormData(form);

            // Submit form via AJAX
            fetch('{{ route("afriscribe.request") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showNotification('Thank you for your interest! Our team will contact you within 24 hours.', 'success');
                    form.reset();
                } else {
                    // Show error message
                    showNotification(data.message || 'An error occurred. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            z-index: 10000;
            ${type === 'success' ? 'background: #28a745;' : 'background: #dc3545;'}
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
});
</script>
@endsection
