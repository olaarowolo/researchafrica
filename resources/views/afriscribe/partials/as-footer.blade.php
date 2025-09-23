<!-- Footer -->
<footer>
    <div class="content-section">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h3 style="color: #f9b233; margin-bottom: 1rem;">Stay Connected</h3>
            <p>Get the latest updates on AfriScribe features and academic publishing insights.</p>
            <footer style="background: #0c1e35; color: #fff; padding: 0;">
                <div style="max-width: 1200px; margin: 0 auto; padding: 4rem 2rem;">
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 2.5rem; text-align: left; margin-bottom: 3rem;">

                        <!-- Column 1: Brand Info -->
                        <div>
                            <a href="{{ route('afriscribe.welcome') }}"
                                style="display: inline-block; margin-bottom: 1rem;">
                                <img src="{{ asset('afriscribe/img/afriscribe-logo-main-logo-white.png') }}" alt="AfriScribe Logo"
                                    style="height: 40px; width: auto;">
                            </a>
                            <p style="color: #a0aec0; font-size: 0.9rem; line-height: 1.6; text-align: left">
                                Empowering African academic publishing through innovative technology solutions.
                            </p>
                        </div>

                        <!-- Column 2: Products -->
                        <div>
                            <h4 style="color: #f9b233; margin-bottom: 1.2rem; font-size: 1.1rem;">Products</h4>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                <li style="margin-bottom: 0.75rem;"><a href="{{ route('afriscribe.manuscripts') }}"
                                        style="color: #fff; text-decoration: none; transition: color 0.3s;"
                                        onmouseover="this.style.color='#f9b233'"
                                        onmouseout="this.style.color='#fff'">Manuscripts</a></li>
                                <li style="margin-bottom: 0.75rem;"><a href="{{ route('afriscribe.proofreading') }}"
                                        style="color: #fff; text-decoration: none; transition: color 0.3s;"
                                        onmouseover="this.style.color='#f9b233'"
                                        onmouseout="this.style.color='#fff'">Proofreading</a></li>
                                <li style="margin-bottom: 0.75rem;"><a href="#"
                                        style="color: #fff; text-decoration: none; transition: color 0.3s;"
                                        onmouseover="this.style.color='#f9b233'"
                                        onmouseout="this.style.color='#fff'">Analytics</a></li>
                                <li style="margin-bottom: 0.75rem;"><a href="#"
                                        style="color: #fff; text-decoration: none; transition: color 0.3s;"
                                        onmouseover="this.style.color='#f9b233'"
                                        onmouseout="this.style.color='#fff'">Archive</a></li>
                            </ul>
                        </div>

                        <!-- Column 3: Company -->
                        <div>
                            <h4 style="color: #f9b233; margin-bottom: 1.2rem; font-size: 1.1rem;">Company</h4>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                <li style="margin-bottom: 0.75rem;"><a href="{{ route('afriscribe.about') }}"
                                        style="color: #fff; text-decoration: none; transition: color 0.3s;"
                                        onmouseover="this.style.color='#f9b233'"
                                        onmouseout="this.style.color='#fff'">About Us</a></li>
                                <li style="margin-bottom: 0.75rem;"><a href="#"
                                        style="color: #fff; text-decoration: none; transition: color 0.3s;"
                                        onmouseover="this.style.color='#f9b233'"
                                        onmouseout="this.style.color='#fff'">Blog</a></li>
                                <li style="margin-bottom: 0.75rem;"><a
                                        href="{{ route('afriscribe.quote-request.create') }}"
                                        style="color: #fff; text-decoration: none; transition: color 0.3s;"
                                        onmouseover="this.style.color='#f9b233'"
                                        onmouseout="this.style.color='#fff'">Contact Us</a></li>
                                <li style="margin-bottom: 0.75rem;"><a href="#"
                                        style="color: #fff; text-decoration: none; transition: color 0.3s;"
                                        onmouseover="this.style.color='#f9b233'"
                                        onmouseout="this.style.color='#fff'">Privacy Policy</a></li>
                            </ul>
                        </div>

                        <!-- Column 4: Newsletter -->
                        <div>
                            <h4 style="color: #f9b233; margin-bottom: 1.2rem; font-size: 1.1rem;">Stay Connected</h4>
                            <p style="color: #a0aec0; font-size: 0.9rem; margin-bottom: 1rem;">Get the latest updates
                                and insights.</p>
                            <form action="#" method="POST" style="margin-top: 1rem;">
                                <div style="display: flex; border-radius: 5px; overflow: hidden;">
                                    <input type="email" name="email" placeholder="Enter your email" required
                                        style="width: 100%; padding: 0.75rem; border: 1px solid #4a5568; background: #2d3748; color: #fff; outline: none; border-right: none;">
                                    <button type="submit"
                                        style="padding: 0.75rem 1rem; background: #f9b233; color: #0c1e35; border: none; cursor: pointer; font-weight: bold; transition: background 0.3s;">Go</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div style="text-align: center;">
                        <p>&copy; {{ date('Y') }} AfriScribe | Research Africa. All Rights Reserved.</p>
                        <p style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
                            Empowering African academic publishing through innovative technology solutions.
                        <div
                            style="border-top: 1px solid #2d3748; padding-top: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <p style="color: #a0aec0; font-size: 0.9rem; margin: 0;">
                                &copy; {{ date('Y') }} AfriScribe | Research Africa. All Rights Reserved.
                            </p>
                            <div style="display: flex; gap: 1rem; align-items: center;">
                                <a href="#" aria-label="Twitter" style="color: #a0aec0; transition: color 0.3s;"
                                    onmouseover="this.style.color='#f9b233'" onmouseout="this.style.color='#a0aec0'">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z">
                                        </path>
                                    </svg>
                                </a>
                                <a href="#" aria-label="LinkedIn" style="color: #a0aec0; transition: color 0.3s;"
                                    onmouseover="this.style.color='#f9b233'" onmouseout="this.style.color='#a0aec0'">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z">
                                        </path>
                                        <rect x="2" y="9" width="4" height="12"></rect>
                                        <circle cx="4" cy="4" r="2"></circle>
                                    </svg>
                                </a>
                                <a href="#" aria-label="Facebook" style="color: #a0aec0; transition: color 0.3s;"
                                    onmouseover="this.style.color='#f9b233'" onmouseout="this.style.color='#a0aec0'">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z">
                                        </path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
            </footer>
