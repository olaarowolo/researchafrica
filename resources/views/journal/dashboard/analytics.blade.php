@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>Analytics for {{ $currentJournal->name }}</h1>
    <p>This page will contain charts and graphs showing article views, downloads, submission rates, and other key metrics.</p>
</div>
@endsection
