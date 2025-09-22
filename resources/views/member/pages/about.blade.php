@extends('layouts.member')

@section('page-name', 'About Us')

@section('content')


@php
$about = \App\Models\About::first();
@endphp


<!-- Page Title
============================================= -->
<section class="page-title bg-dark text-white">
    <div class="container">
        <div class="page-title-row">
            <div class="page-title-content">
                <h1 class="text-white">ABOUT US</h1>
            </div>
        </div>
    </div>
</section><!-- .page-title end -->

<section>
    <div class="container">
        <div class="row">
            <div class="col mt-5 mb-5">
                <div class="card shadow bg-light mb-3">
                   <div class="card-header">
                      <div class="text-primary ms-4 fs-2">About Us</div>
                   </div>
                   <div class="card-body kb-prose kb-max-w-none">
                       {!! $about->description !!}
                   </div>
                </div>
                <div class="card shadow bg-light mb-3">
                   <div class="card-header">
                      <div class="text-primary ms-4 fs-2">Our Mission</div>
                   </div>
                   <div class="card-body">
                        <div class="kb-prose kb-max-w-none">
                        {!! $about->mission ?? '' !!}
                        </div>
                   </div>
                </div>
                <div class="card shadow bg-light mb-3">
                   <div class="card-header">
                      <div class="text-primary ms-4 fs-2">Our Vision</div>
                   </div>
                   <div class="card-body kb-prose kb-max-w-none">
                       {!! $about->vision ?? '' !!}
                   </div>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection



@section('scripts')

@endsection
