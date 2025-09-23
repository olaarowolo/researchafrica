<!-- Proofreading Interest Form Section -->
<section id="contact-form" style="padding: 60px 20px; background: #f8f9fb;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 style="font-size: 2.5rem; color: #0c1e35;">Get a Quote</h2>
            <p style="font-size: 1.2rem; color: #555;">Fill out the form below and we'll get back to you with a personalized quote.</p>
        </div>
        <form id="proofreading-interest-form" method="POST" action="{{ route('afriscribe.request') }}" style="display: grid; gap: 1.5rem;">
            @csrf
            <input type="hidden" name="form_type" value="proofreading_quote">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <input type="text" name="name" placeholder="Your Name" required style="padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem;">
                <input type="email" name="email" placeholder="Your Email" required style="padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem;">
            </div>
            <input type="text" name="institution" placeholder="Institution / University (Optional)" style="padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem;">
            <input type="number" name="word_count" placeholder="Document Word Count" required style="padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem;">
            <select name="turnaround_time" required style="padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; background: white;">
                <option value="" disabled selected>Select Turnaround Time</option>
                <option value="standard">Standard (7 Days)</option>
                <option value="advanced">Advanced (3 Days)</option>
                <option value="express">Express (24 Hours)</option>
            </select>
            <textarea name="details" placeholder="Any additional details or questions?" rows="4" style="padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; resize: vertical;"></textarea>
            <div style="text-align: center;">
                <button type="submit" class="btn" style="background: #f9b233; color: #0c1e35; padding: 15px 40px; font-size: 1.1rem; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Submit Request</button>
            </div>
        </form>
    </div>
</section>
