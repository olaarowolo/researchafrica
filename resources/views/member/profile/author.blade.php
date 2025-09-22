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
        <x-profile-bar>
            <li class="nav-item active">
                <a class="nav-link btn btn-dark btn-small text-white" href="{{ route('member.articles.create') }}"
                    style="height: 40px; width: 140px;">
                    Create Article
                </a>
            </li>
        </x-profile-bar>

        <div class="card shadow">
            <div class="card-body px-4 mt-4">
                <!-- Tabs navs -->
                <ul class="nav nav-tabs mb-3" id="ex1" role="tablist">
                    <li class="nav-item" style="width: fit-content" role="presentation">
                        <a class="nav-link active text-dark" id="ex2-tab-1" data-mdb-toggle="tab" href="#ex2-tabs-1"
                            role="tab" aria-controls="ex2-tabs-1" aria-selected="true">
                            Article Under Review
                        </a>
                    </li>
                    <li class="nav-item" style="width: fit-content" role="presentation">
                        <a class="nav-link text-dark" id="ex2-tab-2" data-mdb-toggle="tab" href="#ex2-tabs-2" role="tab"
                            aria-controls="ex2-tabs-2" aria-selected="true">
                            Published Article
                        </a>
                    </li>
                </ul>
                <!-- Tabs navs -->

                <!-- Tabs content -->
                <div class="tab-content" id="ex2-content">
                    <div class="tab-pane fade show active" id="ex2-tabs-1" role="tabpanel" aria-labelledby="ex2-tab-1">
                        @forelse ($reviewArticles as $article)
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('member.articles.show', $article->id) }}" class="d-block">
                                    @php
                                        $status = \App\Models\Article::ARTICLE_STATUS[$article->article_status];

                                        if ($article->article_status == 1) {
                                            $bg = 'bg-primary';
                                        } elseif ($article->article_status == 2) {
                                            $bg = 'bg-dark';
                                        } elseif ($article->article_status == 3) {
                                            $bg = 'bg-success';
                                        } else {
                                            $bg = '';
                                        }

                                    @endphp
                                    <p class="">
                                        <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                        {{ date('M j, Y - H:i', strtotime($article->created_at ?? now())) }}
                                        | <i class="fa-sharp fa-solid fa-folders"></i>
                                        Category: {{ $article->article_category->category_name ?? '' }}
                                        | Status: <strong
                                            class="{{ $bg }} text-light px-2 font-bold rounded">{{ $status }}</strong>
                                    </p>
                                    <p class="w-75">{{ $article->title ?? '' }}</p>
                                </a>
                                <div>
                                    <div class="d-flex align-items-center">
                                        @if ($count = $article->comments()->count())
                                            <a href="{{ route('member.articles.show', $article->id) }}"
                                                class="em-btn btn-dark comment_btn mx-1"
                                                style="height: 40px; width: 180px;">
                                                View Comment <span class="badge badge-light">{{ $count }}</span>
                                            </a>
                                        @endif

                                        @if ($article->article_status == 1)
                                            <a href="{{ route('member.articles.edit', $article->id) }}"
                                                class="em-btn bg-dark mx-1">
                                                <i class="fa fa-pencil-square" aria-hidden="true"></i> Edit
                                            </a>
                                        @endif
                                        @if ($article->article_status != 3)
                                            <button class="em-btn bg-danger deleteBtn mx-1">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i> Withdraw
                                            </button>
                                            <form action="{{ route('member.articles.destroy', $article->id) }}"
                                                method="post">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @empty

                            <em class="h4 text-danger kb-italic">
                                No Article
                            </em>
                        @endforelse
                    </div>
                    <div class="tab-pane fade" id="ex2-tabs-2" role="tabpanel" aria-labelledby="ex2-tab-2">
                        @forelse ($publishArticles as $article)
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('member.articles.show', $article->id) }}" class="d-block">
                                    @php
                                        $status = \App\Models\Article::ARTICLE_STATUS[$article->article_status];

                                        if ($article->article_status == 1) {
                                            $bg = 'bg-primary';
                                        } elseif ($article->article_status == 2) {
                                            $bg = 'bg-dark';
                                        } elseif ($article->article_status == 3) {
                                            $bg = 'bg-success';
                                        } else {
                                            $bg = '';
                                        }

                                    @endphp
                                    <p class="">
                                        <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                        {{ date('M j, Y - H:i', strtotime($article->created_at ?? now())) }}
                                        | <i class="fa-sharp fa-solid fa-folders"></i>
                                        Category: {{ $article->article_category->category_name ?? '' }}
                                        | Status: <strong
                                            class="{{ $bg }} text-light px-2 font-bold rounded">{{ $status }}</strong>
                                    </p>
                                    <p class="">{{ $article->title ?? '' }}</p>
                                </a>
                                <div>
                                    <div>
                                        @if ($count = $article->comments()->count())
                                            <a href="{{ route('member.articles.show', $article->id) }}"
                                                class="em-btn btn-dark comment_btn" style="height: 40px; width: 180px;">
                                                View Comment <span class="badge badge-light">{{ $count }}</span>
                                            </a>
                                        @endif

                                        @if ($article->article_status == 1)
                                            <a href="{{ route('member.articles.edit', $article->id) }}"
                                                class="em-btn bg-dark">
                                                <i class="fa fa-pencil-square" aria-hidden="true"></i> Edit
                                            </a>
                                        @endif
                                        @if ($article->article_status != 3)
                                            <button class="em-btn bg-danger deleteBtn">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                            </button>
                                            <form action="{{ route('member.articles.destroy', $article->id) }}"
                                                method="post">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @empty

                            <em class="h4 text-danger kb-italic">
                                No Article
                            </em>
                        @endforelse
                    </div>
                </div>
                <!-- Tabs content -->

            </div>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        $('.deleteBtn').click(function(e) {
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
