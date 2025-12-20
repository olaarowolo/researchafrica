@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        Submit a new article to {{ $currentJournal->name }}
    </div>

    <div class="card-body">
        <form action="{{ route('articles.store', ['acronym' => $currentJournal->journal_acronym]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="title">Title*</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label for="article_category_id">Category*</label>
                <select name="article_category_id" id="article_category_id" class="form-control" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="author_name">Author Name*</label>
                <input type="text" id="author_name" name="author_name" class="form-control" value="{{ old('author_name', auth()->user()->fullname) }}" required>
            </div>

            <div class="form-group">
                <label for="other_authors">Other Authors</label>
                <textarea id="other_authors" name="other_authors" class="form-control">{{ old('other_authors') }}</textarea>
            </div>
            
            <div class="form-group">
                <label for="access_type">Access Type</label>
                <select name="access_type" id="access_type" class="form-control" required>
                    <option value="1">Open Access</option>
                    <option value="2">Close Access</option>
                </select>
            </div>

            <div class="form-group">
                <label for="file">Article File (PDF, DOC, DOCX)</label>
                <input type="file" id="file" name="file" class="form-control-file">
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="Submit Article">
            </div>
        </form>
    </div>
</div>
@endsection
