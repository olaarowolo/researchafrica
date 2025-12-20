@extends('layouts.member')

@section('page-name', 'Search Results')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Search Results for "{{ $query }}"</h1>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <h4>Filter Results</h4>
            <form action="{{ route('journals.public.index', ['acronym' => $journal->journal_acronym]) }}/search" method="GET">
                <div class="form-group">
                    <label for="q">Search Term</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ $query }}">
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="year">Year</label>
                    <select name="year" id="year" class="form-control">
                        <option value="">All Years</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="col-md-9">
            @if($articles->isNotEmpty())
                <p>{{ $articles->total() }} articles found.</p>
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
                    {{ $articles->appends(request()->query())->links() }}
                </div>
            @else
                <p>No articles found matching your criteria.</p>
            @endif
        </div>
    </div>
</div>
@endsection
