@extends('layouts.member')

@section('page-name', 'Contact')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Contact {{ $journal->name }}</h1>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <p>For any inquiries, please contact us using the information below.</p>
            <address>
                @if($contactInfo['editor_in_chief'])
                    <strong>Editor-in-Chief:</strong> {{ $contactInfo['editor_in_chief'] }}<br>
                @endif
                @if($contactInfo['email'])
                    <strong>Email:</strong> <a href="mailto:{{ $contactInfo['email'] }}">{{ $contactInfo['email'] }}</a><br>
                @endif
                @if($contactInfo['publisher_name'])
                    <strong>Publisher:</strong> {{ $contactInfo['publisher_name'] }}<br>
                @endif
            </address>
        </div>
    </div>
</div>
@endsection
