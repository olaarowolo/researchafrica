@extends('afriscribe.layouts.dashboard')

@section('page_title', 'AfriScribe Editor')

@section('page_description', 'Advanced document editing tools for academic content on AfriScribe')

@section('dashboard_title', 'Document Editor')

@section('dashboard_subtitle', 'Create and edit academic documents with powerful tools')

@section('dashboard_content')
<div class="editor-container">
    <div class="editor-header">
        <div class="document-info">
            <h2>Untitled Document</h2>
            <span class="document-status">Draft</span>
        </div>
        <div class="editor-actions">
            <button class="btn btn-secondary">💾 Save Draft</button>
            <button class="btn btn-primary">📤 Export</button>
            <button class="btn btn-success">🚀 Publish</button>
        </div>
    </div>

    <div class="editor-toolbar">
        <div class="toolbar-section">
            <button class="tool-btn" title="Bold">**B**</button>
            <button class="tool-btn" title="Italic">*I*</button>
            <button class="tool-btn" title="Underline">U</button>
            <div class="divider"></div>
            <button class="tool-btn" title="Heading 1">H1</button>
            <button class="tool-btn" title="Heading 2">H2</button>
            <button class="tool-btn" title="Heading 3">H3</button>
            <div class="divider"></div>
            <button class="tool-btn" title="Bullet List">•</button>
            <button class="tool-btn" title="Numbered List">1.</button>
            <button class="tool-btn" title="Quote">"</button>
        </div>
        <div class="toolbar-section">
            <button class="tool-btn" title="Insert Image">🖼️</button>
            <button class="tool-btn" title="Insert Table">📋</button>
            <button class="tool-btn" title="Insert Link">🔗</button>
            <button class="tool-btn" title="Insert Code">💻</button>
        </div>
    </div>

    <div class="editor-content">
        <div class="editor-sidebar">
            <div class="sidebar-section">
                <h4>📝 Recent Documents</h4>
                <div class="document-list">
                    <div class="document-item">
                        <span class="document-title">Research Paper Draft</span>
                        <span class="document-date">2 hours ago</span>
                    </div>
                    <div class="document-item">
                        <span class="document-title">Literature Review</span>
                        <span class="document-date">1 day ago</span>
                    </div>
                    <div class="document-item">
                        <span class="document-title">Methodology Notes</span>
                        <span class="document-date">3 days ago</span>
                    </div>
                </div>
            </div>

            <div class="sidebar-section">
                <h4>🔧 Tools</h4>
                <div class="tool-list">
                    <button class="tool-item">📊 Word Count</button>
                    <button class="tool-item">🔍 Find & Replace</button>
                    <button class="tool-item">📈 Readability</button>
                    <button class="tool-item">🎯 Grammar Check</button>
                </div>
            </div>

            <div class="sidebar-section">
                <h4>📚 Templates</h4>
                <div class="template-list">
                    <button class="template-item">Research Paper</button>
                    <button class="template-item">Review Article</button>
                    <button class="template-item">Case Study</button>
                    <button class="template-item">Thesis Chapter</button>
                </div>
            </div>
        </div>

        <div class="editor-main">
            <div class="editor-canvas">
                <div class="editor-placeholder">
                    <div class="placeholder-icon">✏️</div>
                    <h3>Start Writing Your Document</h3>
                    <p>Choose a template from the sidebar or start with a blank document</p>
                    <button class="btn btn-primary">Create New Document</button>
                </div>
            </div>
        </div>
    </div>

    <div class="editor-footer">
        <div class="word-count">
            <span>Words: 0</span>
            <span>Characters: 0</span>
            <span>Reading Time: 0 min</span>
        </div>
        <div class="auto-save">
            <span class="save-status">💾 Auto-saving...</span>
        </div>
    </div>
</div>
@endsection

@section('dashboard_styles')
.editor-container {
    height: calc(100vh - 200px);
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.editor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #dee2e6;
}

.document-info h2 {
    margin: 0;
    color: #0c1e35;
    font-size: 1.5rem;
    font-weight: 600;
}

.document-status {
    background: #ffc107;
    color: #212529;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.editor-actions {
    display: flex;
    gap: 1rem;
}

.editor-toolbar {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 2rem;
    background: #fff;
    border-bottom: 1px solid #dee2e6;
}

.toolbar-section {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tool-btn {
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    background: #fff;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    min-width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.tool-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
}

.divider {
    width: 1px;
    height: 20px;
    background: #dee2e6;
    margin: 0 0.5rem;
}

.editor-content {
    display: flex;
    flex: 1;
    overflow: hidden;
}

.editor-sidebar {
    width: 250px;
    background: #f8f9fa;
    border-right: 1px solid #dee2e6;
    padding: 1rem;
    overflow-y: auto;
}

.sidebar-section {
    margin-bottom: 2rem;
}

.sidebar-section h4 {
    margin: 0 0 1rem 0;
    color: #0c1e35;
    font-size: 1rem;
    font-weight: 600;
}

.document-list, .tool-list, .template-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.document-item, .tool-item, .template-item {
    padding: 0.75rem;
    background: #fff;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.document-item:hover, .tool-item:hover, .template-item:hover {
    background: #e9ecef;
    border-color: #007bff;
}

.document-title {
    display: block;
    color: #0c1e35;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.document-date {
    display: block;
    color: #666;
    font-size: 0.8rem;
}

.tool-item, .template-item {
    text-align: center;
    font-size: 0.9rem;
    color: #0c1e35;
}

.editor-main {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.editor-canvas {
    flex: 1;
    padding: 2rem;
    overflow-y: auto;
    background: #fff;
}

.editor-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    text-align: center;
    color: #666;
}

.placeholder-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.placeholder-icon h3 {
    margin: 0 0 1rem 0;
    color: #0c1e35;
    font-size: 1.5rem;
}

.placeholder-icon p {
    margin: 0 0 2rem 0;
    font-size: 1.1rem;
    max-width: 400px;
}

.editor-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.word-count {
    display: flex;
    gap: 2rem;
    color: #666;
    font-size: 0.9rem;
}

.save-status {
    color: #28a745;
    font-size: 0.9rem;
    font-weight: 500;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
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

.btn-success {
    background: linear-gradient(135deg, #28a745, #20863a);
    color: #fff;
}

.btn-success:hover {
    background: linear-gradient(135deg, #20863a, #1e6b30);
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .editor-header {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem;
    }

    .editor-actions {
        flex-wrap: wrap;
        justify-content: center;
    }

    .editor-toolbar {
        padding: 0.5rem 1rem;
        flex-wrap: wrap;
    }

    .editor-content {
        flex-direction: column;
    }

    .editor-sidebar {
        width: 100%;
        max-height: 200px;
    }

    .editor-canvas {
        padding: 1rem;
    }

    .editor-footer {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem;
    }

    .word-count {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
}
@endsection
