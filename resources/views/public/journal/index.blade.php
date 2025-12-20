@extends('layouts.member')

@section('page-name', $journal->name)

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron">
                <h1 class="display-4">{{ $journal->name }}</h1>
                <p class="lead">{{ $journal->description }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <h2>Recent Articles</h2>
            @if($recentArticles->isNotEmpty())
                @foreach($recentArticles as $article)
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
                            <p class="card-text">{{ Str::limit($article->last?->abstract ?? '', 150) }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <p>No recent articles found.</p>
            @endif
        </div>
        <div class="col-md-4">
            <h2>Journal Stats</h2>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Total Articles
                    <span class="badge badge-primary badge-pill">{{ $stats['total_articles'] }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Published Articles
                    <span class="badge badge-primary badge-pill">{{ $stats['published_articles'] }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Editorial Board Members
                    <span class="badge badge-primary badge-pill">{{ $stats['editorial_board_count'] }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Total Views
                    <span class="badge badge-primary badge-pill">{{ $stats['total_views'] }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
