@extends('afriscribe.layouts.dashboard')

@section('page_title', 'AfriScribe Archive')

@section('page_description', 'Access archived publications and documents on the AfriScribe platform')

@section('dashboard_title', 'Document Archive')

@section('dashboard_subtitle', 'Manage and access your archived academic documents')

@section('dashboard_content')
<div class="archive-container">
    <div class="archive-header">
        <div class="search-section">
            <input type="text" placeholder="Search archived documents..." class="search-input">
            <button class="search-btn">🔍 Search</button>
        </div>
        <div class="filter-section">
            <select class="filter-select">
                <option value="">All Categories</option>
                <option value="manuscripts">Manuscripts</option>
                <option value="proofreading">Proofreading</option>
                <option value="published">Published Works</option>
                <option value="research">Research Papers</option>
            </select>
            <select class="filter-select">
                <option value="">All Years</option>
                <option value="2024">2024</option>
                <option value="2023">2023</option>
                <option value="2022">2022</option>
                <option value="2021">2021</option>
            </select>
        </div>
    </div>

    <div class="archive-grid">
        <div class="archive-card">
            <div class="card-header">
                <div class="document-icon">📄</div>
                <div class="document-info">
                    <h4>Research Paper Title</h4>
                    <p>Category: Research Papers</p>
                    <span class="document-date">Archived: Jan 15, 2024</span>
                </div>
            </div>
            <div class="card-body">
                <p>A comprehensive research paper on academic publishing trends...</p>
                <div class="document-meta">
                    <span class="meta-item">📎 PDF</span>
                    <span class="meta-item">👤 Author Name</span>
                    <span class="meta-item">📊 25 pages</span>
                </div>
            </div>
            <div class="card-actions">
                <button class="btn btn-view">👁️ View</button>
                <button class="btn btn-download">⬇️ Download</button>
            </div>
        </div>

        <div class="archive-card">
            <div class="card-header">
                <div class="document-icon">📝</div>
                <div class="document-info">
                    <h4>Manuscript Draft</h4>
                    <p>Category: Manuscripts</p>
                    <span class="document-date">Archived: Dec 20, 2023</span>
                </div>
            </div>
            <div class="card-body">
                <p>Draft manuscript for journal submission with peer review notes...</p>
                <div class="document-meta">
                    <span class="meta-item">📎 DOCX</span>
                    <span class="meta-item">👤 Co-authors: 3</span>
                    <span class="meta-item">📊 18 pages</span>
                </div>
            </div>
            <div class="card-actions">
                <button class="btn btn-view">👁️ View</button>
                <button class="btn btn-download">⬇️ Download</button>
            </div>
        </div>

        <div class="archive-card">
            <div class="card-header">
                <div class="document-icon">✏️</div>
                <div class="document-info">
                    <h4>Proofreading Project</h4>
                    <p>Category: Proofreading</p>
                    <span class="document-date">Archived: Nov 10, 2023</span>
                </div>
            </div>
            <div class="card-body">
                <p>Completed proofreading project with final corrections and feedback...</p>
                <div class="document-meta">
                    <span class="meta-item">📎 PDF</span>
                    <span class="meta-item">👤 Client: University Press</span>
                    <span class="meta-item">📊 45 pages</span>
                </div>
            </div>
            <div class="card-actions">
                <button class="btn btn-view">👁️ View</button>
                <button class="btn btn-download">⬇️ Download</button>
            </div>
        </div>
    </div>

    <div class="archive-stats">
        <div class="stat-card">
            <h3>Total Documents</h3>
            <span class="stat-number">0</span>
        </div>
        <div class="stat-card">
            <h3>Storage Used</h3>
            <span class="stat-number">0 MB</span>
        </div>
        <div class="stat-card">
            <h3>This Month</h3>
            <span class="stat-number">0</span>
        </div>
    </div>
</div>
@endsection

@section('dashboard_styles')
.archive-container {
    padding: 2rem 0;
}

.archive-header {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.search-section {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.search-input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 25px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #007bff;
}

.search-btn {
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.3s ease;
}

.search-btn:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
}

.filter-section {
    display: flex;
    gap: 1rem;
}

.filter-select {
    padding: 0.75rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 25px;
    background: #fff;
    font-size: 1rem;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: #007bff;
}

.archive-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.archive-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.archive-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.card-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.document-icon {
    font-size: 3rem;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.document-info {
    flex: 1;
}

.document-info h4 {
    margin: 0 0 0.5rem 0;
    color: #0c1e35;
    font-weight: 600;
}

.document-info p {
    margin: 0 0 0.25rem 0;
    color: #666;
    font-size: 0.9rem;
}

.document-date {
    color: #999;
    font-size: 0.8rem;
}

.card-body {
    padding: 1.5rem;
}

.card-body p {
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.document-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}

.meta-item {
    background: #f8f9fa;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    color: #666;
}

.card-actions {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-view {
    background: linear-gradient(135deg, #28a745, #20863a);
    color: #fff;
}

.btn-view:hover {
    background: linear-gradient(135deg, #20863a, #1e6b30);
    transform: scale(1.05);
}

.btn-download {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
}

.btn-download:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: scale(1.05);
}

.archive-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, #0c1e35, #1a2e47);
    color: #fff;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.stat-card h3 {
    margin: 0 0 1rem 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    display: block;
}

@media (max-width: 768px) {
    .archive-header {
        padding: 1.5rem;
    }

    .search-section {
        flex-direction: column;
    }

    .filter-section {
        flex-direction: column;
    }

    .archive-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .card-actions {
        flex-direction: column;
    }

    .archive-stats {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}
@endsection
