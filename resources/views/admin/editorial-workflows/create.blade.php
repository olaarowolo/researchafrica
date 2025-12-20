@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            Create Editorial Workflow
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.editorial-workflows.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="name">Name</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                        id="name" value="{{ old('name', '') }}" required>
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
                            <option value="{{ $journal->id }}" {{ old('journal_id') == $journal->id ? 'selected' : '' }}>
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
                        id="description">{{ old('description') }}</textarea>
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
                        name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
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
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
