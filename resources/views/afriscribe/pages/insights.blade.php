@extends('afriscribe.layouts.dashboard')

@section('page_title', 'AfriScribe Insights')

@section('page_description', 'View analytics and performance metrics for your AfriScribe platform')

@section('dashboard_title', 'Insights & Analytics')

@section('dashboard_subtitle', 'Track your platform performance and user engagement')

@section('dashboard_content')
<div class="insights-container">
    <div class="insights-grid">
        <div class="insight-card">
            <div class="card-header">
                <h3>📊 Platform Overview</h3>
            </div>
            <div class="card-body">
                <p>Comprehensive analytics and insights about your AfriScribe platform performance.</p>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-label">Total Requests</span>
                        <span class="stat-value">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Active Users</span>
                        <span class="stat-value">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Completion Rate</span>
                        <span class="stat-value">0%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="insight-card">
            <div class="card-header">
                <h3>📈 Performance Metrics</h3>
            </div>
            <div class="card-body">
                <p>Track key performance indicators and growth metrics.</p>
                <div class="chart-placeholder">
                    <p>Performance charts will be displayed here</p>
                </div>
            </div>
        </div>

        <div class="insight-card">
            <div class="card-header">
                <h3>🎯 Goals & Targets</h3>
            </div>
            <div class="card-body">
                <p>Monitor progress towards your publishing goals.</p>
                <div class="goals-list">
                    <div class="goal-item">
                        <span class="goal-label">Monthly Requests</span>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="goal-value">0/100</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('dashboard_styles')
.insights-container {
    padding: 2rem 0;
}

.insights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.insight-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.insight-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.card-header {
    background: linear-gradient(135deg, #0c1e35, #1a2e47);
    color: #fff;
    padding: 1.5rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
}

.card-body {
    padding: 2rem;
}

.card-body p {
    color: #666;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.stat-label {
    display: block;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.stat-value {
    display: block;
    color: #0c1e35;
    font-size: 1.5rem;
    font-weight: 700;
}

.chart-placeholder {
    height: 200px;
    background: #f8f9fa;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    border: 2px dashed #ddd;
}

.goals-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.goal-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.goal-label {
    flex: 1;
    color: #666;
    font-weight: 500;
}

.progress-bar {
    flex: 2;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.goal-value {
    color: #0c1e35;
    font-weight: 600;
    min-width: 60px;
    text-align: right;
}

@media (max-width: 768px) {
    .insights-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .goal-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .goal-value {
        text-align: left;
    }
}
@endsection
