@extends('layouts.member')

@section('page-name', 'Articles')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Articles for {{ $journal->name }}</h1>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if($articles->isNotEmpty())
                @foreach($articles as $article)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('journals.public.article-details', ['acronym' => $journal->journal_acronym, 'article' => $article->id]) }}">
                                    {{ $article->title }}
                                </a>
                            </h5>
                            <p class="card-text">
                                <small class="text-muted">By {{ $article->author_name }} on {{ $article->published_online->format('F d, Y') }}</small>
                            </p>
                            <p class="card-text">{{ Str::limit($article->abstract, 250) }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-center">
                    {{ $articles->links() }}
                </div>
            @else
                <p>No articles have been published in this journal yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
