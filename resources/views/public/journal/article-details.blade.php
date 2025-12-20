@extends('layouts.member')

@section('page-name', $article->title)

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <h1>{{ $article->title }}</h1>
            <p class="lead">by {{ $article->author_name }}</p>
            <hr>

            <div class="my-4">
                <strong>Published:</strong> {{ $article->published_online->format('F d, Y') }} in
                <a href="{{ route('journals.public.index', ['acronym' => $journal->journal_acronym]) }}">{{ $journal->name }}</a>
            </div>

            @if($article->abstract)
                <div class="my-4">
                    <h4>Abstract</h4>
                    <p>{!! $article->abstract !!}</p>
                </div>
            @endif

            <div class="my-4">
                <a href="{{ route('journals.articles.download', ['acronym' => $journal->journal_acronym, 'article' => $article->id]) }}" class="btn btn-primary">Download PDF</a>
            </div>

            @if($article->content)
                <div class="my-4 article-content">
                    {!! $article->content !!}
                </div>
            @endif

            <hr>

            <div class="my-4">
                <h4>Comments</h4>
                @if($article->comments->isNotEmpty())
                    @foreach($article->comments as $comment)
                        <div class="card mb-2">
                            <div class="card-body">
                                <p>{{ $comment->comment_text }}</p>
                                <small class="text-muted">By {{ $comment->member->fullname }} on {{ $comment->created_at->format('F d, Y') }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>No comments yet.</p>
                @endif
            </div>

        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Article Details
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Volume:</strong> {{ $article->volume }}</li>
                    <li class="list-group-item"><strong>Issue:</strong> {{ $article->issue_no }}</li>
                    <li class="list-group-item"><strong>DOI:</strong> {{ $article->doi_link }}</li>
                    <li class="list-group-item"><strong>Access Type:</strong> {{ $article->access_type == 1 ? 'Open Access' : 'Subscription' }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
