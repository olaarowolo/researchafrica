@extends('layouts.admin')
@section('content')
    @can('editorial_workflow_create')
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.editorial-workflows.create') }}">
                    Create Editorial Workflow
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            Editorial Workflows
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-EditorialWorkflow">
                    <thead>
                        <tr>
                            <th width="10">
                            </th>
                            <th>
                                Name
                            </th>
                            <th>
                                Journal
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Stages
                            </th>
                            <th>
                                Status
                            </th>
                            <th>
                                Created At
                            </th>
                            <th>
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($editorialWorkflows as $editorialWorkflow)
                            <tr>
                                <td>
                                </td>
                                <td>
                                    {{ $editorialWorkflow->name }}
                                </td>
                                <td>
                                    {{ $editorialWorkflow->journal->name ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ Str::limit($editorialWorkflow->description, 50) }}
                                </td>
                                <td>
                                    {{ $editorialWorkflow->workflowStages->count() }}
                                </td>
                                <td>
                                    @if ($editorialWorkflow->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $editorialWorkflow->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    @can('editorial_workflow_show')
                                        <a class="btn btn-xs btn-primary"
                                            href="{{ route('admin.editorial-workflows.show', $editorialWorkflow->id) }}">
                                            View
                                        </a>
                                    @endcan
                                    @can('editorial_workflow_edit')
                                        <a class="btn btn-xs btn-info"
                                            href="{{ route('admin.editorial-workflows.edit', $editorialWorkflow->id) }}">
                                            Edit
                                        </a>
                                    @endcan
                                    @can('editorial_workflow_delete')
                                        <form action="{{ route('admin.editorial-workflows.destroy', $editorialWorkflow->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this workflow?')"
                                            style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <input type="submit" class="btn btn-xs btn-danger" value="Delete">
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(function() {
            $('.datatable-EditorialWorkflow:not(.ajaxTable)').DataTable({
                pageLength: 25,
                order: [
                    [6, 'desc']
                ]
            });
        });
    </script>
@endsection
