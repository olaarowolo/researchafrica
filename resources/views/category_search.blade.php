@extends('layouts.member')

@section('page-name', 'Category Search')

@section('content')

    {{-- @include('member.partials.slider') --}}

    <!-- Content
                                                         ============================================= -->
    <div class="p-3 bg-dark">

        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="">
                        <p class="text-white fs-1 text-capitalize">{{ str_replace('_', ' ', $journal) }} </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <section id="content">
        <div class="">

            <div class="container">
                <div class="mb-3 row">

                    <div class="p-1 col-lg-9 pb-md-3 ">
                        <div class="my-3 rounded kb-border kb-p-3 kb-bg-gray-300/50">
                            <div class="gap-2 d-flex kb-items-center">


                                <div class="kb-shadow hover:kb-border-2 hover:kb-scale-105 kb-rounded-lg ">
                                    <img src="{{ $sub_cat->cover_image ? $sub_cat->cover_image->getUrl() : '' }}"
                                        alt="" class="kb-h-24 kb-w-20 kb-object-fill">
                                </div>
                                <div class="kb-prose kb-prose-base">
                                    {!! Str::limit($sub_cat->description, 200) !!}
                                    <a href="{{ route('member.journal', $sub) . '?type=description' }}">Read More</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <form action="" method="get">
                                <div class="form-group row">
                                    <label for="sort_by" class="col-md-12 col-form-label">Sort Article By:</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="sort" id="sort_by">
                                            <option value="latest" {{ request()->sort == 'latest' ? 'selected' : '' }} >Latest</option>
                                            <option value="open_access" {{ request()->sort == 'open_access' ? 'selected' : '' }} >Open Access</option>
                                            <option value="most_read" {{ request()->sort == 'most_read' ? 'selected' : '' }} >Most Read</option>
                                            <option value="most_cited" {{ request()->sort == 'most_cited' ? 'selected' : '' }} >Most Cited</option>
                                            <option value="trending" {{ request()->sort == 'trending' ? 'selected' : '' }} >Trending</option>
                                        </select>
                                    </div>
                                    <div class="col-md-auto">
                                        <button type="submit" class="em-btn bg-dark open">Sort</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <x-categoryArticle :articles="$articles" />
                    </div>
                    <div class="p-2 mt-2 col-md-3">
                        <div class="card">
                            <div class="card-header">
                                Journal Infomation
                            </div>
                            <div class="card-body">

                                <ul class="">
                                    <li>
                                        <a href="{{ route('member.journal', $sub) }}?type=description"
                                            class="d-block kb-py-3">
                                            Journal Description
                                        </a>
                                    </li>
                                    <hr />
                                    <li>
                                        <a href="{{ route('member.journal', $sub) }}?type=aim_scope"
                                            class="d-block kb-py-3">
                                            Journal Aim and Scope
                                        </a>
                                    </li>
                                    <hr />
                                    <li>
                                        <a href="{{ route('member.journal', $sub) }}?type=editorial_board"
                                            class="d-block kb-py-3">
                                            Journal Editorial Board
                                        </a>
                                    </li>
                                    <hr />
                                    <li>
                                        <a href="{{ route('member.journal', $sub) }}?type=submission"
                                            class="d-block kb-py-3">
                                            Journal Submission Guidelines
                                        </a>
                                    </li>
                                    <hr />
                                    <li>
                                        <a href="{{ route('member.journal', $sub) }}?type=subscribe"
                                            class="d-block kb-py-3">
                                            Journal Subscription Guidelines
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @if ($articles->isNotEmpty())
                        <div class="d-flex justify-content-end">
                            <div class="">
                                {{ $articles->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section><!-- #content end -->


@endsection



@section('scripts')
    <script>
        $(function() {
            $('p.openAbstract').click(function(e) {
                e.preventDefault();
                let thisOpen = $(this);
                if (thisOpen.hasClass('open')) {
                    thisOpen.removeClass('open');
                    thisOpen.siblings().hide(0200);
                } else {
                    thisOpen.addClass('open');
                    thisOpen.siblings().show(0200);
                }
            });
        });
    </script>
@endsection
