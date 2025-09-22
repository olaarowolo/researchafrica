@extends('layouts.profile')
@section('page-name', 'Profile')

@section('styles')
<style>
    a {
        color: #252525;
        text-decoration: none;
    }

    a:hover {
        text-decoration: none;
    }
</style>
@endsection


@section('page-name', 'Profile')

@section('content')


<div id="content" class="p-4 p-md-5">
    <x-profile-bar  />

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow card-svg">
                    <div class="card-body p-1 px-md-3">
                        <div class="d-flex justify-content-between align-items-center text-dark">
                            <div class="d-flex align-items-center gap-2">
                                <div style="font-size: 20px" style="z-index: 999">
                                    Bookmark
                                </div>
                            </div>
                            <span style="font-size: 50px">
                                <strong>{{auth('member')->user()->bookmarks()->count()}}</strong>
                            </span>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('member.view-bookmark') }}" class="">View All</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow card-svg">
                    <div class="card-body p-1 px-md-3">
                        <div class="d-flex justify-content-between align-items-center text-dark">
                            <div style="font-size: 20px">
                                Purchased Article
                            </div>
                            <span style="font-size: 50px">
                                <strong>{{auth('member')->user()->purchasedArticle()->count()}}</strong>
                            </span>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('member.purchased-article') }}" class="">
                                View All
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Article Title</th>
                        <th scope="col">Author Name</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $bookmarks = auth('member')->user()->bookmarks()->latest()->take(3)->get();
                    @endphp
                    @forelse ($bookmarks as $bookmark)
                        @if($article = $bookmark->article)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td scope="col">{{ $article->title ?? '' }}</td>
                            <td scope="col">{{ $article->member->fullname }}</td>
                            <td scope="col">    
                                @if($article->access_type == 1)
                                <div class="em-btn bg-dark">
                                    {{\App\Models\Article::ACCESS_TYPE[$article->access_type]}}
                                </div>
                                @else
                                <div class="em-btn bg-danger">
                                    {{\App\Models\Article::ACCESS_TYPE[$article->access_type]}}
                                </div>
                                @endif
                            </td>
                            <td scope="col">
                                <a class="em-btn text-light bg-dark" href="{{ route('member.view-article', $article->id) }}">View</a>
                            </td>
                        </tr>
                        @endif
                    @empty
                    <tr>
                        <caption>
                            <h3 class="text-center">
                                No Bookmark <i class="fa fa-bookmark" aria-hidden="true"></i>
                            </h3>
                        </caption>
                    </tr>
                        
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>
</div>

@endsection


@section('scripts')
<script>
    $('.deleteBtn').click(function (e) {
            e.preventDefault();
            let thisBtn = $(this);

            Swal.fire({
              title: 'Deleting.',
              text: 'Are you sure ?',
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Delete'
            }).then((result) => {
              if (result.isConfirmed) {
                thisBtn.siblings('form').submit();
              }
            })

        });
</script>
@endsection
