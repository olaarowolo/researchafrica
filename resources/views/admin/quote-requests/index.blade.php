@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quote Requests</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.home') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Search and Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search quote requests..." id="searchInput">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="quoted">Quoted</option>
                                <option value="accepted">Accepted</option>
                                <option value="rejected">Rejected</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered custom-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Client Name</th>
                                    <th>Email</th>
                                    <th>Service</th>
                                    <th>Location</th>
                                    <th>Word Count</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quoteRequests as $request)
                                    <tr data-status="{{ $request->status }}">
                                        <td>{{ $request->id }}</td>
                                        <td>{{ $request->name }}</td>
                                        <td>{{ $request->email }}</td>
                                        <td>{{ $request->service_type }}</td>
                                        <td>{{ $request->location }}</td>
                                        <td>{{ $request->word_count ? number_format($request->word_count) : 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $request->status_color }} status-badge">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group-vertical btn-group-sm" role="group">
                                                <a href="{{ route('admin.quote-requests.show', $request->id) }}"
                                                   class="btn btn-info mb-1">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <button type="button" class="btn btn-warning mb-1"
                                                        onclick="updateStatus({{ $request->id }}, 'quoted')">
                                                    <i class="fas fa-quote-right"></i> Quote
                                                </button>
                                                <button type="button" class="btn btn-success mb-1"
                                                        onclick="updateStatus({{ $request->id }}, 'accepted')">
                                                    <i class="fas fa-check"></i> Accept
                                                </button>
                                                <button type="button" class="btn btn-danger"
                                                        onclick="updateStatus({{ $request->id }}, 'rejected')">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox fa-3x text-muted"></i>
                                                <p class="text-muted mb-0">No quote requests found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($quoteRequests->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $quoteRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="statusModalLabel"><i class="fas fa-edit"></i> Update Quote Request Status</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status"><i class="fas fa-flag"></i> Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="quoted">Quoted</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estimated_cost"><i class="fas fa-pound-sign"></i> Estimated Cost (£)</label>
                        <input type="number" class="form-control" id="estimated_cost" name="estimated_cost" step="0.01" min="0" placeholder="Enter estimated cost">
                    </div>
                    <div class="form-group">
                        <label for="estimated_turnaround"><i class="fas fa-clock"></i> Estimated Turnaround</label>
                        <input type="text" class="form-control" id="estimated_turnaround" name="estimated_turnaround" placeholder="e.g., 3-5 business days">
                    </div>
                    <div class="form-group">
                        <label for="admin_notes"><i class="fas fa-sticky-note"></i> Admin Notes</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4" placeholder="Add any additional notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateStatus(requestId, newStatus) {
    document.getElementById('status').value = newStatus;
    document.getElementById('statusForm').action = `/admin/quote-requests/${requestId}/status`;
    $('#statusModal').modal('show');
}
</script>
@endsection
