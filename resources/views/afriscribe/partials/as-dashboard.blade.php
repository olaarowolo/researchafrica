@section('dashboard_section')
<div class="dashboard-section">
    <div class="container">
        <div class="section-header">
            <h2>Submissions & Requests Dashboard</h2>
            <p>Overview of all your AfriScribe submissions and requests</p>
        </div>

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

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="{{ route('afriscribe.welcome-form') }}" class="btn btn-primary">Submit New Request</a>
            <a href="{{ route('afriscribe.manuscripts') }}" class="btn btn-secondary">View All Manuscripts</a>
        </div>
    </div>
</div>
@endsection

@section('dashboard_styles')
.dashboard-section {
    padding: 4rem 0;
    background: #f8f9fa;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-header h2 {
    color: #0c1e35;
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.section-header p {
    color: #666;
    font-size: 1.2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.stat-content h3 {
    color: #0c1e35;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.stat-content p {
    color: #666;
    font-size: 1rem;
    margin: 0.5rem 0 0 0;
}

.stat-card.pending .stat-content h3 { color: #ffc107; }
.stat-card.processing .stat-content h3 { color: #17a2b8; }
.stat-card.completed .stat-content h3 { color: #28a745; }

.recent-requests {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 3rem;
}

.recent-requests h3 {
    color: #0c1e35;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
}

.table-responsive {
    overflow-x: auto;
}

.requests-table {
    width: 100%;
    border-collapse: collapse;
}

.requests-table th,
.requests-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.requests-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #0c1e35;
}

.requests-table td {
    color: #333;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-badge.pending { background: #fff3cd; color: #856404; }
.status-badge.processing { background: #d1ecf1; color: #0c5460; }
.status-badge.completed { background: #d4edda; color: #155724; }

.quick-actions {
    text-align: center;
}

.btn {
    display: inline-block;
    padding: 1rem 2rem;
    margin: 0 1rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: scale(1.05);
}

.btn-secondary {
    background: #6c757d;
    color: #fff;
}

.btn-secondary:hover {
    background: #545b62;
    transform: scale(1.05);
}

.no-data {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 2rem;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .requests-table th,
    .requests-table td {
        padding: 0.5rem;
        font-size: 0.875rem;
    }

    .btn {
        display: block;
        margin: 1rem 0;
        width: 100%;
        max-width: 300px;
    }
}
@endsection
