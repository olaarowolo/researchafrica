@extends('layouts.member')

@section('page-name', 'FAQ')

@section('content')


<!-- Page Title
              ============================================= -->
<section class="page-title bg-dark text-white">
    <div class="container">
        <div class="page-title-row">

            <div class="page-title-content">
                <h1 class="text-white">FAQs</h1>
            </div>

        </div>
    </div>
</section><!-- .page-title end -->


<!-- Content
              ============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="divider"><i class="bi-circle-fill"></i></div>
            <div
                class="kb-w-full kb-grid kb-grid-cols-1 md:kb-grid-cols-2 kb-gap-3">
                @foreach ($faqCategories as $faqCategory)
                <div class="card">

                    <div class="card-header">
                        <h3 class="kb-text-xl kb-font-bold">{{
                            $faqCategory->category }}</h3>
                    </div>

                    <div class="card-body">

                        @foreach ($faqCategory->faqs as $faq)

                        <div class="accordion accordion-flush"
                            id="accordionFlushExample{{ $faq->id }}">
                            <div class="accordion-item">
                                <h2 class="accordion-header"
                                    id="flush-headingOne{{ $faq->id }}">
                                    <button class="accordion-button collapsed"
                                        type="button" data-mdb-toggle="collapse"
                                        data-mdb-target="#flush-collapseOne{{ $faq->id }}"
                                        aria-expanded="false"
                                        aria-controls="flush-collapseOne{{ $faq->id }}">
                                        {{ $faq->question ?? '' }}
                                    </button>
                                </h2>
                                <div id="flush-collapseOne{{ $faq->id }}"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="flush-headingOne{{ $faq->id }}"
                                    data-mdb-parent="#accordionFlushExample">
                                    <div class="accordion-body">
                                        {{ $faq->answer ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>


                </div>

                @endforeach
            </div>

        </div>
    </div>
</section><!-- #content end -->


@endsection



@section('scripts')

@endsection
