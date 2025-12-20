@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mt-4">Admin Dashboard</h1>
                <p class="lead">Welcome to the Research Africa Admin Panel</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Articles</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalArticles }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Published Articles</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $publishedArticles }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Pending Articles</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $pendingArticles }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Total Users</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalUsers }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Articles Table -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Articles</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Submitted Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentArticles as $article)
                                    <tr>
                                        <td>{{ $article->title }}</td>
                                        <td>{{ $article->user->name ?? 'N/A' }}</td>
                                        <td>{{ $article->status }}</td>
                                        <td>{{ $article->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
