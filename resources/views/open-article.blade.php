@extends('layouts.member')

@section('page-name', 'Home')

@section('content')

{{-- @include('member.partials.slider') --}}

<!-- Content ====== -->
<section id="content">
    <div class="container py-3">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10">


                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">

                        <div>
                            <div class="text-dark fs-3">
                                {{ $article->title ?? '' }}
                            </div>
                            <div class="mb-3">
                                <div class="">
                                    <strong>Author(s)</strong>: {{$article->member->fullname.", " ?? ''}}
                                    {{$article->author_name ? $article->author_name.", " : ''}}
                                </div>
                                <div class="">
                                    <strong>Corresponding Author(s):</strong> {!! $article->corresponding_authors ??
                                    '<i>None</i>' !!}
                                </div>
                                <div class="">
                                    <span class="kb-font-semibold">DOI Link:</span> <a href="{{ $article->doi_link }}"
                                        target="_blank">{{ $article->doi_link ?? '' }}</a>
                                </div>
                            </div>
                        </div>
                        <div>
                            @auth('member')
                            @if ($bookmark)
                            <i data-article="{{$article->id}}" class='bx bxs-bookmark bx-md bookmarked'></i>
                            @else
                            <i data-article="{{$article->id}}" class='bx bx-bookmark bx-md bookmarked'></i>
                            @endif
                            @endauth
                        </div>
                    </div>

                    <div class="card-body px-5 pb-5">

                        <ul class="d-flex gap-3 list-unstyled">
                            <li> <i class="fa-sharp fa-solid fa-file-pdf"></i>
                                <span>Get PDF</span>
                            </li>

                            <li> <i class="fa-sharp fa-solid fa-share-nodes"></i>
                                <span>Share</span>
                            </li>

                            <li> <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                                <span>Citation Alert</span>
                            </li>

                            <li> <i class="fa-sharp fa-solid fa-quote-right"></i>
                                <span>Get Citation</span>
                            </li>
                        </ul>

                        <hr>

                        <div class="mb-3 kb-prose kb-max-w-none" style="line-height: 25px;">

                            <div class="text-dark">
                                <h2>Abstract</h2>
                            </div>

                            {!! $article->last?->abstract ?? '' !!}

                            {{-- <p class="text-dark"> Copyrights &copy; 1984 Optical Society of America</p> --}}
                        </div>

                        <div class="">
                            @php
                            // $size = $article->last->upload_paper->size/1024;
                            // dd($size);
                            @endphp
                            <a href="{{ route('download-article', $article->id) }}" class="em-btn bg-dark" class="em-btn"> Download Article ({{ $article->paper_size }})
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- #content end -->


@endsection



@section('scripts')
<script>
    $(function () {
        $('p.openAbstract').click(function (e) {
            e.preventDefault();
            let thisOpen = $(this);
            if(thisOpen.hasClass('open')){
                thisOpen.removeClass('open');
                thisOpen.siblings().hide(0200);
            }else{
                thisOpen.addClass('open');
                thisOpen.siblings().show(0200);
            }
        });
    });
</script>
@endsection
