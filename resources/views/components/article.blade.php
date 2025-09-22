<div class="row justify-content-center my-3">
    @if($count != 0 && request()->has('q') && request('q'))
    <h4>Your Search result ({{$count}})</h4>
    <br />
    <div class="col-md-4">
        @if (request('q') && request()->has('q'))
        <div class=" border border-2 border-secondary rounded p-2">
            <!-- Horizontal under breakpoint -->
            <ul class="list-group list-group-flush">


                <a href="{{ request()->fullUrlWithQuery(['with_category' => null]) }}"
                    class="list-group-item d-flex justify-content-between">
                    <span>All Category</span>
                    <span>({{ $count }})</span>
                </a>

                @foreach($categories as $item)

                <a href="{{ request()->fullUrlWithQuery(['with_category' => $item->id]) }}"
                    class="list-group-item d-flex justify-content-between">
                    <span>{{$item->category_name ?? ''}}</span>
                    <span>({{$item->article_count}})</span>
                </a>
                @endforeach
            </ul>
        </div>

        @else

        <div class=" border border-2 border-secondary rounded p-2">
            <!-- Horizontal under breakpoint -->
            <ul class="list-group list-group-flush">
            </ul>
        </div>

        @endif
    </div>
    @endif
    <div class="col-md-8">
        <div class='d-flex align-items-center mb-2'>
            <a href="{{ request()->fullUrlWithQuery(['type' => 'article']) }}"
                class="menu-link {{ request('type') !== 'journal' ? 'active-a' : '' }}">Articles</a> |
            <a href="{{ request()->fullUrlWithQuery(['type' => 'journal']) }}"
                class="menu-link {{ request('type') === 'journal' ? 'active-a' : '' }}">Journal</a>
        </div>
        <hr>
        <br>

        <ul class="nav nav-tabs mb-3" id="myTab0" role="tablist">
            <li class="nav-item" role="presentation">
                <button autofocus
                    class="kb-text-orange-500 kb-border-orange-500 tab-btn kb-mx-1 kb-px-5 kb-py-3 kb-border-b-2 kb-font-medium"
                    id="first-tab0" data-mdb-toggle="tab" data-mdb-target="#first1" type="button" role="tab"
                    aria-controls="first" aria-selected="true">
                    Most Recent
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="tab-btn kb-mx-1 kb-px-5 kb-py-3 kb-font-medium" id="second-tab0" data-mdb-toggle="tab"
                    data-mdb-target="#second2" type="button" role="tab" aria-controls="second" aria-selected="false">
                    Most Read
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="tab-btn kb-mx-1 kb-px-5 kb-py-3 kb-font-medium" id="third-tab0" data-mdb-toggle="tab"
                    data-mdb-target="#third3" type="button" role="tab" aria-controls="third" aria-selected="false">
                    Most Cited
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="tab-btn kb-mx-1 kb-px-5 kb-py-3 kb-font-medium" id="fourth-tab0" data-mdb-toggle="tab"
                    data-mdb-target="#fourth4" type="button" role="tab" aria-controls="fourth" aria-selected="false">
                    Trending
                </button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent0">
            <div class="tab-pane !kb-m-0 !kb-p-0 fade show active" id="first1" role="tabpanel" aria-labelledby="first-tab0">

                @if ($articles->isNotEmpty())
                <x-article-card :articles="$articles" :class="'my-3'" />

                <hr>
                @else
                <div class="w-100 mx-auto">
                    <p class="fs-3 text-center text-danger">
                        Article Not Found
                    </p>
                </div>
                @endif


                @if ($articles->isNotEmpty())
                <div class="d-flex justify-content-center my-3">
                    <div class="">
                        {{$articles->links()}}
                    </div>
                </div>
                @endif

            </div>
            <div class="tab-pane kb-p-2 fade" id="second2" role="tabpanel" aria-labelledby="second-tab0">

                @isset($randomArticle)

                @forelse ($randomArticle as $article)
                <div class="d-flex font-weight-bold align-items-center my-3">
                    <span class="me-3">
                        @if ($article->access_type == 1)
                        <i class="fa fa-unlock text-success me-2" aria-hidden="true"></i> Open
                        Access
                        @else
                        <i class="fa fa-lock text-danger me-2" aria-hidden="true"></i> Close Access
                        @endif </span> |
                    <span class="mx-3"> {{$article->article_category->category_name ?? ''}} </span>|
                    <span class="ms-3">
                        First published {{ date('F d, Y', strtotime($article->updated_at)) }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="md:kb-w-2/3">
                        <div class="fs-4">
                            <a href="{{route('member.view-article', $article->id)}}"
                                class="hover:kb-text-orange-500">{{$article->title}}</a>
                        </div>
                        <div>
                            <div class="">
                                <strong>Author(s)</strong>: {{$article->member->fullname.", " ?? ''}}
                                {{$article->author_name ? $article->author_name.", " : ''}}
                            </div>
                            <div class="">
                                <strong>Other Author(s):</strong> {!! $article->other_authors ??
                                '<i>None</i>' !!}
                            </div>
                            <div class="">
                                <strong>Corresponding Author(s):</strong> {!! $article->corresponding_authors ??
                                '<i>None</i>' !!}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end align-items-center md:kb-w-1/3">
                        @auth('member')
                        <div>
                            @auth('member')
                            @php
                            $bookmark = \App\Models\Bookmark::where('article_id', $article->id)->where('member_id',
                            auth('member')->id())->exists();
                            @endphp
                            @if ($bookmark)
                            <i data-article="{{$article->id}}" class='bx bxs-bookmark bx-md bookmarked'></i>
                            @else
                            <i data-article="{{$article->id}}" class='bx bx-bookmark bx-md bookmarked'></i>
                            @endif
                            @endauth
                        </div>
                        <div>
                            <a href="{{route('member.view-article', $article->id)}}"
                                class="border border-2 {{ $article->access_type == 1 ? 'border-success btn btn-outline-success' : 'border-danger btn btn-outline-danger'}}  px-3 py-1 fs-5">
                                @if ($article->access_type == 1)
                                Get Article
                                <i class="fa fa-download" aria-hidden="true"></i>
                                @else
                                Get Access
                                <i class="fa fa-lock" aria-hidden="true"></i>
                                @endif
                            </a>
                        </div>
                        @else
                        <a href="{{ route('member.login') }}"
                            class="border border-2 {{ $article->access_type == 1 ? 'border-success btn btn-outline-success' : 'border-danger btn btn-outline-danger'}}  px-3 py-1 fs-5">
                            @if ($article->access_type == 1)
                            Get Article
                            <i class="fa fa-download" aria-hidden="true"></i>
                            @else
                            Get Access
                            <i class="fa fa-lock" aria-hidden="true"></i>
                            @endif
                        </a>
                        @endauth
                    </div>
                </div>
                <div class="my-3">
                    <p class="openAbstract em-btn bg-dark open" style="cursor: pointer">Show Abstract</p>
                    <div class="my-2 p-md-4 p-2 rounded kb-prose kb-max-w-none"
                        style="background-color: #7c7c7c49;display: none">
                        <h3>Abstract</h3>
                        {!! $article->last->abstract !!}
                    </div>
                </div>
                <hr>
                @empty
                <div class="w-100 mx-auto">
                    <p class="fs-3 text-center text-danger">
                        Article Not Found
                    </p>
                </div>
                @endforelse

                @if ($randomArticle->isNotEmpty())
                <div class="d-flex justify-content-center my-3">
                    <div class="">
                        {{$randomArticle->links()}}
                    </div>
                </div>
                @endif

                @endisset

            </div>
            <div class="tab-pane kb-p-2 fade" id="third3" role="tabpanel" aria-labelledby="third-tab0">
                Most Cited
            </div>
            <div class="tab-pane kb-p-2 fade" id="fourth4" role="tabpanel" aria-labelledby="fourth-tab0">
                Trending
            </div>
        </div>

    </div>
</div>


@push('component')
<script>
    $(function () {
            $('button.tab-btn').click(function (e) {
                e.preventDefault();
                let thisTab = $(this);

                $('button.tab-btn').removeClass('kb-text-orange-500 kb-border-orange-500 kb-border-b-2');
                thisTab.addClass('kb-text-orange-500 kb-border-orange-500 kb-border-b-2');

            });
        });
</script>
@endpush
