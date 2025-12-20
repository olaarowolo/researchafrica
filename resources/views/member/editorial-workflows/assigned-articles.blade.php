@extends('layouts.profile')
@section('page-name', 'Articles Assigned to Me')

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
            <h4>Articles Assigned for Review</h4>
            <span class="badge badge-info">{{ $assignedArticles->total() }} articles</span>
        </div>

        @if ($assignedArticles->count() > 0)
            <div class="row">
                @foreach ($assignedArticles as $progress)
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="card-title">{{ $progress->article->title }}</h5>
                                        <div class="mb-2">
                                            <span
                                                class="badge badge-secondary">{{ $progress->article->article_category->category_name ?? 'N/A' }}</span>
                                            <small class="text-muted ml-2">
                                                Author: {{ $progress->article->author_name }} |
                                                Submitted: {{ $progress->article->created_at->format('M d, Y') }}
                                            </small>
                                        </div>

                                        <!-- Workflow Information -->
                                        <div class="mb-3">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <small class="text-muted">Workflow</small>
                                                    <br>
                                                    <strong>{{ $progress->editorialWorkflow->name }}</strong>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted">Current Stage</small>
                                                    <br>
                                                    <strong>{{ $progress->currentStage->name ?? 'N/A' }}</strong>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted">Deadline</small>
                                                    <br>
                                                    <strong
                                                        class="{{ $progress->current_stage_deadline && $progress->current_stage_deadline->isPast() ? 'text-danger' : '' }}">
                                                        {{ $progress->current_stage_deadline ? $progress->current_stage_deadline->format('M d, Y') : 'N/A' }}
                                                        @if ($progress->current_stage_deadline && $progress->current_stage_deadline->isPast())
                                                            <small class="text-danger">(Overdue)</small>
                                                        @endif
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stage Description -->
                                        @if ($progress->currentStage && $progress->currentStage->description)
                                            <div class="mb-3">
                                                <small class="text-muted">Stage Requirements:</small>
                                                <p class="mb-1">{{ $progress->currentStage->description }}</p>
                                            </div>
                                        @endif

                                        <!-- Available Actions -->
                                        <div class="mb-2">
                                            <small class="text-muted">Available Actions:</small>
                                            <div class="mt-1">
                                                @if ($progress->currentStage && in_array('approve', $progress->currentStage->allowed_actions ?? []))
                                                    <button class="btn btn-sm btn-success"
                                                        onclick="showActionModal({{ $progress->article->id }}, 'approve', '{{ $progress->currentStage->name }}')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                @endif
                                                @if ($progress->currentStage && in_array('reject', $progress->currentStage->allowed_actions ?? []))
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="showActionModal({{ $progress->article->id }}, 'reject', '{{ $progress->currentStage->name }}')">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                @endif
                                                @if ($progress->currentStage && in_array('request_revision', $progress->currentStage->allowed_actions ?? []))
                                                    <button class="btn btn-sm btn-warning"
                                                        onclick="showActionModal({{ $progress->article->id }}, 'request_revision', '{{ $progress->currentStage->name }}')">
                                                        <i class="fas fa-edit"></i> Request Revision
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 text-right">
                                        <a href="{{ route('member.articles.show', $progress->article->id) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Article
                                        </a>
                                        @if ($progress->article->file_path)
                                            <a href="{{ Storage::url($progress->article->file_path) }}" target="_blank"
                                                class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $assignedArticles->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                    <h5>No Articles Assigned</h5>
                    <p class="text-muted">You don't have any articles assigned for review at this time.</p>
                    <a href="{{ route('member.editorial-workflows.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-home"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Action Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionModalTitle">Perform Action</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="actionForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="action_comments">Comments (Required for reject and request revision):</label>
                            <textarea class="form-control" name="comments" id="action_comments" rows="4"
                                placeholder="Provide detailed feedback..."></textarea>
                        </div>
                        <div id="actionDescription" class="alert alert-info">
                            You are about to perform an action on this article.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="actionButton">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        function showActionModal(articleId, action, stageName) {
            let title, description, buttonClass, buttonText, route;

            switch (action) {
                case 'approve':
                    title = 'Approve Article';
                    description =
                        `You are approving this article at the "${stageName}" stage. This will move it to the next stage in the workflow.`;
                    buttonClass = 'btn-success';
                    buttonText = 'Approve Article';
                    route = '{{ route('member.articles.approve-stage', ':id') }}'.replace(':id', articleId);
                    $('#action_comments').closest('.form-group').hide();
                    break;
                case 'reject':
                    title = 'Reject Article';
                    description =
                        `You are rejecting this article at the "${stageName}" stage. This will end the editorial process for this article.`;
                    buttonClass = 'btn-danger';
                    buttonText = 'Reject Article';
                    route = '{{ route('member.articles.reject-stage', ':id') }}'.replace(':id', articleId);
                    $('#action_comments').closest('.form-group').show();
                    break;
                case 'request_revision':
                    title = 'Request Revision';
                    description =
                        `You are requesting revisions for this article at the "${stageName}" stage. The author will need to address your feedback before proceeding.`;
                    buttonClass = 'btn-warning';
                    buttonText = 'Request Revision';
                    route = '{{ route('member.articles.request-revision', ':id') }}'.replace(':id', articleId);
                    $('#action_comments').closest('.form-group').show();
                    break;
            }

            $('#actionModalTitle').text(title);
            $('#actionDescription').text(description);
            $('#actionButton').removeClass().addClass('btn ' + buttonClass).text(buttonText);
            $('#actionForm').attr('action', route);
            $('#action_comments').val('');

            $('#actionModal').modal('show');
        }
    </script>
@endsection
