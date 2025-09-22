@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quote Request #{{ $quoteRequest->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.quote-requests.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        @if($quoteRequest->file_path)
                            <a href="{{ route('admin.quote-requests.download', $quoteRequest->id) }}"
                               class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i> Download File
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <h4>Client Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Name:</th>
                                    <td>{{ $quoteRequest->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $quoteRequest->email }}</td>
                                </tr>
                                <tr>
                                    <th>Service:</th>
                                    <td>{{ $quoteRequest->ra_service }}</td>
                                </tr>
                                <tr>
                                    <th>Product Type:</th>
                                    <td>{{ $quoteRequest->product }}</td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td>{{ $quoteRequest->location }}</td>
                                </tr>
                                <tr>
                                    <th>Service Type:</th>
                                    <td>{{ $quoteRequest->service_type }}</td>
                                </tr>
                                @if($quoteRequest->word_count)
                                <tr>
                                    <th>Word Count:</th>
                                    <td>{{ number_format($quoteRequest->word_count) }}</td>
                                </tr>
                                @endif
                                @if($quoteRequest->addons)
                                <tr>
                                    <th>Add-ons:</th>
                                    <td>{{ implode(', ', json_decode($quoteRequest->addons, true)) }}</td>
                                </tr>
                                @endif
                                @if($quoteRequest->referral)
                                <tr>
                                    <th>Referral Code:</th>
                                    <td>{{ $quoteRequest->referral }}</td>
                                </tr>
                                @endif
                                @if($quoteRequest->message)
                                <tr>
                                    <th>Additional Notes:</th>
                                    <td>{{ $quoteRequest->message }}</td>
                                </tr>
                                @endif
                                @if($quoteRequest->original_filename)
                                <tr>
                                    <th>Attached File:</th>
                                    <td>{{ $quoteRequest->original_filename }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        <div class="col-md-4">
                            <h4>Status & Timeline</h4>
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Current Status:</label>
                                        <div>
                                            <span class="badge badge-{{ $quoteRequest->status_color }} badge-lg">
                                                {{ ucfirst($quoteRequest->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($quoteRequest->estimated_cost)
                                    <div class="form-group">
                                        <label>Estimated Cost:</label>
                                        <div class="text-success font-weight-bold">
                                            £{{ number_format($quoteRequest->estimated_cost, 2) }}
                                        </div>
                                    </div>
                                    @endif

                                    @if($quoteRequest->estimated_turnaround)
                                    <div class="form-group">
                                        <label>Estimated Turnaround:</label>
                                        <div>{{ $quoteRequest->estimated_turnaround }}</div>
                                    </div>
                                    @endif

                                    <div class="form-group">
                                        <label>Submitted:</label>
                                        <div>{{ $quoteRequest->created_at->format('M d, Y g:i A') }}</div>
                                    </div>

                                    @if($quoteRequest->quoted_at)
                                    <div class="form-group">
                                        <label>Quoted:</label>
                                        <div>{{ $quoteRequest->quoted_at->format('M d, Y g:i A') }}</div>
                                    </div>
                                    @endif

                                    @if($quoteRequest->accepted_at)
                                    <div class="form-group">
                                        <label>Accepted:</label>
                                        <div>{{ $quoteRequest->accepted_at->format('M d, Y g:i A') }}</div>
                                    </div>
                                    @endif

                                    @if($quoteRequest->completed_at)
                                    <div class="form-group">
                                        <label>Completed:</label>
                                        <div>{{ $quoteRequest->completed_at->format('M d, Y g:i A') }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <h4>Update Status</h4>
                            <form method="POST" action="{{ route('admin.quote-requests.update-status', $quoteRequest->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="pending" {{ $quoteRequest->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="quoted" {{ $quoteRequest->status == 'quoted' ? 'selected' : '' }}>Quoted</option>
                                        <option value="accepted" {{ $quoteRequest->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                        <option value="rejected" {{ $quoteRequest->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="completed" {{ $quoteRequest->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="estimated_cost">Estimated Cost (£)</label>
                                    <input type="number" class="form-control" id="estimated_cost" name="estimated_cost"
                                           value="{{ $quoteRequest->estimated_cost }}" step="0.01" min="0">
                                </div>
                                <div class="form-group">
                                    <label for="estimated_turnaround">Estimated Turnaround</label>
                                    <input type="text" class="form-control" id="estimated_turnaround" name="estimated_turnaround"
                                           value="{{ $quoteRequest->estimated_turnaround }}" placeholder="e.g., 3-5 business days">
                                </div>
                                <div class="form-group">
                                    <label for="admin_notes">Admin Notes</label>
                                    <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3">{{ $quoteRequest->admin_notes }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-save"></i> Update Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
