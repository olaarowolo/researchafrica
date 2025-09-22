// AfriScribe Form JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Pricing data
    const pricingData = {
        UK: {
            proofread: {
                standard: { rate: 0.03, turnaround: '5-7 days' },
                express: { rate: 0.05, turnaround: '2-3 days' },
                rush: { rate: 0.08, turnaround: '24-48 hours' }
            }
        },
        Nigeria: {
            proofread: {
                standard: { rate: 12000, turnaround: '5-7 days' },
                express: { rate: 20000, turnaround: '2-3 days' },
                rush: { rate: 32000, turnaround: '24-48 hours' }
            }
        }
    };

    // CSRF token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // DOM elements
    const locationField = document.getElementById('locationField');
    const serviceTypeField = document.getElementById('serviceType');
    const wordCountField = document.getElementById('wordCount');
    const calcBtn = document.getElementById('calcBtn');
    const costBox = document.getElementById('costBox');
    const costValue = document.getElementById('costValue');
    const turnaroundText = document.getElementById('turnaroundText');
    const dropZone = document.getElementById('drop');
    const fileInput = document.getElementById('fileInput');
    const dropText = document.getElementById('dropText');
    const quoteForm = document.getElementById('quoteForm');

    // Initialize form if all elements exist
    if (locationField && serviceTypeField && wordCountField && calcBtn && costBox && costValue && turnaroundText && dropZone && fileInput && dropText && quoteForm) {
        initializeForm();
    }

    function initializeForm() {
        // Populate service types based on location
        locationField.addEventListener('change', function() {
            const location = this.value;
            serviceTypeField.innerHTML = '<option value="" disabled selected hidden>Select service</option>';

            if (location && pricingData[location]) {
                Object.keys(pricingData[location].proofread).forEach(service => {
                    const option = document.createElement('option');
                    option.value = service;
                    option.textContent = service.charAt(0).toUpperCase() + service.slice(1);
                    serviceTypeField.appendChild(option);
                });
            }
        });

        // Calculate cost
        calcBtn.addEventListener('click', function() {
            const location = locationField.value;
            const serviceType = serviceTypeField.value;
            const wordCount = parseInt(wordCountField.value);

            if (!location || !serviceType || !wordCount) {
                alert('Please fill in all required fields');
                return;
            }

            const pricing = pricingData[location].proofread[serviceType];
            const roundedWordCount = Math.ceil(wordCount / 1000) * 1000;
            let cost;

            if (location === 'UK') {
                cost = (roundedWordCount / 1000) * pricing.rate;
                costValue.textContent = '£' + cost.toFixed(2);
            } else {
                cost = (roundedWordCount / 1000) * pricing.rate;
                costValue.textContent = '₦' + cost.toLocaleString();
            }

            turnaroundText.textContent = pricing.turnaround;
            costBox.style.display = 'flex';
        });

        // File upload handling
        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateDropText(files[0].name);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                updateDropText(e.target.files[0].name);
            }
        });

        function updateDropText(filename) {
            dropText.textContent = `Selected: ${filename}`;
        }

        // Form submission
        quoteForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert('Your request has been submitted successfully! Please check your email for confirmation.');
                    quoteForm.reset();
                    costBox.style.display = 'none';
                    dropText.textContent = 'Drag & drop file here, or click to browse (DOC, DOCX, PDF, TXT — max 10MB)';
                } else {
                    alert('Error: ' + (result.message || 'An error occurred'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while submitting your request. Please try again.');
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // Mobile menu toggle
    window.toggleMenu = function() {
        const navLinks = document.querySelector('.nav-links');
        if (navLinks) {
            navLinks.classList.toggle('active');
        }
    };
});
