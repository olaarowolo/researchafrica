@extends('afriscribe.layouts.dashboard')

@section('page_title', 'AfriScribe Dashboard')

@section('page_description', 'Manage your AfriScribe platform - manuscripts, proofreading, insights, and more')

@section('dashboard_title', 'Welcome to AfriScribe Dashboard')

@section('dashboard_subtitle', 'Manage your academic publishing workflow from one central location')

@section('dashboard_content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <h3>📝 Manuscripts</h3>
            <p>Manage manuscript submissions and peer review process</p>
            <a href="{{ route('afriscribe.manuscripts') }}" class="btn">View Manuscripts</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <h3>✏️ Proofreading</h3>
            <p>Track proofreading requests and quality assurance</p>
            <a href="{{ route('afriscribe.proofread') }}" class="btn">View Proofreading</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <h3>📊 Insights</h3>
            <p>View analytics and performance metrics</p>
            <a href="{{ route('afriscribe.insights') }}" class="btn">View Insights</a>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 2rem;">
    <div class="col-md-6">
        <div class="card">
            <h3>🤝 Connect</h3>
            <p>Manage collaborations and communications</p>
            <a href="{{ route('afriscribe.connect') }}" class="btn">View Connect</a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <h3>📚 Archive</h3>
            <p>Access archived publications and documents</p>
            <a href="{{ route('afriscribe.archive') }}" class="btn">View Archive</a>
        </div>
    </div>
</div>
@endsection

@section('dashboard_styles')
.card {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 1rem;
    text-align: center;
}

.card h3 {
    color: #0c1e35;
    margin-bottom: 1rem;
}

.card p {
    color: #666;
    margin-bottom: 1.5rem;
}

.row {
    display: flex;
    gap: 2rem;
    margin-bottom: 2rem;
}

.col-md-4, .col-md-6 {
    flex: 1;
}

@media (max-width: 768px) {
    .row {
        flex-direction: column;
    }
}
@endsection
