@extends('layouts.member')

@section('page-name', 'Journal')

@section('content')

    {{-- @include('member.partials.slider') --}}

    <!-- Content
                 ============================================= -->
    <div class="bg-dark p-3">

        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="">
                        <P class="text-white fs-1">{{ $journal->category_name ?? '' }} </P>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <section id="content">
        <div class="">

            <div class="container">
                <div class="row mb-3">

                    <div class="col-lg-9 p-1 pb-md-3 ">
                        <div class="my-3 kb-border rounded kb-p-3 kb-bg-gray-300/50">
                            <div class="kb-prose kb-max-w-none w-100 mx-auto">
                                @if (request('type') == 'description')
                                    {!! $journal->description ?? '' !!}
                                @endif
                                @if (request('type') == 'aim_scope')
                                    {!! $journal->aim_scope ?? '' !!}
                                @endif
                                @if (request('type') == 'editorial_board')
                                    {!! $journal->editorial_board ?? '' !!}
                                @endif
                                @if (request('type') == 'submission')
                                    {!! $journal->submission ?? '' !!}
                                @endif
                                @if (request('type') == 'subscribe')
                                    {!! $journal->subscribe ?? '' !!}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 p-2 mt-2">
                        <div class="card">
                            <div class="card-header">
                                Journal Infomation
                            </div>
                            <div class="card-body">
                                <ul class="">
                                    <li>
                                        <a href="{{ route('member.journal', $journal->id) }}?type=description"
                                            class="d-block kb-py-3 {{ request('type') == 'description' ? 'kb-font-bold' : '' }}">
                                            Journal Description
                                        </a>
                                    </li>
                                    <hr />
                                    <li>
                                        <a href="{{ route('member.journal', $journal->id) }}?type=aim_scope"
                                            class="d-block kb-py-3 {{ request('type') == 'aim_scope' ? 'kb-font-bold' : '' }}">
                                            Journal Aim and Scope
                                        </a>
                                    </li>
                                    <hr />
                                    <li>
                                        <a href="{{ route('member.journal', $journal->id) }}?type=editorial_board"
                                            class="d-block kb-py-3 {{ request('type') == 'editorial_board' ? 'kb-font-bold' : '' }}">
                                            Journal Editorial Board
                                        </a>
                                    </li>
                                    <hr />
                                    <li>
                                        <a href="{{ route('member.journal', $journal->id) }}?type=submission"
                                            class="d-block kb-py-3 {{ request('type') == 'submission' ? 'kb-font-bold' : '' }}">
                                            Journal Submission Guidelines
                                        </a>
                                    </li>
                                    <hr />
                                    <li>
                                        <a href="{{ route('member.journal', $journal->id) }}?type=subscribe"
                                            class="d-block kb-py-3 {{ request('type') == 'subscribe' ? 'kb-font-bold' : '' }}">
                                            Journal Subscription Guidelines
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
