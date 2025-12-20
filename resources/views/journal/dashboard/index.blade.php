@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>{{ $currentJournal->name }} Dashboard</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Article Stats</div>
                <div class="card-body">
                    <p>Total Articles: {{ $stats['total'] }}</p>
                    <p>Published: {{ $stats['published'] }}</p>
                    <p>Pending: {{ $stats['pending'] }}</p>
                    <p>In Review: {{ $stats['reviewing'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Quick Links</div>
                <div class="card-body">
                    <a href="{{ route('articles.index', ['acronym' => $currentJournal->journal_acronym]) }}" class="btn btn-primary btn-block">Manage Articles</a>
                    <a href="{{ route('articles.create', ['acronym' => $currentJournal->journal_acronym]) }}" class="btn btn-success btn-block">Submit New Article</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
