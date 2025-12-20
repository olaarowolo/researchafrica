@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        {{ $article->title }}
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <p><strong>Author:</strong> {{ $article->author_name }}</p>
                <p><strong>Status:</strong> <span class="badge badge-{{ $article->article_status == 3 ? 'success' : 'info' }}">{{ \App\Models\Article::ARTICLE_STATUS[$article->article_status] ?? 'Unknown' }}</span></p>
                <p><strong>Submitted on:</strong> {{ $article->created_at->format('F d, Y') }}</p>
                
                @if($article->file_path)
                    <a href="{{ route('articles.download', ['acronym' => $currentJournal->journal_acronym, 'article' => $article->id]) }}" class="btn btn-primary">Download Article</a>
                @endif

                <hr>

                <h4>Comments/Reviews</h4>
                @if($article->comments && $article->comments->isNotEmpty())
                    @foreach($article->comments as $comment)
                        <div class="card my-2">
                            <div class="card-body">
                                <p>{{ $comment->comment_text }}</p>
                                <small>By {{ $comment->member->fullname }} on {{ $comment->created_at->format('F d, Y') }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>No comments or reviews yet.</p>
                @endif
            </div>
            <div class="col-md-4">
                <h4>Actions</h4>
                @if(Auth::user()->hasJournalAccess($currentJournal->id, 2)) {{-- Editor --}}
                    @if($article->article_status == 1)
                        <form action="{{ route('articles.review', ['acronym' => $currentJournal->journal_acronym, 'article' => $article->id]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-info btn-sm">Start Review</button>
                        </form>
                    @endif
                    @if(in_array($article->article_status, [1, 2]))
                        <form action="{{ route('articles.approve', ['acronym' => $currentJournal->journal_acronym, 'article' => $article->id]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Approve & Publish</button>
                        </form>
                        <form action="{{ route('articles.reject', ['acronym' => $currentJournal->journal_acronym, 'article' => $article->id]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    @endif
                @endif
                <a href="{{ route('articles.edit', ['acronym' => $currentJournal->journal_acronym, 'article' => $article->id]) }}" class="btn btn-secondary btn-sm">Edit Article</a>
            </div>
        </div>
    </div>
</div>
@endsection
