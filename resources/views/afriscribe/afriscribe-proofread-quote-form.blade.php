@section('styles')
<style>
    /* Form Section */
    .form-section {
        padding: 4rem 2rem;
        background: #fff;
    }

    .form-container {
        max-width: 700px;
        margin: 0 auto;
        background: #f8f9fb;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .form-container h2 {
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
</style>

<section id="request" class="form-section">
    <div class="form-container">
        <h2>Request a Proofreading Quote</h2>
        <form id="quoteForm" method="POST" action="/afriscribe/request" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Your full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Your email" required>
            </div>
            <div class="form-group">
                <label for="service">Select Service</label>
                <select id="service" name="service" required>
                    <option value="">--Choose a service--</option>
                    <option value="student">Student-Friendly Proofreading (£15 / 1000 words)</option>
                    <option value="research">Research Editing (£25 / 1000 words)</option>
                    <option value="publication">Publication-Ready Academic Edit (£40 / 1000 words)</option>
                    <option value="basic">Basic Scholar Package – £120 (Flat, up to 10k words)</option>
                    <option value="advantage">Researcher’s Advantage – £220 (Flat, up to 10k words)</option>
                    <option value="premium">Premium Publication – £350 (Flat, up to 10k words)</option>
                    <option value="phd">PhD Thesis Premium (Calculated per word, up to 100k)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="wordCount">Word Count</label>
                <input type="number" id="wordCount" name="wordCount" placeholder="Enter total words" required>
            </div>
            <div class="form-group addons">
                <label>Add-Ons</label>
                <label><input type="checkbox" name="addons" value="rush"> Rush Service (48h) – £150</label>
                <label><input type="checkbox" name="addons" value="extra"> Extra Words beyond package – £15 per
                    1000</label>
            </div>
            <div class="form-group">
                <label for="file">Upload Manuscript</label>
                <input type="file" id="file" name="file" accept=".doc,.docx,.pdf" required>
            </div>
            <div class="form-group">
                <label for="message">Additional Details</label>
                <textarea id="message" name="message" rows="4" placeholder="Any notes or special instructions"></textarea>
            </div>
            <div class="total" id="totalCost">Estimated Cost: £0</div>
            <button type="submit">Submit Request</button>
        </form>
    </div>
</section>
