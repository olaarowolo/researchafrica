@extends('layouts.member')

@section('page-name', 'Home')

@section('styles')

@endsection

@section('content')
<div class="kb-mb-px">
    <x-slider/>
</div>

@include('member.partials.search')

<!-- Content
									============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="">
                <div class="mb-2">
                    <h2 class="kb-text-2xl md:kb-text-3xl">
                        Search Journals By Categories
                    </h2>
                </div>
                <div class="kb-grid kb-grid-cols-2 md:kb-grid-cols-4">
                    @foreach ($categories as $item)
                    <div class="p-2">
                        <a class="card card-svg d-grid align-content-center justify-content-center hvr-glow rounded-3 {{ Str::snake($item->category_name) == request('category') ? ' bg-dark text-light shadow' : ' bg-light text-dark' }}"
                            href="{{ route('member.search') }}?category={{ Str::snake($item->category_name) }}"
                            style="height: 100px">
                            <div class="kb-text-xl md:kb-text-2xl">
                                {{ $item->category_name ?? '' }}
                            </div>
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>



    <div class="bg-light py-4">
        <div class="container">
            <div class="kb-flex kb-flex-wrap gap-4 justify-content-around">
                <div class="">
                    <div
                        class="d-flex justify-content-center align-items-center gap-2">
                        <i class='fa fa-user kb-text-2xl'></i>
                        <div class="fs-5">INDIVIDUAL</div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="d-flex justify-content-center align-items-center gap-2">
                        <div>
                            <i class='fa fa-university kb-text-2xl'></i>
                        </div>
                        <div class="fs-5">INSTITUTIONAL</div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="d-flex justify-content-center align-items-center gap-2">
                        <div>
                            <i class="fa fa-users kb-text-2xl"></i>
                        </div>
                        <div class="fs-5">SOCIETIES</div>
                    </div>
                </div>
            </div>
        </div>

    </div>



    <a href="{{ route('member.contact') }}"
        class="button button-3d border-bottom-0 button-full text-center text-end mb-6 fw-light font-primary p-3 bg-dark"
        href="javascript:void(0)" style="font-size: 26px;">
        <div class="container kb-text-2xl md:kb-text-4xl">
            Would you like to publish your journal with us? <strong>Enquire
                Here</strong> <i class="uil uil-angle-right-b"
                style="top:3px;"></i>
        </div>
    </a>
    @if ($articles->isNotEmpty())
    <div class="mx-auto mt-6 container">

        <div class="row">
            <div class="w-full mx-auto text-center">
                <div class="fs-2">Recent Articles</div>
                <div class="bg-dark rounded px-2 py-1 mx-auto"
                    style="width:50px;"></div>
            </div>


            <x-article-card :articles="$articles" :class="'col-md-6 my-3'" />
        </div>
        @if($articles->count() >= 8)
        <div class="d-flex justify-content-center my-5">
            {{ $articles->links() }}
        </div>
        @endif


    </div>
    @endif
</section><!-- #content end -->


@endsection



