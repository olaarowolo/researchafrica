@extends('layouts.member')

@section('page-name', 'Editorial Board')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Editorial Board for {{ $journal->name }}</h1>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if($editorialBoard->isNotEmpty())
                @foreach($editorialBoard as $position => $members)
                    <div class="my-4">
                        <h3>{{ $position }}</h3>
                        <div class="row">
                            @foreach($members as $member)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $member->member->fullname }}</h5>
                                            <p class="card-text">{{ $member->institution }}</p>
                                            @if($member->department)
                                                <p class="card-text"><small class="text-muted">{{ $member->department }}</small></p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <p>The editorial board for this journal has not been set up yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
