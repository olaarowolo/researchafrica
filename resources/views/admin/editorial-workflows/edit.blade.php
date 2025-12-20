@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            Edit Editorial Workflow
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.editorial-workflows.update', $editorialWorkflow->id) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label class="required" for="name">Name</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                        id="name" value="{{ old('name', $editorialWorkflow->name) }}" required>
                    @if ($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">The name of the editorial workflow</span>
                </div>
                <div class="form-group">
                    <label class="required" for="journal_id">Journal</label>
                    <select class="form-control select2 {{ $errors->has('journal_id') ? 'is-invalid' : '' }}"
                        name="journal_id" id="journal_id" required>
                        <option value="">Select Journal</option>
                        @foreach ($journals as $journal)
                            <option value="{{ $journal->id }}"
                                {{ old('journal_id', $editorialWorkflow->journal_id) == $journal->id ? 'selected' : '' }}>
                                {{ $journal->name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('journal_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('journal_id') }}
                        </div>
                    @endif
                    <span class="help-block">The journal this workflow belongs to</span>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description"
                        id="description">{{ old('description', $editorialWorkflow->description) }}</textarea>
                    @if ($errors->has('description'))
                        <div class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </div>
                    @endif
                    <span class="help-block">Optional description of the workflow</span>
                </div>
                <div class="form-group">
                    <label for="is_active">Active</label>
                    <input name="is_active" type="hidden" value="0">
                    <input class="form-check-input {{ $errors->has('is_active') ? 'is-invalid' : '' }}" type="checkbox"
                        name="is_active" id="is_active" value="1"
                        {{ old('is_active', $editorialWorkflow->is_active) ? 'checked' : '' }}>
                    @if ($errors->has('is_active'))
                        <div class="invalid-feedback">
                            {{ $errors->first('is_active') }}
                        </div>
                    @endif
                    <span class="help-block">Whether this workflow is active and can be assigned to articles</span>
                </div>
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Workflow Stages Section -->
    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <span>Workflow Stages</span>
                <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addStageModal">
                    Add Stage
                </button>
            </div>
        </div>

        <div class="card-body">
            @if ($editorialWorkflow->workflowStages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Deadline (Days)</th>
                                <th>Required Roles</th>
                                <th>Allowed Actions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="stagesTable">
                            @foreach ($editorialWorkflow->workflowStages->sortBy('stage_order') as $stage)
                                <tr data-stage-id="{{ $stage->id }}">
                                    <td>{{ $stage->stage_order }}</td>
                                    <td>{{ $stage->name }}</td>
                                    <td>{{ Str::limit($stage->description, 50) }}</td>
                                    <td>{{ $stage->deadline_days }}</td>
                                    <td>
                                        @if ($stage->required_roles)
                                            @foreach ($stage->required_roles as $roleId)
                                                <span
                                                    class="badge badge-info">{{ $memberTypes[$roleId] ?? 'Role ' . $roleId }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if ($stage->allowed_actions)
                                            @foreach ($stage->allowed_actions as $action)
                                                <span class="badge badge-secondary">{{ ucfirst($action) }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-xs btn-info edit-stage"
                                            data-stage-id="{{ $stage->id }}">Edit</button>
                                        <form action="{{ route('admin.editorial-workflow-stages.destroy', $stage->id) }}"
                                            method="POST" style="display: inline;"
                                            onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No stages defined yet. Add your first stage to get started.</p>
            @endif
        </div>
    </div>

    <!-- Add Stage Modal -->
    <div class="modal fade" id="addStageModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Workflow Stage</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="stageForm" action="{{ route('admin.editorial-workflows.stages.store', $editorialWorkflow->id) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="stage_name">Name</label>
                            <input type="text" class="form-control" name="name" id="stage_name" required>
                        </div>
                        <div class="form-group">
                            <label for="stage_description">Description</label>
                            <textarea class="form-control" name="description" id="stage_description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="stage_order">Order</label>
                            <input type="number" class="form-control" name="stage_order" id="stage_order"
                                value="{{ $editorialWorkflow->workflowStages->count() + 1 }}" required>
                        </div>
                        <div class="form-group">
                            <label for="deadline_days">Deadline (Days)</label>
                            <input type="number" class="form-control" name="deadline_days" id="deadline_days"
                                value="7" required>
                        </div>
                        <div class="form-group">
                            <label for="required_roles">Required Roles</label>
                            <select class="form-control select2" name="required_roles[]" id="required_roles" multiple>
                                @foreach ($memberTypes as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="allowed_actions">Allowed Actions</label>
                            <select class="form-control" name="allowed_actions[]" id="allowed_actions" multiple>
                                <option value="submit">Submit</option>
                                <option value="approve">Approve</option>
                                <option value="reject">Reject</option>
                                <option value="request_revision">Request Revision</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Stage</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Stage Modal -->
    <div class="modal fade" id="editStageModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Workflow Stage</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="editStageForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_stage_id" name="stage_id">
                        <div class="form-group">
                            <label for="edit_stage_name">Name</label>
                            <input type="text" class="form-control" name="name" id="edit_stage_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_stage_description">Description</label>
                            <textarea class="form-control" name="description" id="edit_stage_description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_stage_order">Order</label>
                            <input type="number" class="form-control" name="stage_order" id="edit_stage_order"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="edit_deadline_days">Deadline (Days)</label>
                            <input type="number" class="form-control" name="deadline_days" id="edit_deadline_days"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="edit_required_roles">Required Roles</label>
                            <select class="form-control select2" name="required_roles[]" id="edit_required_roles"
                                multiple>
                                @foreach ($memberTypes as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_allowed_actions">Allowed Actions</label>
                            <select class="form-control" name="allowed_actions[]" id="edit_allowed_actions" multiple>
                                <option value="submit">Submit</option>
                                <option value="approve">Approve</option>
                                <option value="reject">Reject</option>
                                <option value="request_revision">Request Revision</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Stage</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            // Edit stage functionality
            $('.edit-stage').on('click', function() {
                var stageId = $(this).data('stage-id');
                var row = $('tr[data-stage-id="' + stageId + '"]');

                $('#edit_stage_id').val(stageId);
                $('#edit_stage_name').val(row.find('td:nth-child(2)').text().trim());
                $('#edit_stage_description').val(row.find('td:nth-child(3)').text().trim());
                $('#edit_stage_order').val(row.find('td:nth-child(1)').text().trim());
                $('#edit_deadline_days').val(row.find('td:nth-child(4)').text().trim());

                // Set required roles (this would need AJAX call in real implementation)
                // For now, we'll leave it empty and let the backend handle it

                $('#editStageForm').attr('action',
                    '{{ route('admin.editorial-workflow-stages.update', ':id') }}'.replace(':id',
                        stageId));
                $('#editStageModal').modal('show');
            });
        });
    </script>
@endsection
