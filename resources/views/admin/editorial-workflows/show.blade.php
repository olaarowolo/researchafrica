@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <span>Editorial Workflow: {{ $editorialWorkflow->name }}</span>
                <a href="{{ route('admin.editorial-workflows.edit', $editorialWorkflow->id) }}" class="btn btn-primary">Edit
                    Workflow</a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Workflow Details</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $editorialWorkflow->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Journal:</strong></td>
                            <td>{{ $editorialWorkflow->journal->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Description:</strong></td>
                            <td>{{ $editorialWorkflow->description ?: 'No description' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @if ($editorialWorkflow->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td>{{ $editorialWorkflow->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Statistics</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Total Stages:</strong></td>
                            <td>{{ $editorialWorkflow->workflowStages->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Articles in Progress:</strong></td>
                            <td>{{ $editorialWorkflow->articleProgress->where('status', '!=', 'published')->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Completed Articles:</strong></td>
                            <td>{{ $editorialWorkflow->articleProgress->where('status', 'published')->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Overdue Items:</strong></td>
                            <td>
                                <span class="badge badge-danger">
                                    {{ $editorialWorkflow->articleProgress->filter(function ($progress) {
                                            return $progress->current_stage_deadline &&
                                                $progress->current_stage_deadline->isPast() &&
                                                $progress->status === 'under_review';
                                        })->count() }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Workflow Stages -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Workflow Stages</h5>
        </div>

        <div class="card-body">
            @if ($editorialWorkflow->workflowStages->count() > 0)
                <div class="row">
                    @foreach ($editorialWorkflow->workflowStages->sortBy('stage_order') as $stage)
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        Stage {{ $stage->stage_order }}: {{ $stage->name }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small">{{ $stage->description ?: 'No description' }}</p>
                                    <div class="mb-2">
                                        <strong>Deadline:</strong> {{ $stage->deadline_days }} days
                                    </div>
                                    @if ($stage->required_roles)
                                        <div class="mb-2">
                                            <strong>Required Roles:</strong>
                                            @foreach ($stage->required_roles as $roleId)
                                                <span
                                                    class="badge badge-info mr-1">{{ $memberTypes[$roleId] ?? 'Role ' . $roleId }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if ($stage->allowed_actions)
                                        <div class="mb-2">
                                            <strong>Actions:</strong>
                                            @foreach ($stage->allowed_actions as $action)
                                                <span
                                                    class="badge badge-secondary mr-1">{{ ucfirst(str_replace('_', ' ', $action)) }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="text-muted small">
                                        Articles in this stage: {{ $stage->articleProgress->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">No stages defined for this workflow.</p>
            @endif
        </div>
    </div>

    <!-- Articles in Workflow -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Articles in This Workflow</h5>
        </div>

        <div class="card-body">
            @if ($editorialWorkflow->articleProgress->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Article Title</th>
                                <th>Author</th>
                                <th>Current Stage</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($editorialWorkflow->articleProgress as $progress)
                                <tr>
                                    <td>{{ $progress->article->title }}</td>
                                    <td>{{ $progress->article->author_name }}</td>
                                    <td>{{ $progress->currentStage->name ?? 'N/A' }}</td>
                                    <td>
                                        @switch($progress->status)
                                            @case('draft')
                                                <span class="badge badge-secondary">Draft</span>
                                            @break

                                            @case('submitted')
                                                <span class="badge badge-info">Submitted</span>
                                            @break

                                            @case('under_review')
                                                <span class="badge badge-warning">Under Review</span>
                                            @break

                                            @case('approved')
                                                <span class="badge badge-success">Approved</span>
                                            @break

                                            @case('published')
                                                <span class="badge badge-success">Published</span>
                                            @break

                                            @case('rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @break

                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($progress->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if ($progress->current_stage_deadline)
                                            @if ($progress->current_stage_deadline->isPast() && $progress->status === 'under_review')
                                                <span
                                                    class="text-danger">{{ $progress->current_stage_deadline->format('M d, Y') }}</span>
                                            @else
                                                {{ $progress->current_stage_deadline->format('M d, Y') }}
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.articles.show', $progress->article_id) }}"
                                            class="btn btn-xs btn-primary">View Article</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No articles are currently assigned to this workflow.</p>
            @endif
        </div>
    </div>

    <!-- Assign Article to Workflow -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Assign Article to Workflow</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.editorial-workflows.assign-article', $editorialWorkflow->id) }}">
                @csrf
                <div class="form-group">
                    <label for="article_id">Select Article</label>
                    <select class="form-control select2" name="article_id" id="article_id" required>
                        <option value="">Choose an article...</option>
                        @foreach ($availableArticles as $article)
                            <option value="{{ $article->id }}">{{ $article->title }} (by {{ $article->author_name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Assign to Workflow</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
