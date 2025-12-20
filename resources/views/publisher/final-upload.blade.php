@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Upload Final Ready-to-Publish Version for: {{ $article->title }}</h2>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('publisher.final.upload', $article) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="final_version">Final Version (PDF only, max 20MB):</label>
                <input type="file" name="final_version" id="final_version" class="form-control" required accept=".pdf">
                @error('final_version')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-success mt-2">Upload Final Version</button>
        </form>
        @if ($article->final_version_path)
            <div class="mt-3">
                <strong>Current Final Version:</strong>
                <a href="{{ asset('storage/' . $article->final_version_path) }}" target="_blank">Download</a>
            </div>
        @endif
    </div>
@endsection
