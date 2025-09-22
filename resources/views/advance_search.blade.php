@extends('layouts.member')

@section('page-name', 'Advance Search')


<!-- OG Meta Content Start ====== -->
{{-- <meta name="description" content="{!! $article->last->abstract ?? '' !!}" /> --}}

<!--detailed robots meta https://developers.google.com/search/reference/robots_meta_tag -->
<meta name="robots" content="index, follow, max-snippet: -1, max-image-preview:large, max-video-preview: -1" />
<link rel="canonical" href="{{ url()->current() }}" />

<!--open graph meta tags for social sites and search engines-->
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="website" />
<meta property="og:title"
    content="@isset($article){{ $article->title ?? 'Advance Search' }} - {{ $article->member->last_name ?? '' }} {{ $article->member->first_name ?? '' }} | @endisset publ.by Research Africa" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:site_name"
    content="@isset($article){{ $article->title ?? 'Advance Search' }} - {{ $article->member->last_name ?? '' }} {{ $article->member->first_name ?? '' }} | @endisset publ.by Research Africa" />
<meta property="og:image"
    content="@isset($article){{ $article?->journal_category?->cover_image ?? '' }}@endisset" />
<meta property="og:image:secure_url"
    content="@isset($article){{ $article?->journal_category?->cover_image ?? '' }}@endisset" />
<meta property="og:image:width" content="600" />
<meta property="og:image:height" content="660" />

<!--twitter description-->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:description"
    content="@isset($article){{ strip_tags($article->last->abstract ?? '') }}@endisset" />
<meta name="twitter:title"
    content="@isset($article){{ $article->title ?? 'Advance Search' }} - {{ $article->member->last_name ?? 'Research Afica' }} {{ $article->member->first_name ?? '' }} | @endisset publ.by Research Africa" />
<meta name="twitter:image"
    content="@isset($article){{ $article->journal_category->cover_image ?? '' }}@endisset" />
<meta name="twitter:site" content="{{ url()->current() }}" />
<meta name="twitter:creator" content="@ResearchAfriPub" />


<!-- Meta Content Start ====== -->
<link rel="schema.DC" href="http://purl.org/DC/elements/1.0/">
</link>
<meta name="citation_journal_title"
    content="@isset($article) {{ $article->journal_category->category_name ?? '' }} @endisset" />
<meta name="citation_volume" content="@isset($article) {{ $article->volume ?? '' }} @endisset" />
<meta name="citation_issue" content="@isset($article) {{ $article->issue_no ?? '' }} @endisset" />
<meta name="dc.Title" content="@isset($article)  {{ $article->title ?? '' }} @endisset" />
<meta name="Abstract"
    content="@isset($article)  {{ strip_tags($article->last->abstract ?? '') }} @endisset" />
<meta name="Description"
    content="@isset($article) {{ strip_tags($article->last->abstract ?? '') }} @endisset" />
<meta name="dc.Creator"
    content="@isset($article) {{ $article->member->last_name ?? '' }}, {{ $article->member->first_name ?? '' }};, {{ $article->other_authors ?? '' }} @endisset" />
<meta name="dc.Contributor" content="{{ $article->member->fullname ?? '' }}" />
<meta name="dc.Publisher" content="Research Africa Publications" />
<meta name="dc.Date" scheme="WTN8601"
    content="@isset($article) {{ date('Y', strtotime($article->published_online ?? now())) }} @endisset" />
<meta name="dc.Type" content="research-article" />
<meta name="dc.Format" content="text/HTML" />
<meta name="dc.Identifier" scheme="doi" content="" />
<meta name="dc.Language" content="EN" />
<meta name="dc.Coverage" content="world" />
<meta name="citation_firstpage"
    content="@isset($article) {{ $article->first_page ?? '' }} @endisset" />
<meta name="citation_lastpage"
    content="@isset($article) {{ $article->last_page ?? '' }} @endisset" />
<meta name="citation_issn"
    content="@isset($article) {{ $article->journal_category->issn ?? '' }} @endisset" />
<meta name="citation_doi" content="@isset($article) {{ $article->doi ?? '' }} @endisset" />
<meta name="dc.Rights" content="CC BY 4.0 DEED: Attribution 4.0 International" />
<meta name="dc.FirstPublished" scheme="WTN8601"
    content="@isset($article) {{ $article->publish_date ? date('M d, Y', strtotime($article->publish_date ?? now())) : date('M d, Y', strtotime($article->updated_at ?? now())) }} @endisset " />
<meta name="dc.PublishedOnline" scheme="WTN8601"
    content="@isset($article) {{ date('M j, Y', strtotime($article->published_date ?? now())) }} @endisset" />
<meta name="citation_author_orcid"
    content="@isset($article) {{ $article->member?->first()?->orchid_id ?? '' }} @endisset" />
<meta property="og:image"
    content="@isset($article) {{ $article->journal_category->cover_image ?? '' }} @endisset" />
<!-- Meta Content End ====== -->

@section('content')
    <!-- Content ============================================= -->
    <div class="bg-dark p-3">
        <div claass="container my-5">
            <p class="text-white fs-4"> Advanced Search </p>
        </div>
    </div>
    <section>
        <h1 class="displayText"></h1>
        <div class="mx-md-5 card shadow my-3">
            <form action="" method="get" class="card-body">
                <div class="row d-flex align-items-center g-2 mb-3">
                    <div class="col-md-3 text-center">Title Terms</div>
                    <div class="col-md-6">
                        <div class="">
                            {{-- <label for="content" class="form-label">Name</label> --}}
                            <input type="text" class="form-control" name="content" id="content"
                                aria-describedby="contentId" value="{{ request('content') }}"
                                placeholder="Content terms">
                            <small id="contentId" class="form-text text-muted"></small>
                        </div>
                    </div>
                </div>

                <div class="row d-flex align-items-center  mb-3">
                    <div class="col-md-3 text-center">
                        Article Published
                    </div>
                    <div class="col-md-6">
                        <div class='row'>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="from_date" id="from_date"
                                    value="{{ request('from_date') }}" placeholder="Start Year" readonly>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="to_date" id="to_date"
                                    value="{{ request('to_date') }}" placeholder="End Year" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2">
                    <div class="col-md-3 text-center">
                        Access Type
                    </div>
                    <div class="col-md-6">
                        <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                            <input type="radio" class="btn-check" name="access"
                                {{ request('access') == 'on' ? 'checked' : '' }} id="check1" autocomplete="off"
                                checked>
                            <label class="btn btn-outline-dark" for="check1"> All Content</label>

                            @foreach (\App\Models\Article::ACCESS_TYPE as $id => $key)
                                <input type="radio" class="btn-check" name="access" value="{{ $id }}"
                                    {{ request('access') == $id ? 'checked' : '' }} id="access{{ $id }}"
                                    autocomplete="off">
                                <label class="btn btn-outline-dark" for="access{{ $id }}">
                                    {{ $key }}</label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center align-items-center g-2">
                    <div class="col-md-5 text-center pt-3">
                        <button type="submit" class="em-btn bg-secondary" id="subnitBtns" disabled
                            style="cursor: not-allowed;">Search</button>
                    </div>
                </div>

            </form>
        </div>
        <div class="">

            <div class="container">
                <div class="row mb-3">
                    @if ($search)

                        @if (!is_null($count))
                            <h4>Your Search result ({{ $count }})</h4>
                        @endif
                        <div class="col-md-3">
                            @if (request('content') && request()->has('content'))
                                <div class=" border border-2 border-secondary rounded p-2">
                                    <!-- Horizontal under breakpoint -->
                                    <ul class="list-group list-group-flush">
                                        @foreach ($categories as $item)
                                            <a href="{{ request()->fullUrlWithQuery(['with_category' => $item->id]) }}"
                                                class="list-group-item d-flex justify-content-between">
                                                <span>{{ $item->category_name ?? '' }}</span>
                                                <span>({{ $item->article_count }})</span>
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
                        <div class="col-md-9 p-1 p-md-3">

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
                                        {{ $articles->links() }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
    <!-- #content end -->
@endsection



@section('scripts')
    <script>
        $(function() {

            $('#from_date').datepicker({
                autoHide: true,
                format: 'yyyy'
            });
            $('#to_date').datepicker({
                autoHide: true,
                format: 'yyyy',
                startDate: $('#from_date').val(),
            });

            // $('#from_date').change(function(e) {
            //     e.preventDefault();

            //     Swal.fire(
            //         $('#from_date').val(),
            //       '',
            //       'success'
            //     )
            //     $('#to_date').val(null);

            // });


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


            $('#content').keyup(function(e) {
                console.log($(this).val());
                if ($(this).val() != '') {
                    $('#subnitBtns').removeAttr('disabled').css('cursor', 'pointer').addClass(' bg-dark')
                        .removeClass(' bg-secondary');
                } else {
                    $('#subnitBtns').attr('disabled', true).css('cursor', 'not-allowed').addClass(
                        ' bg-secondary').removeClass(' bg-dark');;
                }
            });

            if ($('#content').val() != '') {
                $('#subnitBtns').removeAttr('disabled').css('cursor', 'pointer').addClass(' bg-dark').removeClass(
                    ' bg-secondary');
            }

        });
    </script>
@endsection
