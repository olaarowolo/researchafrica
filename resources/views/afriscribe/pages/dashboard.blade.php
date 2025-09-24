@extends('afriscribe.layouts.dashboard')

@section('page_title', 'AfriScribe Dashboard')

@section('page_description', 'Manage your AfriScribe platform - manuscripts, proofreading, insights, and more')

@section('dashboard_title', 'Welcome to AfriScribe Dashboard')

@section('dashboard_subtitle', 'Manage your academic publishing workflow from one central location')

@section('dashboard_content')
<div class="dashboard-grid">
    <div class="card">
        <div class="card-icon">📝</div>
        <h3>Manuscripts</h3>
        <p>Manage manuscript submissions and quote requests</p>
        <a href="{{ route('admin.afriscribe.quote-requests.index') }}" class="btn">View Requests</a>
    </div>
    <div class="card">
        <div class="card-icon">✏️</div>
        <h3>Proofreading</h3>
        <p>Track legacy proofreading requests and quality assurance</p>
        <a href="{{ route('admin.afriscribe.requests') }}" class="btn">View Legacy Requests</a>
    </div>
    <div class="card">
        <div class="card-icon">📊</div>
        <h3>Insights</h3>
        <p>View analytics and performance metrics</p>
        <a href="{{ route('afriscribe.insights') }}" class="btn">View Insights</a>
    </div>
    <div class="card">
        <div class="card-icon">🤝</div>
        <h3>Connect</h3>
        <p>Manage collaborations and communications</p>
        <a href="{{ route('afriscribe.connect') }}" class="btn">View Connect</a>
    </div>
    <div class="card">
        <div class="card-icon">📚</div>
        <h3>Archive</h3>
        <p>Access archived publications and documents</p>
        <a href="{{ route('afriscribe.archive') }}" class="btn">View Archive</a>
    </div>
</div>

<!-- Dashboard Statistics Section -->
<div class="dashboard-stats-section">
    <h2>Submissions & Requests Overview</h2>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
                <h3>{{ $totalRequests }}</h3>
                <p>Total Requests</p>
            </div>
        </div>
        <div class="stat-card pending">
            <div class="stat-icon">⏳</div>
            <div class="stat-content">
                <h3>{{ $pendingRequests }}</h3>
                <p>Pending</p>
            </div>
        </div>
        <div class="stat-card processing">
            <div class="stat-icon">⚙️</div>
            <div class="stat-content">
                <h3>{{ $processingRequests }}</h3>
                <p>Processing</p>
            </div>
        </div>
        <div class="stat-card completed">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <h3>{{ $completedRequests }}</h3>
                <p>Completed</p>
            </div>
        </div>
    </div>

    <!-- Recent Requests Table -->
    <div class="recent-requests">
        <h3>Recent Requests</h3>
        @if($recentRequests->count() > 0)
            <div class="table-responsive">
                <table class="requests-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Service Type</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRequests as $request)
                            <tr>
                                <td>{{ $request->name }}</td>
                                <td>{{ $request->email }}</td>
                                <td>{{ ucfirst($request->service_type) }}</td>
                                <td>
                                    <span class="status-badge {{ $request->status }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="no-data">No recent requests found.</p>
        @endif
    </div>
</div>
@endsection

@section('dashboard_styles')
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.card {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.card-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.card h3 {
    color: #0c1e35;
    margin-bottom: 1rem;
    font-weight: 600;
}

.card p {
    color: #666;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 500;
    transition: background 0.3s ease, transform 0.2s ease;
}

.btn:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}
@endsection
