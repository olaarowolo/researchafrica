@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>Article Management for {{ $currentJournal->name }}</h1>
    <p>This page will contain a more detailed view of articles for editors and publishers, including assignments and workflow status.</p>
    <p>For now, please use the main articles list:</p>
    <a href="{{ route('articles.index', ['acronym' => $currentJournal->journal_acronym]) }}" class="btn btn-primary">Go to Articles</a>
</div>
@endsection
