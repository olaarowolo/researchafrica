@extends('layouts.member')

@section('page-name', 'Search')

@section('content')

{{-- @include('member.partials.slider') --}}

<!-- Content
             ============================================= -->
<div class="">
@include('member.partials.search')
</div>


<div class="container">
    <div class="py-5">
        @if(request()->has('category') && request('category'))

        <div class="mb-2">
            <h2 class="kb-text-2xl md:kb-text-3xl">
                Search Journals By Categories
            </h2>
        </div>
        <div class="kb-grid kb-grid-cols-2 md:kb-grid-cols-4">
            @foreach ($categories as $item)
            <div class="p-2">
                <a class="card d-grid align-content-center justify-content-center hvr-glow rounded-3 {{ Str::snake($item->category_name) == request('category') ? ' bg-dark text-light shadow' : 'text-dark card-svg' }}"
                    href="{{ route('member.search') }}?category={{ Str::snake($item->category_name) }}"
                    style="height: 100px">
                    <div class="kb-text-xl md:kb-text-2xl">
                        {{ $item->category_name ?? '' }}
                    </div>
                </a>
            </div>
            @endforeach

        </div>
        @endif
    </div>


    <div class="container">

        @if (request()->has('category') && request('category'))

        <x-journal :journals="$journals" :count="$count" :categories="$categories" />
        @else


        @if(request('type') === 'journal')
        <x-journal :journals="$journals" :count="$count" :categories="$categories" />
        @else
        <x-article :randomArticle="$randomArticle" :articles="$articles" :count="$count" :categories="$categories" />
        @endif

        @endif
    </div>
</div>


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
