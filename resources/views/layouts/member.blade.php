<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en-US">
<!--<![endif]-->


@php
    $setting = \App\Models\Setting::where('status', 1)->first();
@endphp
{{-- TODO: Seo head --}}

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Font Imports -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap"
        rel="stylesheet">

    <link rel="pingback" href="https: //domainname.com/xmlrpc.php" />
    <title>@yield('page-name') - {{ $setting ? $setting->website_name : trans('panel.site_title') }}</title>
    <!-- style and script resources -->
    <link rel="stylesheet" href="" media="all">


    <!--meta properties -->
    {{-- <meta name="description" content="{!! $article->last->abstract ?? '' !!}" /> --}}

    <!--detailed robots meta https://developers.google.com/search/reference/robots_meta_tag -->
    <meta name="robots" content="index, follow, max-snippet: -1, max-image-preview:large, max-video-preview: -1" />
    <link rel="canonical" href="{{ route('home') }}" />

    <!--open graph meta tags for social sites and search engines-->
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $setting ? $setting->website_name : trans('panel.site_title') }}" />
    {{-- <meta property="og:description" content="{!! $setting ? $setting->description : '' !!}" /> --}}
    <meta property="og:url" content="{{ route('home') }}" />
    <meta property="og:site_name" content="{{ $setting ? $setting->website_name : trans('panel.site_title') }}" />
    <meta property="og:image" content="{{ $setting && $setting->logo ? $setting->logo->getUrl() : '' }}" />
    <meta property="og:image:secure_url" content="{{ $setting && $setting->logo ? $setting->logo->getUrl() : '' }}" />
    <meta property="og:image:width" content="600" />
    <meta property="og:image:height" content="660" />
    <!--twitter description-->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:description" content="{!! $setting ? $setting->description : '' !!}" />
    <meta name="twitter:title" content="{{ $setting ? $setting->website_name : trans('panel.site_title') }}" />
    <meta name="twitter:site" content="{{ route('home') }}" />
    <meta name="twitter:image" content="{{ $setting && $setting->logo ? $setting->logo->getUrl() : '' }}" />
    <meta name="twitter:creator" content="@research_africa" />
    <!--opengraph tags for location or address for information panel in google-->
    <meta name="og:latitude" content="" />
    <meta name="og:longitude" content="" />
    <meta name="og:street-address" content="" />
    <meta name="og:locality" content="Ojo" />
    <meta name="og:region" content="Lagos" />
    <meta name="og:postal-code" content="102101" />
    <meta name="og:country-name" content="Nigeria" />
    <!--search engine verification-->
    <meta name="google-site-verification" content="" />
    <meta name="yandex-verification" content="" />
    <!--powered by meta-->
    <meta name="generator" content="" />
    <!-- Site fevicon icons -->
    <link rel="icon" href="{{ $setting && $setting->favicon ? $setting->favicon->getUrl() : '' }}"
        sizes="32x32" />
    <link rel="icon" href="{{ $setting && $setting->favicon ? $setting->favicon->getUrl() : '' }}"
        sizes="192x192" />
    <link rel="apple-touch-icon-precomposed"
        href="{{ $setting && $setting->favicon ? $setting->favicon->getUrl() : '' }}" />
    <meta name="msapplication-TileImage"
        content="{{ $setting && $setting->favicon ? $setting->favicon->getUrl() : '' }}" />
    <!--complete list of meta tags at - https://gist.github.com/lancejpollard/1978404 -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hover.css/2.1.1/css/hover-min.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link href="/lib/style.css" rel="stylesheet" />
    <link href="/lib/font-icons.css" rel="stylesheet" />
    <link href="/lib/construction.css" rel="stylesheet" />
    <link href="/lib/custom.css" rel="stylesheet" />

    @yield("meta")


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700" rel="stylesheet">
    <style>
        h1,
        h2,
        body {
            font-family: 'Poppins', sans-serif;
        }

        h3 {
            font-family: 'Poppins', sans-serif;
            text-indent: 10px;
        }
    </style>

</head>

@yield('styles')


<body class="stretched" style="height: 100vh">
    <div id="wrapper">
        @include('member.partials.header', ['setting' => $setting])
        @if ($errors->any())
            <div class="pt-md-5 pt-2 bg-dark">
                <div class="w-75 mx-auto">
                    @foreach ($errors->all() as $error)
                        <div class="px-3 mt-3 py-1 font-bold bg-danger text-light rounded">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @yield('content')
        @if (!request()->is('login') && !request()->is('register') && !request()->is('email-verify'))
            @include('member.partials.footer', ['setting' => $setting])
        @endif


    </div>

    {{-- Scripts --}}


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"></script>

    {{-- <script
        src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js">
    </script> --}}

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>


    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>

    <script>
        $(function() {
            $('.datepicker').datepicker({
                autoHide: true,
                format: 'yyyy-mm-dd'
            });

            @auth('member')


                $('.bookmarked').click(function(e) {
                    e.preventDefault();
                    let thisBookmark = $(this);
                    let article = thisBookmark.attr('data-article');

                    if (thisBookmark.hasClass('bx-bookmark')) {
                        thisBookmark.removeClass('bx-bookmark').addClass('bxs-bookmark');
                        Swal.fire({
                            icon: 'success',
                            title: 'Bookmarked',
                            //   text: 'text',
                            toast: true,
                            position: 'top-right',
                            showConfirmButton: false,
                        })
                    } else {
                        thisBookmark.removeClass('bxs-bookmark').addClass('bx-bookmark');
                        Swal.fire({
                            icon: 'info',
                            title: 'Bookmark removed',
                            //   text: 'text',
                            toast: true,
                            position: 'top-right',
                            showConfirmButton: false,
                        })
                    }

                    $.ajax({
                        type: "get",
                        url: "/bookmark/" + article,
                        success: function(response) {}
                    });

                });
            @endauth

        });
    </script>


    @yield('scripts')




    <x-alert />
    @stack('component')
</body>

</html>
