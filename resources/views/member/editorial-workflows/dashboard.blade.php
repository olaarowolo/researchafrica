@extends('layouts.profile')
@section('page-name', 'Editorial Workflow Dashboard')

@section('content')
    <div id="content" class="p-4 p-md-5">
        <x-profile-bar />

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger my-2">{{ $error }}</div>
            @endforeach
        @endif

        @if (session('success'))
            <div class="alert alert-success my-2">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger my-2">{{ session('error') }}</div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">My Articles</h5>
                        <h2>{{ $stats['my_articles_count'] }}</h2>
                        <p class="mb-0">In editorial workflows</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Assigned to Review</h5>
                        <h2>{{ $stats['assigned_articles_count'] }}</h2>
                        <p class="mb-0">Articles to review</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Published</h5>
                        <h2>{{ $stats['published_count'] }}</h2>
                        <p class="mb-0">Successfully published</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Under Review</h5>
                        <h2>{{ $stats['under_review_count'] }}</h2>
                        <p class="mb-0">Currently being reviewed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="{{ route('member.editorial-workflows.my-articles') }}"
                                    class="btn btn-primary btn-block">
                                    <i class="fas fa-file-alt"></i> My Articles
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('member.editorial-workflows.assigned-articles') }}"
                                    class="btn btn-warning btn-block">
                                    <i class="fas fa-tasks"></i> Assigned Articles
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('member.articles.create') }}" class="btn btn-success btn-block">
                                    <i class="fas fa-plus"></i> Submit New Article
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('member.articles.index') }}" class="btn btn-info btn-block">
                                    <i class="fas fa-list"></i> All My Articles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Articles in Workflow -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>My Articles in Workflow</h5>
                        <a href="{{ route('member.editorial-workflows.my-articles') }}" class="btn btn-sm btn-primary">View
                            All</a>
                    </div>
                    <div class="card-body">
                        @if ($myArticles->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($myArticles->take(5) as $article)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($article->title, 50) }}</h6>
                                                <small class="text-muted">
                                                    Stage: {{ $article->editorialProgress->currentStage->name ?? 'N/A' }} |
                                                    Status: <span
                                                        class="badge badge-{{ $this->getStatusBadgeClass($article->editorialProgress->status) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $article->editorialProgress->status)) }}
                                                    </span>
                                                </small>
                                            </div>
                                            <a href="{{ route('member.articles.show', $article->id) }}"
                                                class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No articles in editorial workflows yet.</p>
                            <a href="{{ route('member.articles.create') }}" class="btn btn-sm btn-primary mt-2">Submit Your
                                First Article</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Assigned Articles -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Articles Assigned to Me</h5>
                        <a href="{{ route('member.editorial-workflows.assigned-articles') }}"
                            class="btn btn-sm btn-warning">View All</a>
                    </div>
                    <div class="card-body">
                        @if ($assignedArticles->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($assignedArticles->take(5) as $progress)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($progress->article->title, 50) }}</h6>
                                                <small class="text-muted">
                                                    Stage: {{ $progress->currentStage->name ?? 'N/A' }} |
                                                    Deadline:
                                                    {{ $progress->current_stage_deadline ? $progress->current_stage_deadline->format('M d, Y') : 'N/A' }}
                                                </small>
                                            </div>
                                            <a href="{{ route('member.articles.show', $progress->article->id) }}"
                                                class="btn btn-sm btn-outline-warning">Review</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No articles assigned for review.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@php
    function getStatusBadgeClass($status)
    {
        return match ($status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'under_review' => 'warning',
            'approved' => 'success',
            'published' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }
@endphp
