@extends('layouts.profile')
@section('page-name', 'My Articles in Editorial Workflows')

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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>My Articles in Editorial Workflows</h4>
            <a href="{{ route('member.articles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Submit New Article
            </a>
        </div>

        @if ($articles->count() > 0)
            <div class="row">
                @foreach ($articles as $article)
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="card-title">{{ $article->title }}</h5>
                                        <div class="mb-2">
                                            <span
                                                class="badge badge-secondary">{{ $article->article_category->category_name ?? 'N/A' }}</span>
                                            <small class="text-muted ml-2">
                                                Submitted: {{ $article->created_at->format('M d, Y') }}
                                            </small>
                                        </div>

                                        <!-- Workflow Progress -->
                                        @if ($article->editorialProgress)
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted">Workflow Progress</small>
                                                    <span
                                                        class="badge badge-{{ $this->getStatusBadgeClass($article->editorialProgress->status) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $article->editorialProgress->status)) }}
                                                    </span>
                                                </div>

                                                <div class="progress mb-2" style="height: 8px;">
                                                    @php
                                                        $totalStages = $article->editorialProgress->editorialWorkflow->workflowStages->count();
                                                        $currentOrder =
                                                            $article->editorialProgress->currentStage->stage_order ?? 0;
                                                        $progressPercent =
                                                            $totalStages > 0 ? ($currentOrder / $totalStages) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-{{ $article->editorialProgress->status === 'published' ? 'success' : 'primary' }}"
                                                        style="width: {{ $progressPercent }}%"></div>
                                                </div>

                                                <div class="row text-center">
                                                    <div class="col">
                                                        <small class="text-muted">Current Stage</small>
                                                        <br>
                                                        <strong>{{ $article->editorialProgress->currentStage->name ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="col">
                                                        <small class="text-muted">Deadline</small>
                                                        <br>
                                                        <strong
                                                            class="{{ $article->editorialProgress->current_stage_deadline && $article->editorialProgress->current_stage_deadline->isPast() ? 'text-danger' : '' }}">
                                                            {{ $article->editorialProgress->current_stage_deadline ? $article->editorialProgress->current_stage_deadline->format('M d, Y') : 'N/A' }}
                                                        </strong>
                                                    </div>
                                                    <div class="col">
                                                        <small class="text-muted">Workflow</small>
                                                        <br>
                                                        <strong>{{ $article->editorialProgress->editorialWorkflow->name }}</strong>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="mb-2">
                                                @if ($article->editorialProgress->status === 'draft')
                                                    <form method="POST"
                                                        action="{{ route('member.articles.submit-for-review', $article->id) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-paper-plane"></i> Submit for Review
                                                        </button>
                                                    </form>
                                                @elseif($article->editorialProgress->status === 'revision_requested')
                                                    <button class="btn btn-sm btn-warning"
                                                        onclick="showRevisionModal({{ $article->id }}, '{{ addslashes($article->editorialProgress->current_comments ?? '') }}')">
                                                        <i class="fas fa-edit"></i> Address Revision Request
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                This article is not yet assigned to an editorial workflow.
                                                <a href="{{ route('member.articles.show', $article->id) }}"
                                                    class="alert-link">View Details</a>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-4 text-right">
                                        <a href="{{ route('member.articles.show', $article->id) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <a href="{{ route('member.articles.edit', $article->id) }}"
                                            class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $articles->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5>No Articles in Editorial Workflows</h5>
                    <p class="text-muted">You haven't submitted any articles to editorial workflows yet.</p>
                    <a href="{{ route('member.articles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Submit Your First Article
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Revision Modal -->
    <div class="modal fade" id="revisionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Address Revision Request</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="revisionForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Revision Comments:</label>
                            <div id="revisionComments" class="alert alert-info"></div>
                        </div>
                        <div class="form-group">
                            <label for="revision_response">Your Response:</label>
                            <textarea class="form-control" name="comments" id="revision_response" rows="4"
                                placeholder="Explain how you've addressed the revision request..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit Revision</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        function showRevisionModal(articleId, comments) {
            $('#revisionComments').html(comments.replace(/\n/g, '<br>'));
            $('#revisionForm').attr('action', '{{ route('member.articles.submit-for-review', ':id') }}'.replace(':id',
                articleId));
            $('#revisionModal').modal('show');
        }
    </script>

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
                'revision_requested' => 'warning',
                default => 'secondary',
            };
        }
    @endphp
@endsection
