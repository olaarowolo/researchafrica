@extends('layouts.member')

@section('page-name', 'Contact Us')

@section('content')

@php
$setting = \App\Models\Setting::first();
@endphp

<!-- Page Title
      ============================================= -->
<section class="page-title bg-dark text-white">
    <div class="container">
        <div class="page-title-row">

            <div class="page-title-content">
                <h1 class="text-white">CONTACT US</h1>
            </div>

        </div>
    </div>
</section><!-- .page-title end -->

<!-- Content
      ============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container">

            <div class="row gx-5 col-mb-80">
                <!-- Postcontent
          ============================================= -->
                <main class="postcontent col-lg-9">

                    <h3>Send us an Email</h3>

                    <div class="form-widget">

                        <div class="form-result"></div>

                        <form class="mb-0" action="{{route('user.contact')}}"
                            method="post">
                            @csrf
                            <div class="form-process">
                                <div class="css3-spinner">
                                    <div class="css3-spinner-scaler"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label for="name">Name
                                        <small>*</small></label>
                                    <input type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name') }}" class="form-control required">
                                </div>

                                <div class="col-md-4 form-group">
                                    <label
                                        for="email">Email
                                        <small>*</small></label>
                                    <input type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        class="required email form-control">
                                </div>

                                <div class="col-md-4 form-group">
                                    <label
                                        for="phone">Phone</label>
                                    <input type="text"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone') }}" class="form-control">
                                </div>

                                <div class="w-100"></div>

                                <div class="col-md-12 form-group">
                                    <label
                                        for="subject">Subject
                                        <small>*</small></label>
                                    <input type="text"
                                        id="subject"
                                        name="subject" value="{{ old('subject') }}"
                                        class="required form-control">
                                </div>

                                <div class="w-100"></div>

                                <div class="col-12 form-group">
                                    <label
                                        for="message">Message
                                        <small>*</small></label>
                                    <textarea class="required form-control"
                                        id="message"
                                        name="message"
                                        rows="6" cols="30">{{ old('message') }}</textarea>
                                </div>

                                <div class="col-12 form-group d-none">
                                    <input type="text"
                                        id="botcheck"
                                        name="botcheck"
                                        value="" class="form-control">
                                </div>

                                <div class="col-12 form-group">
                                    <button class="btn m-0 text-white"
                                        type="submit"
                                        id="submit"
                                        name="submit"
                                        value="submit"
                                        style="background-color:#000">Send
                                        Message</button>
                                </div>
                            </div>

                        </form>
                    </div>

                </main><!-- .postcontent end -->

                <!-- Sidebar
          ============================================= -->
                <aside class="sidebar col-lg-3">

                    <div class="widget">

                        <h3 class="mb-0 text-uppercase">Nigeria</h3><br>

                        <address>
                            <strong>Headquarters:</strong>
                            <span class="kb-w-3/12">
                                {{ $setting ? $setting->address : '' }} <br />
                            </span>
                        </address>
                        <abbr
                            title="Phone Number"><strong>Phone:</strong></abbr>
                        {{ $setting ? $setting->phone_number : '' }}
                        <br>
                        <abbr
                            title="Email Address"><strong>Email:</strong></abbr>
                        {{ $setting ? $setting->website_email : '' }}
                    </div>

                    <div class="line line-sm"></div>

                    <div class="widget mt-0">

                        <a href="{{ $setting ? $setting->facebook_url : '' }}"
                            class="social-icon si-small bg-dark border-0 h-bg-facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>

                        <a href="{{ $setting ? $setting->twitter_url : '' }}"
                            class="social-icon si-small bg-dark border-0 h-bg-twitter">
                            <i class="fa-brands fa-twitter"></i>
                            <i class="fa-brands fa-twitter"></i>
                        </a>

                        <a href="{{ $setting ? $setting->instagram_url : '' }}"
                            class="social-icon si-small bg-dark border-0 h-bg-instagram">
                            <i class="fa-brands fa-instagram"></i>
                            <i class="fa-brands fa-instagram"></i>
                        </a>

                        <a href="{{ $setting ? $setting->linkedin_url : '' }}"
                            class="social-icon si-small bg-dark border-0 h-bg-linkedin">
                            <i class="fa-brands fa-linkedin"></i>
                            <i class="fa-brands fa-linkedin"></i>
                        </a>

                    </div>

                </aside><!-- .sidebar end -->
            </div>

        </div>

    </div>
</section><!-- #content end -->

@endsection



@section('scripts')
@endsection
