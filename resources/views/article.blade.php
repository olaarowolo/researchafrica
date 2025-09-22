@extends('layouts.member')

@section('page-name', $article->title)


<!-- OG Meta Content Start ====== -->
{{-- <meta name="description" content="{!! $article->last->abstract ?? '' !!}" /> --}}

<!--detailed robots meta https://developers.google.com/search/reference/robots_meta_tag -->
<meta name="robots" content="index, follow, max-snippet: -1, max-image-preview:large, max-video-preview: -1" />
<link rel="canonical" href="{{ url()->current() }}" />

<!--open graph meta tags for social sites and search engines-->
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="website" />
<meta property="og:title"
    content="{{ $article->title }} - {{ $article->member->last_name ?? '' }} {{ $article->member->first_name ?? '' }}, {{ $article->other_authors }}| publ.by Research Africa" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:site_name"
    content="{{ $article->title }} - {{ $article->member->last_name ?? '' }} {{ $article->member->first_name ?? '' }}, {{ $article->other_authors }}| publ.by Research Africa" />
{{-- <meta property="og:image" content="{{ $article->journal_category->cover_image}} --}}
{{-- <meta property="og:image:secure_url" content="{{ $article->journal_category->cover_image}} --}}
<meta property="og:image:width" content="600" />
<meta property="og:image:height" content="660" />

<!--twitter description-->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:description" content="{!! strip_tags($article->last->abstract) ?? '' !!}" />
<meta name="twitter:title"
    content="{{ $article->title }} - {{ $article->member->last_name ?? '' }} {{ $article->member->first_name ?? '' }} | publ.by Research Africa" />
<meta name="twitter:site" content="{{ url()->current() }}" />
<meta name="twitter:image" content="{{ $article->journal_category->cover_image }}
        <meta name="twitter:creator"
    content="@ResearchAfriPub" />


<!-- Meta Content Start ====== -->
<link rel="schema.DC" href="http://purl.org/DC/elements/1.0/">
</link>
<meta name="citation_journal_title" content="{{ $article->journal_category->category_name ?? '' }}">
</meta>
<meta name="citation_volume" content="{{ $article->volume ?? '' }}">
</meta>
<meta name="citation_issue" content="{{ $article->issue_no ?? '' }}">
</meta>
<meta name="dc.Title" content="{{ $article->title }}">
</meta>
<meta name="Abstract" content="{{ strip_tags($article->last->abstract) ?? '' }}">
</meta>
<meta name="Description" content="{{ strip_tags($article->last->abstract) ?? '' }}">
</meta>
<meta name="dc.Creator"
    content="{{ $article->member->last_name ?? '' }}, {{ $article->member->first_name ?? '' }}, {{ $article->other_authors }}">
</meta>
<meta name="dc.Contributor" content="{{ $article->member->fullname ?? '' }}">
</meta>
<meta name="dc.Publisher" content="Research Africa Publications">
</meta>
<meta name="dc.Date" scheme="WTN8601" content="{{ date('Y', strtotime($article->published_online)) }}">
</meta>
<meta name="dc.Type" content="research-article">
</meta>
<meta name="dc.Format" content="text/HTML">
</meta>
<meta name="dc.Identifier" scheme="doi" content="">
</meta>
<meta name="dc.Language" content="EN">
</meta>
<meta name="dc.Coverage" content="world">
</meta>
<meta name="citation_firstpage" content="{{ $article->first_page ?? '' }}">
</meta>
<meta name="citation_lastpage" content="{{ $article->last_page ?? '' }}">
</meta>
<meta name="citation_issn" content="{{ $article->journal_category->issn ?? '' }}">
</meta>
<meta name="citation_doi" content="{{ $article->doi ?? '' }}">
</meta>
<meta name="dc.Rights" content="CC BY 4.0 DEED: Attribution 4.0 International">
</meta>
<meta name="dc.FirstPublished" scheme="WTN8601"
    content="{{ $article->publish_date ? date('M d, Y', strtotime($article->publish_date)) : date('M d, Y', strtotime($article->updated_at)) }}">
</meta>
<meta name="dc.PublishedOnline" scheme="WTN8601"
    content="{{ date('M j, Y', strtotime($article->published_date ?? now())) }}">
</meta>
<meta name="citation_author_orcid" content="{{ $article->member?->first()?->orchid_id }}">
</meta>
<meta property="og:image"
    content="{{ $article->journal_category->cover_image }}
        </meta>
<!-- Meta Content End ====== -->



@section('content')

    {{-- @include('member.partials.slider') --}}

    <!-- Content ====== -->
    <section id="content">
<div class="container py-3">
    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header">
                    <div style="text-align: left; padding-left: 25px; padding-top: 25px; padding-right: 25px;">
                        <div class="text-dark kb-text-xl kb-font-medium md:kb-text-3xl">
                            {{ $article->title ?? '' }}
                        </div>
                        <div class="kb-flex kb-flex-col md:kb-flex-row kb-justify-between md:kb-items-center">
                            <div class="mb-3" {{ $article->journal_category->category_link ?? '' }} <span
                                class="mx-1">
                                <a href="{{ $article->journal_category->journal_url ?? '' }}"> <span class="mx-1">
                                    <i> {{ $article->journal_category->category_name ?? '' }} </i> </a>

                                &nbsp |
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
                                {{-- </span>


                                @if ($article->volume)
                                    {!! $article->doi_link ? '   ' : '' !!}
                                    <span class="me-1">
                                        <span class=> <strong> Volume:</strong> </span> {!! $article->volume ?? '<i>None</i>' !!}
                                    </span>
                                @endif
                                @if ($article->issue_no)
                                    {!! $article->doi_link | $article->volume ? '   &nbsp; ' : '' !!}
                                    <span class="me-1">
                                        <strong> Issue:</strong> &nbsp;{!! $article->issue_no ?? '<i>None</i>' !!}
                                    </span>
                                @endif --}}
                                <div class="">
                                    {{ $article->member->fullname ?? '' }}
                                    @php
                                        $fullname = $article->member->fullname;
                                        $displayFullname = $fullname !== '. .' ? $fullname : '';
                                    @endphp

                                    @if ($displayFullname && $article->author_name)
                                        <strong>Author(s){{ $article->author_name ? 's' : '' }}</strong>:
                                        {{ $displayFullname }};
                                    @endif

                                    @if ($article->author_name)
                                        {{ $article->author_name }}
                                    @endif

                                    @if ($article->other_authors)
                                        {!! $article->other_authors !!}
                                    @endif
                                </div>
                                
                                {{-- String Funtion --}}

                                {{-- <div class="">
                                        <strong>Author(s)</strong>: {{ $article->member->fullname ?? '' }}
                                        @if ($article->member?->first()?->orchid_id)
                                            <sup>
                                                <a href="https://orcid.org/{{ $article->member?->first()?->orchid_id }}"
                                                    style="display: inline;">
                                                    <img src="https://orcid.org/assets/vectors/orcid.logo.icon.svg"
                                                        alt="ORCID Logo"
                                                        style="width: 13px; height: 13px; display: inline;">
                                                </a>
                                            </sup>
                                        @endif
                                        {{ $article->author_name ? ', ' . $article->author_name . ', ' : '' }}
                                    </div>
                                    
                                    @if ($article->other_authors && $article->other_authors != 'None')
                                    <div class="">
                                        <strong>Author(s):</strong> {!! $article->other_authors !!}
                                    </div>
                                    @endif --}}



                                {{-- <div class="">
                                        <strong>Corresponding Author:</strong>
                                        @if ($article->member->fullname)
                                            {{ $article->member->fullname ?? '' }}
                                           @if ($article->member?->first()?->orchid_id)

                                            <sup>
                                                <a href="https://orcid.org/{{ $article->member?->first()?->orchid_id }}"
                                                    style="display: inline;">
                                                    <img src="https://orcid.org/assets/vectors/orcid.logo.icon.svg"
                                                        alt="ORCID Logo"
                                                        style="width: 13px; height: 13px; display: inline;">
                                                </a>
                                            </sup>
                                             @endif

                                        @else
                                            <i>None</i>
                                        @endif
                                    </div> --}}
                                @if ($article->member->fullname && $article->member->fullname !== '. .')
                                    <div>
                                        @if ($article->member->fullname !== 'None')
                                            <strong>Corresponding Author:</strong>
                                        @endif
                                        {{ $article->member->fullname ?? '' }}
                                        @if ($article->member->first()->orchid_id)
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
                                <div class="">
                                    <span class="kb-font-semibold"></span> <a href="{{ $article->doi_link }}"
                                        target="_blank">{{ $article->doi_link ?? '' }}</a>
                                </div>
                            </div>
                            <div class="kb-flex gap-2 kb-self-end md:kb-self-start kb-items-center">
                                <div>
                                    @auth('member')
                                        @if ($bookmark)
                                            <i data-article="{{ $article->id }}"
                                                class='bx bxs-bookmark bx-md bookmarked'></i>
                                        @else
                                            <i data-article="{{ $article->id }}"
                                                class='bx bx-bookmark bx-md bookmarked'></i>
                                        @endif
                                    @endauth
                                    {{-- <i class="fa fa-bookmark f" aria-hidden="true"></i> --}}
                                </div>
                                <div class="fs-3 fw-bold"
                                    style="text-align: left; padding-left: 25px; padding-top: 25px; padding-right: 25px;">
                                    @if ($article->access_type == 2)
                                        &#x20A6; {{ number_format($article->amount ?? 0) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-md-5 pb-5">



                    <ul class="kb-flex kb-flex-wrap gap-2 list-unstyled" style="font-size: 80%">


                        <li >

                            @if ($article->access_type == 1)
                                <i class="fa fa-unlock text-success me-2" aria-hidden="true"></i>
                                <span class="">
                                    Open Access
                                </span>
                            @else
                                <i class="fa fa-lock text-danger me-2" aria-hidden="true"></i>
                                <span class="">
                                    Close Access
                                </span>
                            @endif
                        </li>
                        <li class="kb-cursor-pointer">
                            @if ($article->access_type == 2)
                                @if ($purchased)
                                    <a href="{{ route('download-article', $article->id) }}">
                                        <i class="fa-sharp fa-solid fa-file-pdf"></i>
                                        <span>Get PDF</span>
                                    </a>
                                @else
                                    @guest('member')
                                        <span class="loginButton">
                                            <i class="fa-sharp fa-solid fa-file-pdf"></i>
                                            <span>Get PDF</span>
                                        </span>
                                    @endguest
                                @endif
                            @else
                                <a href="{{ route('download-article', $article->id) }}">
                                    <i class="fa-sharp fa-solid fa-file-pdf"></i>
                                    <span>Get PDF</span>
                                </a>
                            @endif
                        </li>
                        <li data-mdb-toggle="modal" data-mdb-target="#exampleModal" class="kb-cursor-pointer"> <i
                                class="fa-sharp fa-solid fa-share-nodes"></i>
                            <span>Share</span>
                        </li>

                        <li> <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                            <span>Citation Alert</span>
                        </li>

                        <li> <i class="fa-sharp fa-solid fa-quote-right"></i>
                            <span>Get Citation</span>
                        </li>

                        <li> <i class="fa-sharp fa-solid fa-eye"></i>
                            <span>{{ $article->views ? ($article->views->view >= 1000 ? round($article->views->view / 1000, 3) . 'K' : $article->views->view) : 0 }}</span>
                        </li>

                        <li> <i class="fa-sharp fa-solid fa-download"></i>
                            <span>{{ $article->downloads ? ($article->downloads->download >= 1000 ? round($article->downloads->download / 1000, 3) . 'K' : $article->downloads->download) : 0 }}</span>
                        </li>

                        <li style="text-align: left; padding-left: 0px;" class="mobile-hidden">
                            @if ($article->publish_date)
                                <p class="">
                                    First Published (print):
                                    <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                    <strong>
                                        {{ $article->publish_date ? date('M d, Y', strtotime($article->publish_date)) : date('M d, Y', strtotime($article->updated_at)) }}<br>
                                    </strong>
                                </p>
                            @endif
                        </li>
                        <li style="text-align: left; padding-left: 0px;" class="mobile-hidden">
                            <p class="">
                                Submitted :
                                <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                <strong> {{ date('M j, Y', strtotime($article->created_at ?? now())) }} <br>
                                </strong>
                            </p>
                        </li>
                        <li style="text-align: left; padding-left: 0px;"class="mobile-hidden">
                            <p class="">
                                Published:
                                <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                <strong>
                                    {{ date('M j, Y', strtotime($article->published_date ?? now())) }} <br>
                                </strong>
                            </p>
                        </li>
                        <style>
                            @media (max-width: 768px) {
                                .mobile-hidden {
                                    display: none;
                                }
                            }
                        </style>


                        </li>

                        </li>

                    </ul>

                    <hr>

                    <div class="mb-3 kb-prose kb-max-w-none" style="line-height: 25px;">

                        <div class="text-dark">
                            <h2>Abstract</h2>
                        </div>

                        {!! $article->last->abstract ?? '' !!}

                        {{-- <p class="text-dark"> Copyrights &copy; 1984 Optical Society of America</p> --}}
                    </div>

                    @php
                        // $size = $article->last->upload_paper->size/1024;
                    @endphp

                    @if ($article->access_type == 2)
                        @if ($purchased)
                            <div class="">
                                <a href="{{ route('download-article', $article->id) }}" class="em-btn bg-danger"
                                    class="em-btn"> Download Article ({{ $article->paper_size }}) </a>
                            </div>
                        @else
                            @auth('member')
                                <form method="" action="" id="paymentForm">
                                    @csrf
                                    <button onclick="payWithPaystack()" class="em-btn bg-danger">
                                        <i class="fa fa-lock" aria-hidden="true"></i>
                                        Get Access ({{ $article->paper_size }}) </button>
                                </form>
                            @else
                                <button class="em-btn bg-danger loginButton">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                    Get Access ({{ $article->paper_size }}) </button>
                            @endauth
                        @endif
                    @else
                        <a href="{{ route('download-article', $article->id) }}" class="em-btn bg-dark"
                            class="em-btn">
                            Download Article ({{ $article->paper_size }})
                        </a>

                        <ul class="kb-flex kb-flex-wrap gap-3 list-unstyled">
                            <li
                                style="text-align: left; 2px; padding-top: 20px;>
                                                <p class="">
                                Published Online:
                                <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                <strong>
                                    {{ date('M j, Y', strtotime($article->published_date ?? now())) }} <br>
                                </strong>
                                </p>
                            </li>
                            <li style="text-align: left; 2px; padding-top: 20px">
                                @if ($article->publish_date)
                                    <p class="">
                                        First Published (print):
                                        <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                        <strong>
                                            {{ $article->publish_date ? date('M d, Y', strtotime($article->publish_date)) : date('M d, Y', strtotime($article->updated_at)) }}<br>
                                        </strong>
                                    </p>
                                @endif
                            </li>


                            <style>
                                @media (max-width: 768px) {
                                    .mobile-hidden {
                                        display: none;
                                    }
                                }
                            </style>
                            </li>
                            </li>

                        </ul>

                        <hr>

                    @endif


                </div>
            </div>
        </div>
    </div>
</div>
</section>
<!-- #content end -->




<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title kb-text-xl kb-font-bold" id="exampleModalLabel">Share</h5>
                <i type="button" class="fa fa-close kb-text-red-600" data-mdb-dismiss="modal"
                    aria-label="Close"></i>
            </div>
            <div class="modal-body">
                <div class="row kb-justify-center">
                    <div class="col-12">
                        <div class="mb-3 kb-flex kb-gap-2 kb-justify-center">
                            <input type="text" class="form-control kb-rounded"
                                value="{{ route('member.view-article', $article->id) }}" readonly />
                            <div class="kb-rounded kb-text-white kb-bg-black kb-p-2 kb-flex kb-items-center"
                                id="copyBtn">
                                <i class='bx bx-copy bx-md ' title="Copy"></i>
                                <i class='bx bx-check bx-md kb-hidden' title="Copied"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 text-center">
                        <a href="https://www.facebook.com/sharer.php?u={{ route('member.view-article', $article->id) }}"
                            target="_blank" class="kb-text-[#3C5C9C]/50 hover:kb-text-[#3C5C9C]">
                            <i class='bx bxl-facebook-circle bx-lg'></i>
                            <p>Facebook</p>
                        </a>
                    </div>
                    <div class="col-3 kb-text-center">
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ route('member.view-article', $article->id) }}"
                            target="_blank" class="kb-text-[#048CCC]/50 hover:kb-text-[#048CCC]">
                            <i class='bx bxl-linkedin-square bx-lg'></i>
                            <p>Linkedin</p>
                        </a>
                    </div>
                    <div class="col-3 kb-text-center">
                        <a href="whatsapp://send/?text={{ $article->title }}%20{{ route('member.view-article', $article->id) }}&text={{ $article->title }}"
                            target="_blank" class="kb-text-[#44C553]/50 hover:kb-text-[#44C553]">
                            <i class='bx bxl-whatsapp bx-lg'></i>
                            <p>Whatsapp</p>
                        </a>
                    </div>
                    <div class="col-3 kb-text-center">
                        <a href="https://t.me/share/url?url={{ route('member.view-article', $article->id) }}&text={{ $article->title }}"
                            target="_blank" class="kb-text-[#1C96D3]/50 hover:kb-text-[#1C96D3]">
                            <i class='bx bxl-telegram bx-lg'></i>
                            <p>Telegram</p>
                        </a>
                    </div>
                    <div class="col-3 kb-text-center">
                        <a href="https://twitter.com/intent/tweet?url={{ route('member.view-article', $article->id) }}&text={{ $article->title }}&via=me&hashtags=#research_africa"
                            target="_blank" class="kb-text-[#0E8DEE]/50 hover:kb-text-[#0E8DEE]">
                            <i class='bx bxl-twitter bx-lg'></i>
                            <p>Twitter</p>
                        </a>
                    </div>
                    <div class="col-3 kb-text-center">
                        <a href="https://reddit.com/submit?url={{ route('member.view-article', $article->id) }}&title={{ $article->title }}"
                            target="_blank" class="kb-text-[#FB5104]/50 hover:kb-text-[#FB5104]">
                            <i class='bx bxl-reddit bx-lg'></i>
                            <p>Reddit</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
    const paymentForm = document.getElementById('paymentForm');
    paymentForm.addEventListener("submit", payWithPaystack, false);

    function payWithPaystack(e) {
        e.preventDefault();

        let handler = PaystackPop.setup({
            key: 'pk_live_d299bbb438852a069e481feda61b38197df6bd01', // Replace with your public key
            email: "{{ auth('member')->user()->email_address ?? '' }}",
            amount: "{{ $article->amount * 100 }}",
            currency: 'NGN',
            ref: 'PA-' + Math.floor((Math.random() * 1000000000) +
                1
            ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
            // label: "Optional string that replaces customer email"
            onClose: function() {
                Swal.fire(
                    'Canceled',
                    '',
                    'error'
                )
            },
            callback: function(response) {


                $.ajax({
                    url: '/verify_transaction/' + response.reference,
                    method: 'get',
                    data: {
                        "member_id": "{{ auth('member')->id() }}",
                        "article_id": "{{ $article->id }}",
                        "amount": "{{ $article->amount ?? 0 }}",
                        "reference": response.reference
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            Swal.fire(
                                'Payment Successful',
                                '',
                                'success'
                            )
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            Swal.fire(
                                'Something went wrong',
                                '',
                                'error'
                            )
                        }
                    }
                });
            }
        });

        handler.openIframe();
    }
</script>
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

        $('.loginButton').click(function(e) {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Login to continue',
            });

        });

        $('#copyBtn').click(function(e) {
            e.preventDefault();
            let thisBtn = $(this);

            let val1 = thisBtn.siblings('input');
            val1.select();

            // Copy the text inside the text field
            navigator.clipboard.writeText(val1.val());

            thisBtn.removeClass('kb-bg-black').addClass('kb-bg-green-700');

            thisBtn.children('i:nth-child(1)').hide(0100, () => {
                thisBtn.children('i:nth-child(2)').show(0500);
            });
            setTimeout(() => {
                thisBtn.removeClass('kb-bg-green-700').addClass('kb-bg-black');

                thisBtn.children('i:nth-child(1)').show(0100, () => {
                    thisBtn.children('i:nth-child(2)').hide(0500);
                });
            }, 5000);

        });

    });
</script>
@endsection
