<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        line-height: 1.6;
        background: #f8f9fb;
        color: #333;
    }

    /* Navbar */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        background: #0c1e35;
        color: #fff;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .navbar .logo {
        font-size: 1.5rem;
        font-weight: bold;
        color: #fff;
        text-decoration: none;
    }

    .navbar .logo img {
        height: 60px;
        width: auto;
    }

    .navbar ul {
        list-style: none;
        display: flex;
        gap: 1.5rem;
    }

    .navbar ul li a {
        color: #fff;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .navbar ul li a:hover {
        color: #f9b233;
    }

    .hamburger {
        display: none;
        flex-direction: column;
        cursor: pointer;
        gap: 5px;
    }

    .hamburger span {
        width: 25px;
        height: 3px;
        background: #fff;
        border-radius: 2px;
        transition: 0.3s;
    }

    /* Mobile Menu */
    .nav-links {
        display: flex;
    }

    .nav-links.active {
        display: block;
        position: absolute;
        top: 60px;
        right: 0;
        background: #0c1e35;
        width: 200px;
        padding: 1rem;
        flex-direction: column;
    }

    .nav-links.active li {
        margin-bottom: 1rem;
    }

    /* Hero */
    .hero {
        text-align: center;
        padding: 6rem 2rem 4rem;
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
            url('https://www.star-vietnam.com.vn/Data/Sites/1/News/31/glenn-carstens-peters-npxxwgq33zq-unsplash.jpg') no-repeat center/cover;
        color: #fff;
        min-height: 60vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .hero p {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .hero p {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .btn {
        background: #f9b233;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 25px;
        color: #000;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn:hover {
        background: #e6a029;
        transform: translateY(-2px);
    }

    /* Services Section */
    .services {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        padding: 4rem 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .card {
        background: #fff;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        text-align: center;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card h3 {
        margin-bottom: 1rem;
        color: #0c1e35;
        font-size: 1.5rem;
    }

    .card p {
        color: #666;
        line-height: 1.6;
    }

    .card-icon {
        width: 60px;
        height: 60px;
        background: #f9b233;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
        color: #0c1e35;
    }

    /* Features Section */
    .features {
        background: #0c1e35;
        color: #fff;
        padding: 4rem 2rem;
        text-align: center;
    }

    .features h2 {
        font-size: 2.5rem;
        margin-bottom: 2rem;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        max-width: 1000px;
        margin: 0 auto;
    }

    .feature-item {
        padding: 1rem;
    }

    .feature-item h4 {
        margin-bottom: 0.5rem;
        color: #f9b233;
    }

    /* CTA Section */
    .cta {
        background: linear-gradient(135deg, #f9b233, #e6a029);
        color: #0c1e35;
        padding: 4rem 2rem;
        text-align: center;
    }

    .cta h2 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .cta p {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .cta-btn {
        background: #0c1e35;
        color: #fff;
        padding: 1rem 2rem;
        border: none;
        border-radius: 25px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .cta-btn:hover {
        background: #1a3a5c;
        transform: translateY(-2px);
    }

    /* Footer */
    footer {
        background: #0c1e35;
        color: #fff;
        text-align: center;
        padding: 2rem;
    }

    footer p {
        margin: 0;
    }

    /* Responsive */
    @media(max-width: 768px) {
        .navbar ul {
            display: none;
            flex-direction: column;
        }

        .hamburger {
            display: flex;
        }

        .hero h1 {
            font-size: 2rem;
        }

        .hero p {
            font-size: 1.1rem;
        }

        .services {
            grid-template-columns: 1fr;
            padding: 2rem 1rem;
        }

        .features h2 {
            font-size: 2rem;
        }

        .cta h2 {
            font-size: 2rem;
        }
    }

    /* Form Section */

    .form-container {
        max-width: 700px;
        margin: 0 auto;
        background: #f8f9fb;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .form-container h2,
    .form-container h3 {
        margin-bottom: 1.5rem;
        text-align: center;
        color: #0c1e35;
    }

    .form-group {
        margin-bottom: 1.2rem;
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    input,
    textarea,
    select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1rem;
    }

    input[type="file"] {
        border: none;
        background: #fff;
    }

    .addons {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .total {
        font-size: 1.2rem;
        font-weight: bold;
        margin-top: 1rem;
        color: #0c1e35;
    }

    button[type="submit"] {
        background: #0c1e35;
        color: #fff;
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        width: 100%;
        transition: background 0.3s;
    }

    button[type="submit"]:hover {
        background: #09284a;
    }

    .quote-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .field {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .field.half {
        flex: 1 1 calc(50% - 0.5rem);
    }

    .field label {
        font-weight: 500;
        margin-bottom: 0.4rem;
        color: #0c1e35;
    }

    /* Checkbox alignment */
    .field input[type="checkbox"] {
        width: auto;
        margin: 0;
    }

    .field label[for^="addon"] {
        font-weight: normal;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    /* Dropzone */
    .drop {
        border: 2px dashed #0c1e35;
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        background: #fafbfc;
        cursor: pointer;
        transition: background 0.3s, border-color 0.3s;
    }

    .drop:hover {
        background: #f1f4f8;
    }

    .drop.dragover {
        border-color: #f9b233;
        background: #fff8ec;
    }

    .drop #dropText {
        font-size: 0.95rem;
        color: #555;
    }

    /* Cost box */
    .cost {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f9f9fb;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .cost .note {
        font-size: 0.8rem;
        color: #666;
    }

    .cost .currency {
        font-size: 1.2rem;
        font-weight: bold;
        color: #0c1e35;
    }

    .small.muted {
        font-size: 13px;
        color: #6b7280;
    }

    .btn.secondary {
        background: #fff;
        color: #0f172a;
        border: 1px solid #e6e9ef;
        font-weight: 600;
    }
</style>


<!-- Form Section -->
<section id="request" class="form-section">
    <div class="form-container">
        <h3 id="quote-form-title" style="margin:0 0 8px; text-align:left;">Request a Quote</h3>
        <p class="small muted" style="margin:0 0 12px">Download the rate card first, then submit your document for an
            email-only quote.</p>



        <form id="quoteForm" class="quote-form" method="post" action="/afriscribe/request"
            enctype="multipart/form-data" novalidate>
            <!-- NOTE: set action to your backend endpoint which will accept multipart POST and email attachments -->
            <!-- CSRF token needed if integrated into Laravel -->
            <div class="row">
                <div class="field half">
                    <label for="fullName">Full name</label>
                    <input id="fullName" name="name" type="text" placeholder="Dr. A. Olu" required />
                </div>
                <div class="field half">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" placeholder="you@example.com" required />
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label for="raService">Research Africa service</label>
                    <select id="raService" name="ra_service" aria-label="Research Africa service">
                        <option value="afriscribe" selected>AfriScribe</option>
                        <option value="other">Other services</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="field half">
                    <label for="product">AfriScribe product</label>
                    <select id="product" name="product" required>
                        <option value="proofread" selected>AfriScribe Proofread</option>
                        <option value="manuscripts">AfriScribe Manuscripts</option>
                        <option value="insights">AfriScribe Insights</option>
                        <option value="connect">AfriScribe Connect</option>
                        <option value="archive">AfriScribe Archive</option>
                        <option value="editor">AfriScribe Editor</option>
                    </select>
                </div>

                <div class="field half">
                    <label for="location">Location</label>
                    <select id="locationField" name="location" required>
                        <option value="">Select location</option>
                        <option value="UK">United Kingdom</option>
                        <option value="Nigeria">Nigeria</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="field half">
                    <label for="serviceType">Service type</label>
                    <select id="serviceType" name="service" required>
                        <option value="">Select service</option>
                        <!-- populated by JS -->
                    </select>
                </div>

                <div class="field half">
                    <label for="wordCount">Word count (approx.)</label>
                    <input id="wordCount" name="word_count" type="number" min="100" step="1"
                        placeholder="e.g. 4500" />
                    <div class="small muted">Rates apply per 1,000 words; we round up to the next 1,000.</div>
                </div>
            </div>

            <div class="row" style="margin-top:8px">
                <div class="field">
                    <label for="addons">Add-ons</label>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <label style="display:inline-flex;gap:8px;align-items:center"><input type="checkbox"
                                id="addon_rush" name="addons[]" value="rush"> Rush (48h)</label>
                        <label style="display:inline-flex;gap:8px;align-items:center"><input type="checkbox"
                                id="addon_plag" name="addons[]" value="plag"> Plagiarism check</label>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:10px">
                <div class="field">
                    <label for="referral">Referral code (optional)</label>
                    <input id="referral" name="referral" placeholder="Enter referral code" type="text" />
                </div>
            </div>

            <!-- File drop -->
            <div class="row" style="margin-top:10px">
                <div class="field">
                    <label for="fileInput">Upload document</label>
                    <div id="drop" class="drop" tabindex="0" aria-label="File upload dropzone">
                        <div id="dropText">Drag & drop file here, or click to browse (DOC, DOCX, PDF, TXT — max 10MB)
                        </div>
                        <input id="fileInput" name="file" type="file" accept=".doc,.docx,.pdf,.txt"
                            style="display:none" required>
                    </div>
                    <div id="fileHint" class="small muted" style="margin-top:8px">Your file will be attached to the
                        admin email only.</div>
                </div>
            </div>

            <!-- details -->
            <div class="row" style="margin-top:12px">
                <div class="field">
                    <label for="details">Project details (optional)</label>
                    <textarea id="details" name="details" placeholder="Journal target, referencing style, deadline notes..."></textarea>
                </div>
            </div>

            <!-- cost preview -->
            <div id="costBox" class="cost" role="status" aria-live="polite" style="display:none">
                <div>
                    <div class="note">Estimated cost</div>
                    <div class="currency" id="costValue">£0</div>
                </div>
                <div>
                    <div class="note">Turnaround</div>
                    <div id="turnaroundText">N/A</div>
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:12px;align-items:center">
                <button type="button" class="btn" id="calcBtn">Calculate & Preview</button>
                <button type="submit" class="btn" id="submitBtn" style="opacity:0.95">Submit request</button>
            </div>
            <div style="display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; justify-content:center;">
                <a class="btn secondary" href="{{ asset('files/AfriScribe-UK-RateCard.pdf') }}" target="_blank"
                    rel="noopener">
                    Download UK rate card
                </a>
                <a class="btn secondary" href="{{ asset('files/Rate Card-AfriScribe-Proofreading-Service-NG.pdf') }}"
                    target="_blank" rel="noopener">
                    Download Nigeria rate card
                </a>
            </div>
            <p class="small muted" style="margin-top:10px; text-align:center;">
                We will email you directly with the confirmed quote. <br>
                Note: large files may be requested via secure link if attachment exceeds mail limits.
            </p>
        </form>


    </div>

</section>


<script>
    function toggleMenu() {
        document.querySelector('.nav-links').classList.toggle('active');
    }

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

    /* -------------------------
       Pricing configuration
       All rates in per-1000 words or flat amounts
       ------------------------- */
    const PRICING = {
        UK: {
            unit: 'GBP',
            per1000: {
                "Student-Friendly Proofreading": 15,
                "Research Editing": 25,
                "Publication-Ready Academic Edit": 40
            },
            packages: {
                "Basic Scholar Package": 120,
                "Researcher’s Advantage Package": 220,
                "Premium Publication Package": 350
            },
            rushFee: 150,
            extraPer1000: 15,
            turnaround: {
                'Student-Friendly Proofreading': '3–5 business days',
                'Research Editing': '3–4 business days',
                'Publication-Ready Academic Edit': '2–3 business days'
            }
        },
        Nigeria: {
            unit: 'NGN',
            per1000: {
                "Student-Friendly Proofreading": 2000,
                "Research Editing": 3500,
                "Publication-Ready Academic Edit": 5000
            },
            packages: {
                "Basic Scholar Package": 20000,
                "Researcher’s Advantage Package": 35000,
                "Premium Publication Package": 50000
            },
            rushFee: 10000,
            extraPer1000: 2000,
            turnaround: {
                'Student-Friendly Proofreading': '3–5 business days',
                'Research Editing': '3–4 business days',
                'Publication-Ready Academic Edit': '2–3 business days'
            }
        }
    };

    /* UI elements */
    const locationField = document.getElementById('locationField');
    const serviceType = document.getElementById('serviceType');
    const wordCount = document.getElementById('wordCount');
    const calcBtn = document.getElementById('calcBtn');
    const costBox = document.getElementById('costBox');
    const costValue = document.getElementById('costValue');
    const turnaroundText = document.getElementById('turnaroundText');
    const addonRush = document.getElementById('addon_rush');
    const addonPlag = document.getElementById('addon_plag');
    const product = document.getElementById('product');

    // Populate service list based on location
    function populateServices(loc) {
        serviceType.innerHTML = '<option value="">Select service</option>';
        if (!loc) return;
        const p = PRICING[loc];
        // per-1000 services
        Object.keys(p.per1000).forEach(k => {
            const opt = document.createElement('option');
            opt.value = k;
            opt.textContent =
                `${k} — ${loc === 'UK' ? '£' : '₦'}${p.per1000[k].toLocaleString()} per 1,000 words`;
            serviceType.appendChild(opt);
        });
        // packages
        Object.keys(p.packages).forEach(k => {
            const opt = document.createElement('option');
            opt.value = k;
            opt.textContent = `${k} — ${loc === 'UK' ? '£' : '₦'}${p.packages[k].toLocaleString()} (flat)`;
            serviceType.appendChild(opt);
        });
        // phd special
        const phd = document.createElement('option');
        phd.value = 'PhD Thesis Premium Package';
        phd.textContent = 'PhD Thesis Premium Package — Custom quoted (up to 100,000 words)';
        serviceType.appendChild(phd);
    }

    // event: when location changes, update service list
    locationField.addEventListener('change', (e) => {
        populateServices(e.target.value);
        costBox.style.display = 'none';
    });

    // drag & drop upload
    const drop = document.getElementById('drop');
    const fileInput = document.getElementById('fileInput');
    const dropText = document.getElementById('dropText');
    drop.addEventListener('click', () => fileInput.click());
    drop.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') fileInput.click();
    });

    drop.addEventListener('dragover', (e) => {
        e.preventDefault();
        drop.classList.add('dragover');
    });
    drop.addEventListener('dragleave', () => drop.classList.remove('dragover'));
    drop.addEventListener('drop', (e) => {
        e.preventDefault();
        drop.classList.remove('dragover');
        const f = e.dataTransfer.files && e.dataTransfer.files[0];
        if (f) setFile(f);
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files[0]) setFile(e.target.files[0]);
    });

    function setFile(file) {
        const maxBytes = 10 * 1024 * 1024; // 10MB
        if (file.size > maxBytes) {
            alert('File exceeds 10MB limit. Please use the rate card link to request a secure upload for large files.');
            fileInput.value = '';
            dropText.textContent = 'File too large — choose a smaller file';
            return;
        }
        dropText.textContent = `Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
    }

    // calculate cost (same logic must be re-run server-side)
    function calculate() {
        const loc = locationField.value;
        const service = serviceType.value;
        const words = parseInt(wordCount.value) || 0;
        if (!loc || !service) {
            alert('Please select a location and service first.');
            return null;
        }
        const pricing = PRICING[loc];
        let cost = 0;
        let currencySymbol = loc === 'UK' ? '£' : '₦';
        let turnaround = 'TBD';

        // per-1000 services
        if (pricing.per1000[service]) {
            const unitRate = pricing.per1000[service];
            const units = Math.max(1, Math.ceil(words / 1000));
            cost = unitRate * units;
            turnaround = pricing.turnaround[service] || 'Varies';
        }
        // packages (flat)
        else if (pricing.packages[service]) {
            cost = pricing.packages[service];
            turnaround = 'See package details';
        }
        // phd special
        else if (service === 'PhD Thesis Premium Package') {
            cost = null; // custom
            turnaround = '10–14 business days (custom quote)';
        } else {
            // unknown fallback
            cost = null;
        }

        // add-ons
        if (addonRush && addonRush.checked) {
            cost = (cost !== null) ? cost + pricing.rushFee : null;
            if (turnaround && turnaround !== 'TBD') turnaround = 'Rush (48h)';
        }
        if (addonPlag && addonPlag.checked) {
            // Plagiarism fixed fee: Nigeria flat ₦5,000, UK approximate £0? we will set local: use NGN only on NG clients
            if (loc === 'Nigeria') {
                cost = (cost !== null) ? cost + 5000 : null;
            } else {
                /* UK: if you offer Turnitin for UK clients, add fee here */ }
        }

        return {
            cost,
            currencySymbol,
            turnaround
        };
    }

    // attach calculation to button and preview
    calcBtn.addEventListener('click', (ev) => {
        ev.preventDefault();
        const result = calculate();
        if (!result) return;
        const {
            cost,
            currencySymbol,
            turnaround
        } = result;
        if (cost === null) {
            costBox.style.display = 'flex';
            costValue.textContent = 'Custom quote required';
            turnaroundText.textContent = turnaround;
        } else {
            costBox.style.display = 'flex';
            costValue.textContent = `${currencySymbol}${Number(cost).toLocaleString()}`;
            turnaroundText.textContent = turnaround;
        }
        // set aria-live update
        costBox.setAttribute('aria-hidden', 'false');
    });

    // On submit: re-calc server-validatable data and confirm
    const form = document.getElementById('quoteForm');
    form.addEventListener('submit', function(e) {
        // front-end check
        const res = calculate();
        if (!res) {
            e.preventDefault();
            return;
        }
        // Ensure file chosen
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Please attach your document before submitting.');
            return;
        }
        // Optionally: set hidden fields for the server with calculated cost for record
        // If you want to include estimated cost in the submission, create hidden inputs here:
        // example:
        let existing = document.getElementById('estimated_cost_hidden');
        if (!existing) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'estimated_cost';
            input.id = 'estimated_cost_hidden';
            form.appendChild(input);
        }
        document.getElementById('estimated_cost_hidden').value = res.cost === null ? '' : res.cost;

        // final confirmation — keep brief
        // Note: do not rely on client-side confirmation for authoritative pricing.
        // Server should recalculate identically before sending the admin email/invoice.
    });

    // progressive enhancement: if js loads, pre-populate services for a default location
    (function init() {
        // optional: choose default location if you have analytics to pick region automatically
        // locationField.value = 'UK';
        // populateServices('UK');
    })();
</script>
