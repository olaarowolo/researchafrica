<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AfriScribe | Research Africa</title>
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
            margin-top: 2rem;
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

        .form-container h2, .form-container h3 {
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

        .small.muted { font-size:13px; color:#6b7280; }
        .btn.secondary {
          background:#fff;
          color:#0f172a;
          border:1px solid #e6e9ef;
          font-weight:600;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
@include('afriscribe.partials.as-nav')

@include('afriscribe.partials.as-pr-form')


    <!-- Features Section -->
    <section id="features" class="features">
        <h2>Why Choose AfriScribe?</h2>
        <div class="features-grid">
            <div class="feature-item">
                <h4>🔒 Secure & Compliant</h4>
                <p>Enterprise-grade security with full compliance to academic publishing standards.</p>
            </div>
            <div class="feature-item">
                <h4>🌍 Africa-Focused</h4>
                <p>Designed specifically for African academic institutions and publishing needs.</p>
            </div>
            <div class="feature-item">
                <h4>⚡ Fast & Efficient</h4>
                <p>Streamlined workflows that reduce time-to-publication by up to 60%.</p>
            </div>
            <div class="feature-item">
                <h4>💰 Cost-Effective</h4>
                <p>Affordable pricing models designed for academic institutions and researchers.</p>
            </div>
            <div class="feature-item">
                <h4>📱 Mobile-Friendly</h4>
                <p>Access your manuscripts and reviews anywhere, on any device.</p>
            </div>
            <div class="feature-item">
                <h4>🎯 User-Friendly</h4>
                <p>Intuitive interface designed by academics, for academics.</p>
            </div>
        </div>
    </section>


    <!-- Footer -->
@include('afriscribe.partials.as-footer')

    <script>
        function toggleMenu() {
            document.querySelector('.nav-links').classList.toggle('active');
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
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
            turnaround: { 'Student-Friendly Proofreading': '3–5 business days', 'Research Editing': '3–4 business days', 'Publication-Ready Academic Edit': '2–3 business days' }
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
            turnaround: { 'Student-Friendly Proofreading': '3–5 business days', 'Research Editing': '3–4 business days', 'Publication-Ready Academic Edit': '2–3 business days' }
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
            opt.textContent = `${k} — ${loc === 'UK' ? '£' : '₦'}${p.per1000[k].toLocaleString()} per 1,000 words`;
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
        drop.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') fileInput.click(); });

        drop.addEventListener('dragover', (e) => { e.preventDefault(); drop.classList.add('dragover'); });
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
            if (loc === 'Nigeria') { cost = (cost !== null) ? cost + 5000 : null; }
            else { /* UK: if you offer Turnitin for UK clients, add fee here */ }
          }

          return { cost, currencySymbol, turnaround };
        }

        // attach calculation to button and preview
        calcBtn.addEventListener('click', (ev) => {
          ev.preventDefault();
          const result = calculate();
          if (!result) return;
          const { cost, currencySymbol, turnaround } = result;
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
        form.addEventListener('submit', function(e){
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
        (function init(){
          // optional: choose default location if you have analytics to pick region automatically
          // locationField.value = 'UK';
          // populateServices('UK');
            // Pre-select product based on URL parameter
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const productParam = urlParams.get('product');
                if (productParam) {
                    const productDropdown = document.getElementById('product');
                    if (productDropdown) {
                        productDropdown.value = productParam;
                    }
                }
            } catch (e) { console.error("Error reading URL params:", e); }
        })();
    </script>


</body>
</html>
