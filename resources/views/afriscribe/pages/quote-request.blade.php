@extends('afriscribe.layouts.form')

@section('page_title', 'Request a Quote - AfriScribe')

@section('page_description', 'Get a custom quote for AfriScribe academic publishing services')

@section('form_header')
<div style="text-align: center; margin-bottom: 3rem;">
    <h1>Request a Quote</h1>
    <p>Get a custom quote for your academic publishing needs. Our team will review your requirements and provide a tailored solution.</p>
</div>
@endsection

@section('form_content')
<form action="{{ route('afriscribe.quote-request.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-section">
        <h3>📝 Project Details</h3>

        <div class="form-group">
            <label for="service_type">Service Type *</label>
            <select name="service_type" id="service_type" required>
                <option value="">Select a service</option>
                <option value="manuscripts">AfriScribe Manuscripts</option>
                <option value="proofread">AfriScribe Proofread</option>
                <option value="insights">AfriScribe Insights</option>
                <option value="connect">AfriScribe Connect</option>
                <option value="archive">AfriScribe Archive</option>
                <option value="editor">AfriScribe Editor</option>
                <option value="full_platform">Full Platform Access</option>
            </select>
        </div>

        <div class="form-group">
            <label for="project_type">Project Type *</label>
            <select name="project_type" id="project_type" required>
                <option value="">Select project type</option>
                <option value="journal_setup">New Journal Setup</option>
                <option value="manuscript_processing">Manuscript Processing</option>
                <option value="proofreading_service">Proofreading Service</option>
                <option value="platform_migration">Platform Migration</option>
                <option value="training">Staff Training</option>
                <option value="consultation">Consultation</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="message">Project Description *</label>
            <textarea name="message" id="message" placeholder="Please describe your project requirements, timeline, and any specific needs..." required></textarea>
        </div>
    </div>

    <div class="form-section">
        <h3>👤 Contact Information</h3>

        <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" name="phone" id="phone">
        </div>

        <div class="form-group">
            <label for="organization">Organization/Institution *</label>
            <input type="text" name="organization" id="organization" required>
        </div>

        <div class="form-group">
            <label for="country">Country *</label>
            <select name="country" id="country" required>
                <option value="">Select your country</option>
                <option value="nigeria">Nigeria</option>
                <option value="south_africa">South Africa</option>
                <option value="kenya">Kenya</option>
                <option value="ghana">Ghana</option>
                <option value="egypt">Egypt</option>
                <option value="other">Other</option>
            </select>
        </div>
    </div>

    <div class="form-section">
        <h3>📎 Additional Information</h3>

        <div class="form-group">
            <label for="budget_range">Budget Range</label>
            <select name="budget_range" id="budget_range">
                <option value="">Select budget range</option>
                <option value="under_1000">Under $1,000</option>
                <option value="1000_5000">$1,000 - $5,000</option>
                <option value="5000_10000">$5,000 - $10,000</option>
                <option value="10000_25000">$10,000 - $25,000</option>
                <option value="over_25000">Over $25,000</option>
                <option value="discuss">Let's discuss</option>
            </select>
        </div>

        <div class="form-group">
            <label for="timeline">Preferred Timeline</label>
            <select name="timeline" id="timeline">
                <option value="">Select timeline</option>
                <option value="asap">ASAP</option>
                <option value="1_month">Within 1 month</option>
                <option value="3_months">Within 3 months</option>
                <option value="6_months">Within 6 months</option>
                <option value="flexible">Flexible</option>
            </select>
        </div>

        <div class="form-group">
            <label for="document">Upload Document (Optional)</label>
            <div class="file-upload-area" onclick="document.getElementById('document').click()">
                <div>
                    <span style="font-size: 2rem;">📄</span>
                    <p>Click to upload or drag and drop</p>
                    <p style="font-size: 0.9rem; color: #666;">PDF, DOC, DOCX (max 10MB)</p>
                    <span class="file-name" style="display: block; margin-top: 1rem; font-style: italic;"></span>
                </div>
            </div>
            <input type="file" name="document" id="document" style="display: none;" accept=".pdf,.doc,.docx">
        </div>
    </div>

    <button type="submit" class="submit-btn">Submit Quote Request</button>
</form>
@endsection

@section('form_scripts')
<script>
// Form-specific scripts
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const fileInput = document.getElementById('document');
    const fileUploadArea = document.querySelector('.file-upload-area');
    const fileNameDisplay = document.querySelector('.file-name');

    // File upload handling
    if (fileInput && fileUploadArea) {
        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        fileUploadArea.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });

        fileUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateFileName();
            }
        });

        fileInput.addEventListener('change', updateFileName);

        function updateFileName() {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
            } else {
                fileNameDisplay.textContent = '';
            }
        }
    }

    // Form validation enhancement
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc3545';

                    // Add error message if not exists
                    let errorMsg = field.parentNode.querySelector('.error-message');
                    if (!errorMsg) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.style.color = '#dc3545';
                        errorMsg.style.fontSize = '0.9rem';
                        errorMsg.style.marginTop = '0.5rem';
                        errorMsg.textContent = 'This field is required';
                        field.parentNode.appendChild(errorMsg);
                    }
                } else {
                    field.style.borderColor = '#28a745';

                    // Remove error message if exists
                    const errorMsg = field.parentNode.querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = form.querySelector('.error-message');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
});
</script>
@endsection
