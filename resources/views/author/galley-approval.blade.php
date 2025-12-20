@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Galley Proof Approval for: {{ $article->title }}</h2>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="mb-3">
            <a href="{{ asset('storage/' . $article->galley_proof_path) }}" target="_blank" class="btn btn-info">View Galley
                Proof (PDF)</a>
        </div>
        <form method="POST" action="">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
            <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
        </form>
    </div>
@endsection
