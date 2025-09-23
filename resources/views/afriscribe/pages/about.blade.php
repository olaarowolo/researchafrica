@extends('afriscribe.layouts.app')

@section('title', 'About Us - AfriScribe | Research Africa')

@section('meta_description', 'Learn about AfriScribe\'s mission to empower African academic publishing through innovative technology solutions. Discover our story, values, and commitment to advancing scholarly communication in Africa.')

@section('content')
<!-- Hero Section -->
<section class="hero" style="background: linear-gradient(rgba(12, 30, 53, 0.9), rgba(12, 30, 53, 0.9)), url('{{ asset('afriscribe/img/hero-bg.jpg') }}') no-repeat center/cover;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem; text-align: center;">
        <h1 style="font-size: 3.5rem; margin-bottom: 1.5rem; color: #fff; font-weight: 700;">About AfriScribe</h1>
        <p style="font-size: 1.4rem; margin-bottom: 2rem; color: #f9b233; max-width: 800px; margin-left: auto; margin-right: auto; line-height: 1.6;">
            Empowering African academic publishing through innovative technology solutions
        </p>
        <p style="font-size: 1.1rem; color: #e0e0e0; max-width: 600px; margin: 0 auto; line-height: 1.6;">
            We are committed to advancing scholarly communication and research excellence across the African continent.
        </p>
    </div>
</section>

<!-- Our Story Section -->
<section class="content-section">
    <div style="max-width: 1000px; margin: 0 auto; padding: 0 2rem;">
        <h2 style="text-align: center; margin-bottom: 3rem; color: #0c1e35; font-size: 2.5rem;">Our Story</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem; align-items: center; margin-bottom: 4rem;">
            <div>
                <h3 style="color: #f9b233; margin-bottom: 1.5rem; font-size: 1.8rem;">Bridging the Gap in African Academia</h3>
                <p style="font-size: 1.1rem; line-height: 1.8; color: #555; margin-bottom: 1.5rem;">
                    AfriScribe was born from a simple yet powerful vision: to democratize access to high-quality academic publishing tools for African researchers, scholars, and institutions. We recognized that while Africa produces world-class research, many academics face significant barriers in publishing and disseminating their work globally.
                </p>
                <p style="font-size: 1.1rem; line-height: 1.8; color: #555; margin-bottom: 1.5rem;">
                    Our platform was created to address these challenges by providing affordable, user-friendly solutions that streamline the entire academic publishing workflow—from manuscript preparation to peer review and final publication.
                </p>
            </div>
            <div style="text-align: center;">
                <img src="{{ asset('afriscribe/img/afriscribe-logo-main-logo-black.png') }}" alt="AfriScribe Logo"
                     style="height: 200px; width: auto; opacity: 0.8;">
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section style="background: #f8f9fb; padding: 4rem 0;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 4rem;">
            <div class="card" style="text-align: center; padding: 3rem 2rem;">
                <div class="card-icon" style="margin: 0 auto 2rem; background: #f9b233; color: #0c1e35; font-size: 2rem;">🎯</div>
                <h3 style="color: #0c1e35; margin-bottom: 1.5rem; font-size: 1.8rem;">Our Mission</h3>
                <p style="color: #666; line-height: 1.6; font-size: 1rem;">
                    To empower African academic institutions and researchers with cutting-edge technology solutions that enhance research quality, streamline publishing processes, and increase global visibility of African scholarship.
                </p>
            </div>

            <div class="card" style="text-align: center; padding: 3rem 2rem;">
                <div class="card-icon" style="margin: 0 auto 2rem; background: #f9b233; color: #0c1e35; font-size: 2rem;">👁️</div>
                <h3 style="color: #0c1e35; margin-bottom: 1.5rem; font-size: 1.8rem;">Our Vision</h3>
                <p style="color: #666; line-height: 1.6; font-size: 1rem;">
                    To become Africa's leading academic publishing technology platform, fostering a vibrant ecosystem where African research thrives and contributes meaningfully to global knowledge advancement.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="content-section">
    <div style="max-width: 800px; margin: 0 auto; padding: 0 2rem;">
        <h2 style="text-align: center; margin-bottom: 3rem; color: #0c1e35; font-size: 2.5rem;">Our Values</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div class="card" style="text-align: center; padding: 2rem;">
                <div class="card-icon" style="margin: 0 auto 1.5rem;">🌍</div>
                <h4 style="color: #f9b233; margin-bottom: 1rem; font-size: 1.3rem;">Accessibility</h4>
                <p style="color: #666; line-height: 1.6;">
                    We believe in making high-quality academic tools accessible to all African researchers, regardless of their institution's size or location.
                </p>
            </div>

            <div class="card" style="text-align: center; padding: 2rem;">
                <div class="card-icon" style="margin: 0 auto 1.5rem;">💡</div>
                <h4 style="color: #f9b233; margin-bottom: 1rem; font-size: 1.3rem;">Innovation</h4>
                <p style="color: #666; line-height: 1.6;">
                    We continuously develop and integrate the latest technologies to solve real-world challenges in academic publishing.
                </p>
            </div>

            <div class="card" style="text-align: center; padding: 2rem;">
                <div class="card-icon" style="margin: 0 auto 1.5rem;">🤝</div>
                <h4 style="color: #f9b233; margin-bottom: 1rem; font-size: 1.3rem;">Collaboration</h4>
                <p style="color: #666; line-height: 1.6;">
                    We foster partnerships with academic institutions, researchers, and publishers to build a stronger African research community.
                </p>
            </div>

            <div class="card" style="text-align: center; padding: 2rem;">
                <div class="card-icon" style="margin: 0 auto 1.5rem;">⭐</div>
                <h4 style="color: #f9b233; margin-bottom: 1rem; font-size: 1.3rem;">Excellence</h4>
                <p style="color: #666; line-height: 1.6;">
                    We are committed to delivering exceptional quality in everything we do, from our technology to our customer service.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section style="background: #0c1e35; color: #fff; padding: 4rem 0;">
    <div style="max-width: 1000px; margin: 0 auto; padding: 0 2rem; text-align: center;">
        <h2 style="margin-bottom: 3rem; font-size: 2.5rem;">Our Team</h2>
        <p style="font-size: 1.2rem; margin-bottom: 3rem; color: #e0e0e0; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6;">
            AfriScribe is powered by a passionate team of technologists, academics, and publishing experts who share a common goal: advancing African scholarship through technology.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
            <div style="text-align: center;">
                <div style="width: 120px; height: 120px; background: #f9b233; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #0c1e35;">
                    👨‍💼
                </div>
                <h4 style="color: #f9b233; margin-bottom: 0.5rem;">Leadership Team</h4>
                <p style="color: #a0aec0; font-size: 0.9rem;">Experienced professionals driving our mission forward</p>
            </div>

            <div style="text-align: center;">
                <div style="width: 120px; height: 120px; background: #f9b233; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #0c1e35;">
                    👩‍🔬
                </div>
                <h4 style="color: #f9b233; margin-bottom: 0.5rem;">Research Experts</h4>
                <p style="color: #a0aec0; font-size: 0.9rem;">Academic professionals ensuring quality and relevance</p>
            </div>

            <div style="text-align: center;">
                <div style="width: 120px; height: 120px; background: #f9b233; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #0c1e35;">
                    👨‍💻
                </div>
                <h4 style="color: #f9b233; margin-bottom: 0.5rem;">Tech Innovators</h4>
                <p style="color: #a0aec0; font-size: 0.9rem;">Skilled developers creating cutting-edge solutions</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA Section -->
<section class="cta">
    <div style="max-width: 800px; margin: 0 auto; padding: 0 2rem; text-align: center;">
        <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem; color: #0c1e35;">Ready to Transform Your Academic Publishing?</h2>
        <p style="font-size: 1.2rem; margin-bottom: 2rem; color: #666; line-height: 1.6;">
            Join thousands of African researchers who trust AfriScribe to enhance their academic publishing journey.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('afriscribe.welcome') }}" class="cta-btn">Explore Our Services</a>
            <a href="{{ route('afriscribe.quote-request.create') }}" class="btn" style="background: transparent; border: 2px solid #0c1e35; color: #0c1e35;">Get Started</a>
        </div>
    </div>
</section>
@endsection

@section('custom-styles')
<style>
    .hero {
        min-height: 50vh;
        display: flex;
        align-items: center;
    }

    .card {
        background: #fff;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
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

    @media(max-width: 768px) {
        .hero h1 {
            font-size: 2.5rem;
        }

        .hero p {
            font-size: 1.1rem;
        }
    }
</style>
@endsection
