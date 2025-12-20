@extends('layouts.member')

@section('page-name', 'About ' . $journal->name)

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>About {{ $journal->name }}</h1>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            @if($journal->aim_scope)
                <h2>Aim & Scope</h2>
                <p>{!! $journal->aim_scope !!}</p>
            @endif

            @if($journal->description)
                <h2>Description</h2>
                <p>{!! $journal->description !!}</p>
            @endif
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Journal Details
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>ISSN:</strong> {{ $journal->issn }}</li>
                    <li class="list-group-item"><strong>Online ISSN:</strong> {{ $journal->online_issn }}</li>
                    <li class="list-group-item"><strong>Publisher:</strong> {{ $journal->publisher_name }}</li>
                    <li class="list-group-item"><strong>Editor-in-Chief:</strong> {{ $journal->editor_in_chief }}</li>
                    <li class="list-group-item">
                        <a href="{{ $journal->journal_url }}" target="_blank">Journal Website</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
