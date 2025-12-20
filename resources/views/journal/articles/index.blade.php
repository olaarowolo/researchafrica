@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        Articles for {{ $currentJournal->name }}
    </div>

    <div class="card-body">
        <div class="mb-3">
            <a href="{{ route('articles.create', ['acronym' => $currentJournal->journal_acronym]) }}" class="btn btn-primary">
                Submit New Article
            </a>
        </div>

        <table class="table table-bordered table-striped table-hover datatable">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $article)
                    <tr>
                        <td>{{ $article->title }}</td>
                        <td>{{ $article->author_name }}</td>
                        <td>{{ $article->article_category->name ?? '' }}</td>
                        <td>
                            <span class="badge badge-{{ $article->article_status == 3 ? 'success' : 'info' }}">
                                {{ \App\Models\Article::ARTICLE_STATUS[$article->article_status] ?? 'Unknown' }}
                            </span>
                        </td>
                        <td>{{ $article->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a class="btn btn-xs btn-primary" href="{{ route('articles.show', ['acronym' => $currentJournal->journal_acronym, 'article' => $article->id]) }}">
                                View
                            </a>
                            @if($article->article_status != 3)
                                <a class="btn btn-xs btn-info" href="{{ route('articles.edit', ['acronym' => $currentJournal->journal_acronym, 'article' => $article->id]) }}">
                                    Edit
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($articles->hasPages())
            <div class="mt-3">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
