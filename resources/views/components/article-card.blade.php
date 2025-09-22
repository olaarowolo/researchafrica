@foreach ($articles as $article)
    <div class="{{ $class ?? '' }}">
        <div class="card hvr-glow mb-3 card-article">
            <div class="card-body">
                <div class="">
                    <div class="">
                        <div class="d-flex kb-flex-wrap font-weight-bold align-items-center mb-2" style="font-size: 80%">
                            <span class="me-1">
                                @if ($article->access_type == 1)
                                    <i class="fa fa-unlock text-success me-2" aria-hidden="true"></i>
                                    <span class="">
                                        Open Access &nbsp;
                                    </span>
                                @else
                                    <i class="fa fa-lock text-danger me-2" aria-hidden="true"></i>
                                    <span class="">
                                        Close Access
                                    </span>
                                @endif

                            </span> |
                            <a href="{{ $article->article_category->category_link ?? '#' }}"
                                class="mx-1">{{ $article->article_category->category_name ?? '' }}</a>
                            </span> >
                            <a href="{{ $article->journal_category->journal_url ?? '' }}"> <span class="mx-1">
                                    {{ $article->journal_category->category_name ?? '' }}</a>
                            </span> |
                            @if ($article->volume)
                                {!! $article->doi_link ? ' &nbsp;  ' : '' !!}
                                <span class="me-1">
                                    <span class=>&nbsp; Vol: </span> {!! $article->volume ?? '<i>None</i>' !!}
                                </span>
                            @endif
                            @if ($article->issue_no)
                                {!! $article->doi_link | $article->volume ? '  , &nbsp; ' : '' !!}
                                <span class="me-1">
                                    No: {!! $article->issue_no ?? '<i>None</i>' !!}
                                </span>
                            @endif
                        </div>
                        <div class="d-flex kb-flex-wrap font-weight-bold align-items-center mb-2"style="font-size: 80%">
                            <span class="me-3">

                                <strong>Published online:</strong> <br>
                                {{ $article->published_online->format('M d, Y') }}

                            </span>
                            @if ($article->publish_date)
                                <span class="ms-3">
                                    <strong>First published (print):</strong> <br>
                                    {{ date('F Y', strtotime($article->publish_date)) }}
                                </span>
                            @endif


                            <style>
                                @media only screen and (max-width: 600px) {

                                    .me-3,
                                    .ms-3 {
                                        display: block;
                                        text-align: left;
                                    }
                                }
                            </style>

                        </div>

                        <div class="kb-text-xl md:text-4xl kb-w-full ">
                            <a class=" kb-text-ellipsis" style="color: inherit"
                                href="{{ route('member.view-article', $article->id) }}">{{ $article->title }}</a>
                        </div>

                        <div>
                            <div style="font-size: 80%">
                                @if ($article->member->fullname !== '. .')
                                    {{ $article->member->fullname }}
                                @endif
                                @if ($article->author_name)
                                    {{ $article->author_name }};
                                @endif
                            
                                @if ($article->other_authors)
                                    @if ($article->member->fullname !== '. .' || $article->author_name)
                                        ,
                                    @endif
                                    {!! $article->other_authors !!}
                                @endif
                            </div>
                            @if ($article->member->fullname)
                                <div style="font-size: 80%">
                                    <strong>Corresponding Author:</strong>
                                    @if ($article->member->fullname !== '. .')
                                        {{ $article->member->fullname }}
                                    @else
                                        <i>Data unavailable.</i>
                                        @endif @if ($article->member->first()->orchid_id)
                                            <sup>
                                                <a href="https://orcid.org/{{ $article->member->first()->orchid_id }}"
                                                    style="display: inline;">
                                                    <img src="https://orcid.org/assets/vectors/orcid.logo.icon.svg"
                                                        alt="ORCID Logo"
                                                        style="width: 13px; height: 13px; display: inline;">
                                                </a>
                                            </sup>
                                        @endif
                                </div>
                            @elseif ($article->member->fullname == 'None')
                                <div>
                                    <i>None</i>
                                </div>
                            @endif
                        </div>
                        <div class="d-flex kb-flex-wrap font-weight-bold align-items-center"style="font-size: 80%">
                            @if ($article->doi_link)
                                <span class="me-3">
                                    <span class="kb-font-semibold"></span> <a href="{{ $article->doi_link ?? '' }}"
                                        target="_blank">{{ $article->doi_link ?? '' }}</a>
                                </span>
                            @endif
                            @if ($article->volume)
                                {!! $article->doi_link ? ' &nbsp; ' : '' !!}
                            @endif
                            @if ($article->issue_no)
                                {!! $article->doi_link || $article->volume ? '' : '' !!}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-2 kb-flex kb-justify-between kb-items-center">

                    <div class="mb-3">
                        <button class="openAbstract em-btn bg-dark open" name="{{ $article->id }}"
                            style="cursor: pointer">Show
                            Abstract</button>
                    </div>
                    <div class="mb-3">
                        <div class="kb-flex kb-flex-row-reverse md:kb-flex-row kb-justify-between kb-items-center">
                            @auth('member')
                                @php
                                    $bookmark = \App\Models\Bookmark::where('article_id', $article->id)
                                        ->where('member_id', auth('member')->id())
                                        ->exists();
                                @endphp
                                @if ($bookmark)
                                    <i class='bx bxs-bookmark bx-md bookmarked' data-article="{{ $article->id }}"></i>
                                @else
                                    <i class='bx bx-bookmark bx-md bookmarked' data-article="{{ $article->id }}"></i>
                                @endif
                            @endauth
                            <div>
                                <a class="border border-2 em-btn-ring {{ $article->access_type == 1 ? 'border-success btn-outline-success text-success' : 'border-danger btn-outline-danger text-danger' }}"
                                    href="{{ route('member.view-article', $article->id) }}">
                                    @if ($article->access_type == 1)
                                        Get Article
                                        <i class="fa fa-download" aria-hidden="true"></i>
                                    @else
                                        Get Access
                                        <i class="fa fa-lock" aria-hidden="true"></i>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mt-2 p-md-4 p-2 rounded kb-prose kb-max-w-none" id="abstract{{ $article->id }}"
                        style="background-color: #7c7c7c49;display: none">
                        <h3>Abstract</h3>
                        {!! $article->last->abstract !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@push('component')
    <script>
        $(function() {
            $('button.openAbstract').click(function(e) {
                e.preventDefault();
                let thisOpen = $(this);

                let getId = thisOpen.attr('name');

                if (thisOpen.hasClass('open')) {
                    thisOpen.removeClass('open').text('Hide Abstract');
                    $('#abstract' + getId).show(0200);

                } else {
                    thisOpen.addClass('open').text('Show Abstract');
                    $('#abstract' + getId).hide(0200);
                }
            });
        });
    </script>
@endpush
