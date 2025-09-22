@extends('afriscribe.layouts.app')

@section('title', @yield('page_title', 'AfriScribe - Request Quote'))

@section('meta_description', @yield('page_description', 'Request a quote for AfriScribe academic publishing services'))

@section('content')
<div class="content-section">
    <!-- Page Header -->
    @yield('form_header')

    <!-- Form Container -->
    <div class="form-container">
        @yield('form_content')
    </div>
</div>
@endsection

@section('custom-styles')
.form-container {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    padding: 3rem;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.form-section {
    margin-bottom: 2rem;
}

.form-section h3 {
    color: #0c1e35;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #0c1e35;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #f9b233;
}

.form-group textarea {
    min-height: 120px;
    resize: vertical;
}

.submit-btn {
    background: #f9b233;
    color: #0c1e35;
    padding: 1rem 2rem;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    transition: all 0.3s ease;
}

.submit-btn:hover {
    background: #e6a029;
    transform: translateY(-2px);
}

.file-upload-area {
    border: 2px dashed #e1e5e9;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.file-upload-area:hover,
.file-upload-area.dragover {
    border-color: #f9b233;
}

.file-upload-area.dragover {
    background: #fff8e1;
}

@yield('form_styles')
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // File upload drag and drop functionality
    const fileUploads = document.querySelectorAll('.file-upload-area');

    fileUploads.forEach(area => {
        const fileInput = area.querySelector('input[type="file"]');

        if (fileInput) {
            area.addEventListener('click', () => fileInput.click());

            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.classList.add('dragover');
            });

            area.addEventListener('dragleave', () => {
                area.classList.remove('dragover');
            });

            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    updateFileName(fileInput, area);
                }
            });

            fileInput.addEventListener('change', () => {
                updateFileName(fileInput, area);
            });
        }
    });

    function updateFileName(input, area) {
        const fileNameDisplay = area.querySelector('.file-name');
        if (fileNameDisplay && input.files.length > 0) {
            fileNameDisplay.textContent = input.files[0].name;
        }
    }

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc3545';
                } else {
                    field.style.borderColor = '#28a745';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
});
</script>

@yield('form_scripts')
@endsection
