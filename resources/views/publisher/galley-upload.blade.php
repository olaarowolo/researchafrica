    @if ($article->galley_proof_status === 'approved')
        <div class="mt-4">
            <h4>Upload Final Ready-to-Publish Version</h4>
            <form method="POST" action="{{ route('publisher.final.upload', $article) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="final_version">Final Version (PDF only, max 20MB):</label>
                    <input type="file" name="final_version" id="final_version" class="form-control" required
                        accept=".pdf">
                    @error('final_version')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success mt-2">Upload Final Version</button>
            </form>
        </div>
    @endif
    @extends('layouts.app')

    @section('content')
        <div class="container">
            <h2>Upload Galley Proof for: {{ $article->title }}</h2>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('publisher.galley.upload', $article) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="galley_proof">Galley Proof (PDF only, max 20MB):</label>
                    <input type="file" name="galley_proof" id="galley_proof" class="form-control" required
                        accept=".pdf">
                    @error('galley_proof')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary mt-2">Upload</button>
            </form>
            @if ($article->galley_proof_path)
                <div class="mt-3">
                    <strong>Current Galley Proof:</strong>
                    <a href="{{ asset('storage/' . $article->galley_proof_path) }}" target="_blank">Download</a>
                </div>
            @endif
        </div>
    @endsection
