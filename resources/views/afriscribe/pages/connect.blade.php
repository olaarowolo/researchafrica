@extends('afriscribe.layouts.dashboard')

@section('page_title', 'AfriScribe Connect')

@section('page_description', 'Manage collaborations and communications on the AfriScribe platform')

@section('dashboard_title', 'Connect & Collaborate')

@section('dashboard_subtitle', 'Build partnerships and manage communications')

@section('dashboard_content')
<div class="connect-container">
    <div class="connect-grid">
        <div class="connect-card">
            <div class="card-header">
                <h3>🤝 Partnerships</h3>
            </div>
            <div class="card-body">
                <p>Manage your academic partnerships and collaborations.</p>
                <div class="partnership-list">
                    <div class="partnership-item">
                        <div class="partnership-icon">🏛️</div>
                        <div class="partnership-info">
                            <h4>University Collaborations</h4>
                            <p>Connect with academic institutions</p>
                        </div>
                        <div class="partnership-status active">Active</div>
                    </div>
                    <div class="partnership-item">
                        <div class="partnership-icon">📚</div>
                        <div class="partnership-info">
                            <h4>Publisher Network</h4>
                            <p>Expand your publishing reach</p>
                        </div>
                        <div class="partnership-status pending">Pending</div>
                    </div>
                </div>
                <a href="#" class="btn btn-primary">Manage Partnerships</a>
            </div>
        </div>

        <div class="connect-card">
            <div class="card-header">
                <h3>💬 Communications</h3>
            </div>
            <div class="card-body">
                <p>Stay connected with your academic community.</p>
                <div class="communication-stats">
                    <div class="stat-item">
                        <span class="stat-number">0</span>
                        <span class="stat-label">Active Conversations</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">0</span>
                        <span class="stat-label">Unread Messages</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">0</span>
                        <span class="stat-label">Pending Responses</span>
                    </div>
                </div>
                <a href="#" class="btn btn-secondary">View Messages</a>
            </div>
        </div>

        <div class="connect-card">
            <div class="card-header">
                <h3>🌐 Network</h3>
            </div>
            <div class="card-body">
                <p>Expand your professional network in academia.</p>
                <div class="network-features">
                    <div class="feature-item">
                        <span class="feature-icon">🔍</span>
                        <span class="feature-text">Find Researchers</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">📧</span>
                        <span class="feature-text">Direct Messaging</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">👥</span>
                        <span class="feature-text">Group Discussions</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">📅</span>
                        <span class="feature-text">Event Coordination</span>
                    </div>
                </div>
                <a href="#" class="btn btn-outline">Explore Network</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('dashboard_styles')
.connect-container {
    padding: 2rem 0;
}

.connect-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.connect-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.connect-card:hover {
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

.partnership-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.partnership-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    gap: 1rem;
}

.partnership-icon {
    font-size: 2rem;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.partnership-info {
    flex: 1;
}

.partnership-info h4 {
    margin: 0 0 0.25rem 0;
    color: #0c1e35;
    font-weight: 600;
}

.partnership-info p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.partnership-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.partnership-status.active {
    background: #d4edda;
    color: #155724;
}

.partnership-status.pending {
    background: #fff3cd;
    color: #856404;
}

.communication-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: #0c1e35;
    margin-bottom: 0.25rem;
}

.stat-label {
    display: block;
    color: #666;
    font-size: 0.9rem;
}

.network-features {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.feature-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    gap: 1rem;
}

.feature-icon {
    font-size: 1.5rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.feature-text {
    color: #0c1e35;
    font-weight: 500;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-align: center;
    border: none;
    cursor: pointer;
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
    background: linear-gradient(135deg, #6c757d, #545b62);
    color: #fff;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #545b62, #3d4142);
    transform: scale(1.05);
}

.btn-outline {
    background: transparent;
    color: #007bff;
    border: 2px solid #007bff;
}

.btn-outline:hover {
    background: #007bff;
    color: #fff;
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .connect-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .communication-stats {
        grid-template-columns: 1fr;
    }
}
@endsection
