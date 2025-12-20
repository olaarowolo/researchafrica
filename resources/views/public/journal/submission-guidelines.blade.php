@extends('layouts.member')

@section('page-name', 'Submission Guidelines')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Submission Guidelines for {{ $journal->name }}</h1>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if($journal->submission)
                <div>
                    {!! $journal->submission !!}
                </div>
            @elseif($submissionSettings)
                @foreach($submissionSettings as $key => $value)
                    <h4>{{ Str::title(str_replace('_', ' ', $key)) }}</h4>
                    <p>{{ $value }}</p>
                @endforeach
            @else
                <p>Submission guidelines for this journal have not been set up yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
