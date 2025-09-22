<!-- Manuscripts Pricing Section -->
<section class="content-section" style="background: #f8f9fb;">
    <h2 style="color: #0c1e35; font-size: 2.5rem; margin-bottom: 1rem; text-align: center;">Flexible Pricing Plans</h2>
    <p style="font-size: 1.2rem; color: #666; margin-bottom: 3rem; text-align: center; max-width: 600px; margin-left: auto; margin-right: auto;">
        Choose the plan that best fits your institution's needs and budget
    </p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; max-width: 1000px; margin: 0 auto;">
        <!-- Starter Plan -->
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 2px solid #e1e5e9;">
            <h3 style="color: #0c1e35; font-size: 1.5rem; margin-bottom: 1rem;">Starter</h3>
            <div style="text-align: center; margin-bottom: 2rem;">
                <span style="font-size: 3rem; font-weight: bold; color: #f9b233;">$99</span>
                <span style="color: #666;">/month</span>
            </div>
            <ul style="list-style: none; padding: 0; margin-bottom: 2rem;">
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Up to 50 submissions/month</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Basic peer review workflow</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Standard analytics</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Email support</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Basic customization</li>
            </ul>
            <button style="width: 100%; background: #f9b233; color: #0c1e35; padding: 1rem; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;" onclick="scrollToForm()">
                Get Started
            </button>
        </div>

        <!-- Professional Plan -->
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 3px solid #f9b233; position: relative;">
            <div style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: #f9b233; color: #0c1e35; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: bold;">
                Most Popular
            </div>
            <h3 style="color: #0c1e35; font-size: 1.5rem; margin-bottom: 1rem;">Professional</h3>
            <div style="text-align: center; margin-bottom: 2rem;">
                <span style="font-size: 3rem; font-weight: bold; color: #f9b233;">$299</span>
                <span style="color: #666;">/month</span>
            </div>
            <ul style="list-style: none; padding: 0; margin-bottom: 2rem;">
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Up to 200 submissions/month</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Advanced peer review workflow</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Advanced analytics & reporting</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Priority support</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Full customization</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ API access</li>
            </ul>
            <button style="width: 100%; background: #f9b233; color: #0c1e35; padding: 1rem; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;" onclick="scrollToForm()">
                Get Started
            </button>
        </div>

        <!-- Enterprise Plan -->
        <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 2px solid #e1e5e9;">
            <h3 style="color: #0c1e35; font-size: 1.5rem; margin-bottom: 1rem;">Enterprise</h3>
            <div style="text-align: center; margin-bottom: 2rem;">
                <span style="font-size: 3rem; font-weight: bold; color: #f9b233;">Custom</span>
                <span style="color: #666;">pricing</span>
            </div>
            <ul style="list-style: none; padding: 0; margin-bottom: 2rem;">
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Unlimited submissions</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Custom workflows</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ White-label solution</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Dedicated support</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ On-premise deployment</li>
                <li style="margin-bottom: 0.5rem; color: #666;">✓ Custom integrations</li>
            </ul>
            <button style="width: 100%; background: #f9b233; color: #0c1e35; padding: 1rem; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;" onclick="scrollToForm()">
                Contact Sales
            </button>
        </div>
    </div>

    <script>
        function scrollToForm() {
            const formSection = document.getElementById('contact-form');
            if (formSection) {
                formSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
    </script>
</section>
