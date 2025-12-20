@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        Editing article: {{ $article->title }}
    </div>

    <div class="card-body">
        <form action="{{ route('articles.update', ['acronym' => $currentJournal->journal_acronym, 'article' => $article->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="title">Title*</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $article->title) }}" required>
            </div>

            <div class="form-group">
                <label for="article_category_id">Category*</label>
                <select name="article_category_id" id="article_category_id" class="form-control" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $article->article_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="author_name">Author Name*</label>
                <input type="text" id="author_name" name="author_name" class="form-control" value="{{ old('author_name', $article->author_name) }}" required>
            </div>

            <div class="form-group">
                <label for="other_authors">Other Authors</label>
                <textarea id="other_authors" name="other_authors" class="form-control">{{ old('other_authors', $article->other_authors) }}</textarea>
            </div>
            
            <div class="form-group">
                <label for="access_type">Access Type</label>
                <select name="access_type" id="access_type" class="form-control" required>
                    <option value="1" {{ $article->access_type == 1 ? 'selected' : '' }}>Open Access</option>
                    <option value="2" {{ $article->access_type == 2 ? 'selected' : '' }}>Close Access</option>
                </select>
            </div>

            <div class="form-group">
                <label for="file">Article File (PDF, DOC, DOCX)</label>
                <input type="file" id="file" name="file" class="form-control-file">
                @if($article->file_path)
                    <p class="mt-2">Current file: <a href="{{ route('articles.download', ['acronym' => $currentJournal->journal_acronym, 'article' => $article->id]) }}">Download</a></p>
                @endif
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="Update Article">
            </div>
        </form>
    </div>
</div>
@endsection
