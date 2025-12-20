@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>Editorial Board Management for {{ $currentJournal->name }}</h1>
    <p>This page will contain tools for managing the editorial board, including adding/removing members and assigning roles.</p>
</div>
@endsection
