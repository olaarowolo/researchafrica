<!-- Footer
     ============================================= -->
<footer id="footer" class="dark bg-dark">
    <div class="container">

        <!-- Footer Widgets
                    ============================================= -->
        <div class="footer-widgets-wrap">

            <div class="row">
                <div class="col-lg-12">
                    <div class="widget">


                        <div class="line" style="margin: 30px 0;"></div>

                        <div class="row justify-content-center">
                            <div class="col-lg-4 mb-3 widget_links">

                                <h2 class="kb-text-lg kb-uppercase kb-pb-1 kb-border-b-2 kb-w-fit kb-pe-3 kb-ps-1 mb-2">
                                    <strong>About</strong></h2>


                                <ul>
                                    <li><a href="{{ route('member.about') }}">About Research Africa Publications</a>
                                    </li>
                                    <li><a href="#">Permisions</a></li>
                                    <li><a href="/terms">Term of Use</a></li>
                                    <li><a href="/ethics">Ethics</a></li>
                                    <li><a href="/policy">Privacy Policy</a></li>
                                    <li><a href="/cookiepolicy">Cookie Policy</a></li>

                                </ul>



                            </div>

                            <div class="col-lg-4 mb-3 widget_links">
                                <h2 class="kb-text-lg kb-uppercase kb-pb-1 kb-border-b-2 kb-w-fit kb-pe-3 kb-ps-1 mb-2">
                                    <strong>Information for</strong></h2>

                                <ul>
                                    <li><a href="/infomation/authors">Authors</a></li>
                                    <li><a href="/infomation/editors">Editors</a></li>
                                    <li><a href="/infomation/researchers">Reseachers</a></li>
                                    <li><a href="/infomation/reviewers">Reviewers</a></li>
                                    <li><a href="{{ route('member.faq') }}">Frequently Asked Questions</a></li>
                                </ul>
                            </div>

                            <div class="col-lg-4 mb-3 widget_links">
                                <h2 class="kb-text-lg kb-uppercase kb-pb-1 kb-border-b-2 kb-w-fit kb-pe-3 kb-ps-1 mb-2">
                                    <strong>Social Media</strong></h2>

                                <ul>
                                    <li class="{{ $setting && $setting->facebook_url ? 'kb-block' : 'kb-hidden' }}"><a href="{{ $setting->facebook_url ?? '' }}">
                                        <i class='bx bxl-facebook-square'></i>
                                        Facebook
                                    </a></li>
                                    <li class="{{ $setting && $setting->instagram_url ? 'kb-block' : 'kb-hidden' }}"><a href="{{ $setting->instagram_url ?? '' }}">
                                        <i class='bx bxl-instagram-alt' ></i>
                                        Instagram
                                    </a></li>
                                    <li class="{{ $setting && $setting->twitter_url ? 'kb-block' : 'kb-hidden' }}"><a href="{{ $setting->twitter_url ?? '' }}">
                                        <i class='bx bxl-twitter' ></i>
                                        Twitter
                                    </a></li>
                                    <li class="{{ $setting && $setting->linkedin_url ? 'kb-block' : 'kb-hidden' }}"><a href="{{ $setting->linkedin_url ?? '' }}">
                                        <i class='bx bxl-linkedin' ></i>
                                        Linkedin
                                    </a></li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- .footer-widgets-wrap end -->
    </div>

    <!-- Copyrights
                ============================================= -->
    <div id="copyrights">
        <div class="container">

            <div class="row justify-content-between col-mb-30">
                <div class="col-12 col-md-auto text-center text-md-start">
                    Copyrights &copy; 2023 Research Africa Publication.<br>
                </div>

                <div class="col-12 col-md-auto text-center text-md-end">
                    <div class="copyrights-menu copyright-links">
                        <a href="{{ route('home') }}">Home</a>/<a href="{{ route('member.about') }}">About Us</a>/<a
                            href="{{ route('member.faq') }}">FAQs</a>/<a
                            href="{{ route('member.contact') }}">Contact</a>
                    </div>
                </div>
            </div>

        </div>
    </div><!-- #copyrights end -->
</footer><!-- #footer end -->
