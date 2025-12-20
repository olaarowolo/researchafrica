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

    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }

    .animate-blink-blue {
        animation: blink 1s infinite;
        color: blue;
    }
</style>

@endsection


@section('page-name', 'Profile')

@section('content')


<div id="content" class="p-4 p-md-5">
    <x-profile-bar>
        <div class="font-weight-bold h4">
            Publisher
        </div>
    </x-profile-bar>

    {{-- <div class="container">
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
    --}}

    <div class="card rounded shadow">
        <div class="card-header">
            <!-- Tabs navs -->
            <ul class="nav nav-tabs mb-3" id="ex1" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link h5 font-weight-bold active" id="ex1-tab-1" data-mdb-toggle="tab"
                        href="#ex1-tabs-1" role="tab" aria-controls="ex1-tabs-1" aria-selected="true">
                        Incoming Request
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link h5 font-weight-bold" id="ex1-tab-2" data-mdb-toggle="tab" href="#ex1-tabs-2"
                        role="tab" aria-controls="ex1-tabs-2" aria-selected="false">
                        Published Article
                    </a>
                </li>
            </ul>
            <!-- Tabs navs -->
        </div>

        <div class="card-body">
            <!-- Tabs content -->
            <div class="tab-content" id="ex1-content">
                <div class="tab-pane fade show active" id="ex1-tabs-1" role="tabpanel" aria-labelledby="ex1-tab-1">

                    @foreach ($newArticles as $article)

                    <div class="d-flex">


                        <p class="">
                            <i class="fa-sharp fa-solid fa-calendar-days"></i>
                            {{date('M j, Y - H:i', strtotime($article->created_at ?? now()))}}

                            |


                            {{-- <i
                                class="fa-sharp fa-solid fa-folders"></i> Category: {{
                            $article->article_category->category_name ?? '' }} --}}
                            <p class="">
                                {{-- <strong><span style="color:red; animation: blink 1s infinite;">{{ $article->last->status == 7 ? "Returning Article" : (is_null($article->last->comment_id) ? "New Article" : "Modify Article") }}</span></strong>  --}}
                                |
                                Submission to:  <i
                                class="fa-sharp fa-solid fa-folders"></i> <strong><span style="color:red;"> {{
                            $article->article_category->category_name ?? '' }}   </span></strong>>   {{$article->journal_category->category_name ?? '' }}
                            |

                            <i class="fa-sharp fa-solid fa-bookmark"></i> {{ $article->volume ?? '' }} | <i
                                class="fa-sharp fa-solid fa-book-bookmark"></i> ISSN : {{ $article->issue_no ?? '' }}
                        </p>
                    </div>
                    <div class="d-flex justify-content-between">
                        {{-- <div>
                            <p>{{ $article->title ?? '' }}</p>
                            <strong>{{ $article->last->status == 7 ? "Returning Article" :
                                (is_null($article->last->comment_id) ? "New Article: Ready to Publish" : "Modify Article") }}</strong>
                        </div> --}}
                        <div class="kb-shadow hover:kb-border-2 hover:kb-scale-105 kb-rounded-lg">
                            <p><strong class="animate-blink-blue">{{ $article->last && $article->last->status == 7 ? "Returning Article" : ($article->last && is_null($article->last->comment_id) ? "New Article: Ready to Publish" : "Modify Article") }}</strong>
                            </p>

                            <p style="font-size: 20px;">   {{ $article->title ?? '' }} </p>

                            <strong>Corresponding Author</strong>: {{ $article->member->fullname ?? '' }}
 @if ($article->member->fullname)
                                            <sup>
                                               <a href="https://orcid.org/{{ $article->member?->first()?->orchid_id }}" style="display: inline;" target="_blank">
    <img src="https://orcid.org/assets/vectors/orcid.logo.icon.svg" alt="ORCID Logo" style="width: 13px; height: 13px; display: inline;">
</a>
                                            </sup>
                                        @else
                                            <i>None</i>
                                        @endif
                            {{-- <strong class="animate-blink">{{ $article->last->status == 7 ? "Returning Article" : (is_null($article->last->comment_id) ? "New Article: Ready to Publish" : "Modify Article") }}</strong> --}}
                        </div>

                        <div>
                            <div class="d-flex align-items-center">
                                @if (is_null($article->publisher_accept->member_id))
                                <button class="em-btn bg-success acceptBtn mr-md-2">
                                    Accept
                                </button>
                                <form
                                    class="acceptForm"
                                    action="{{ route('member.publisher.accept', $article->id) }}"
                                    method="post">@csrf</form>
                                @endif
                            </div>
                        </div>
                    </div>
                    <hr>

                    @endforeach
                </div>
                <div class="tab-pane fade" id="ex1-tabs-2" role="tabpanel" aria-labelledby="ex1-tab-2">


                    @foreach ($acceptedArticle as $article)


                    <div class="d-flex">
                        <p class="">
                            <i class="fa-sharp fa-solid fa-calendar-days"></i>
                            {{date('M j, Y - H:i', strtotime($article->created_at ?? now()))}} | <i
                                class="fa-sharp fa-solid fa-folders"></i> Category: {{
                            $article->article_category->category_name ?? '' }}
                            | <i class="fa-sharp fa-solid fa-bookmark"></i> {{ $article->volume ?? '' }} | <i
                                class="fa-sharp fa-solid fa-book-bookmark"></i> ISSN : {{ $article->issue_no ?? '' }}
                        </p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('member.articles.show', $article->id) }}" class="h4">{{ $article->title ?? '' }}</a>
                        </div>
                        <div>
                            <div class="">

                                <a href="{{ route('member.articles.show', $article->id) }}" class="em-btn bg-dark">
                                    View Article
                                </a>
                                <div class="text-center">
                                    <small>{{ $article->comments->count() }} Comment(s)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    @endforeach
                </div>
            </div>
            <!-- Tabs content -->
        </div>
    </div>

</div>

@endsection


@section('scripts')
<script>

    $('.acceptBtn').click(function (e) {
        e.preventDefault();
        thisBtn = $(this);

        Swal.fire({
            title: 'Are You sure?',
            text: 'You won\'t be able to revert this action?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Accept'
        }).then((result) => {
            if (result.isConfirmed) {
            thisBtn.siblings('form').submit();
            }
        })
    });

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
