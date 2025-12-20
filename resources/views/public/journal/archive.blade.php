@extends('layouts.member')

@section('page-name', 'Archive')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Archive for {{ $journal->name }}</h1>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if($archivedArticles->isNotEmpty())
                @foreach($archivedArticles as $archive)
                    <div class="my-4">
                        <h3>{{ \Carbon\Carbon::create()->month($archive->month)->format('F') }} {{ $archive->year }}</h3>
                        <ul class="list-group">
                            @foreach($archive->articles as $article)
                                <li class="list-group-item">
                                    <a href="{{ route('journals.public.article-details', ['acronym' => $journal->journal_acronym, 'article' => $article->id]) }}">
                                        {{ $article->title }}
                                    </a>
                                    <br>
                                    <small class="text-muted">By {{ $article->author_name }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @else
                <p>No articles have been archived yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
