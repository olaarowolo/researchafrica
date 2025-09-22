<div>

    <hr>
    @forelse ($articles as $article)

        <div class="d-flex font-weight-bold align-items-center my-3">
            <span class="me-3">
                @if ($article->access_type == 1)
                    <i class="fa fa-unlock text-success me-2" aria-hidden="true"></i> Open
                    Access
                @else
                    <i class="fa fa-lock text-danger me-2" aria-hidden="true"></i> Close Access
                @endif
            </span> |
            <span class="mx-3"> {{ $article->article_category->category_name ?? '' }} </span>|
            <span class="ms-3">
                First published {{ date('F d, Y', strtotime($article->updated_at)) }}
            </span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="md:kb-w-2/3">
                <div class="fs-4">
                    <a href="{{ route('member.view-article', $article->id) }}"
                        class="hover:kb-text-orange-500">{{ $article->title }}</a>
                </div>
                <div>
                    <div class="">
                        {{-- <strong>Author(s)</strong>: {{ $article->member->fullname . ', ' ?? '' }}
                        {{ $article->author_name ? $article->author_name . ', ' : '' }}
                    </div>
                    <div class="">
                        <strong>Other Author(s):</strong> {!! $article->other_authors ?? '<i>None</i>' !!}
                    </div> --}}
                    <strong>Author(s){{ $article->member->fullname && $article->author_name ? 's' : '' }}</strong>:
                          
                                @php
                                  $fullname = $article->member->fullname;
                                  $displayFullname = $fullname !== '. .' ? $fullname : '';
                                @endphp
                              
                                @if ($displayFullname && $article->author_name)
                                  <strong>Author(s){{ $article->author_name ? 's' : '' }}</strong>: {{ $displayFullname }},
                                @endif
                              
                                @if ($article->author_name)
                                  {{ $article->author_name }},
                                @endif
                              
                                @if ($article->other_authors)
                                  {!! $article->other_authors !!},
                                @endif
                              </div>
                              @if ($article->member->fullname && $article->member->fullname !== ". .")
    <div>
        @if ($article->member->fullname !== "None")
            <strong>Corresponding Author:</strong>
        @endif
        {{ $article->member->fullname ?? '' }}
        @if ($article->member->first()->orchid_id)
            <sup>
                <a href="https://orcid.org/{{ $article->member->first()->orchid_id }}" style="display: inline;">
                    <img src="https://orcid.org/assets/vectors/orcid.logo.icon.svg" alt="ORCID Logo"
                        style="width: 13px; height: 13px; display: inline;">
                </a>
            </sup>
        @endif
    </div>
@elseif ($article->member->fullname == "None")
    <div>
        <i>None</i>
    </div>
@endif
                    {{-- <div class="">
                        <strong>Corresponding Author(s):</strong> {!! $article->corresponding_authors ?? '<i>None</i>' !!}
                    </div> --}}
                    <div class="d-flex kb-flex-wrap font-weight-bold align-items-center">
                        @if ($article->doi_link)
                            <span class="me-3">
                                <span class="kb-font-semibold">DOI Link:</span> <a href="{{ $article->doi_link ?? '' }}"
                                    target="_blank">{{ $article->doi_link ?? '' }}</a>
                            </span>
                        @endif
                        @if ($article->volume)
                            {!! $article->doi_link ? ' &nbsp; | &nbsp; ' : '' !!}
                            <span class="me-3">
                                <span class="kb-font-semibold">Volume: </span> {!! $article->volume ?? '<i>None</i>' !!}
                            </span>
                        @endif
                        @if ($article->issue_no)
                            {!! $article->doi_link || $article->volume ? ' &nbsp; | &nbsp; ' : '' !!}
                            <span class="me-3">
                                <strong> Issue No:</strong> {!! $article->issue_no ?? '<i>None</i>' !!}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end align-items-center md:kb-w-1/3">
                @auth('member')
                    <div>
                        @auth('member')
                            @php
                                $bookmark = \App\Models\Bookmark::where('article_id', $article->id)
                                    ->where('member_id', auth('member')->id())
                                    ->exists();
                            @endphp
                            @if ($bookmark)
                                <i data-article="{{ $article->id }}" class='bx bxs-bookmark bx-md bookmarked'></i>
                            @else
                                <i data-article="{{ $article->id }}" class='bx bx-bookmark bx-md bookmarked'></i>
                            @endif
                        @endauth
                    </div>
                    <div>
                        <a href="{{ route('member.view-article', $article->id) }}"
                            class="border border-2 {{ $article->access_type == 1 ? 'border-success btn btn-outline-success' : 'border-danger btn btn-outline-danger' }}  px-3 py-1 fs-5">
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
                        class="border border-2 {{ $article->access_type == 1 ? 'border-success btn btn-outline-success' : 'border-danger btn btn-outline-danger' }}  px-3 py-1 fs-5">
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

</div>
