<!doctype html>

@php
$setting = \App\Models\Setting::where('status', 1)->first();
@endphp
<html lang="en">

<head>
    <title>@yield('page-name') - {{ $setting ? $setting->website_name : trans('panel.site_title') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    {{-- <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> --}}
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/lib/style1.css">
    <link rel="stylesheet" href="/lib/profile.css">

    <!-- Font Icons -->
    <link rel="stylesheet" href="/lib/font-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css"/>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />


    <style type="text/css">
        label.required::after {
            content: " *";
            color: #fd1010;
        }

        .links li {
            list-style-type: none;
        }


        @media screen and (max-width: 767px) {

            /* .icon-header {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
  } */
            .links {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
            }

            .item {
                flex-basis: 48%;
                margin-bottom: 1rem;
            }

            h5 {
                font-size: 13px;
            }

        }
    </style>
    @yield('styles')
    @livewireStyles

<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</head>

<body>

    <div class="wrapper d-flex align-items-stretch">

        @include('member.partials.sidebar', ['setting' => $setting])

        <!-- Page Content  -->
        @yield('content')
    </div>

    {{-- <script src="/lib/jquery.min.js"></script> --}}
    {{-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/lib/popper.js"></script>
    <script src="/lib/bootstrap.min.js"></script>
    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
    <script src="/lib/main.js"></script>


    <!-- text editor -->
    <script src="https://cdn.tiny.cloud/1/rxej7x2melg6ls3yt21us48h5bu5bv4g7e329hevysyzyu62/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script>
        tinymce.init({
            selector: '.textarea'
        });
        $('.datepicker').datepicker({
            autoHide: true,
            format: 'yyyy-mm-dd'
        });


        $('.select-all').click(function () {
            let $select2 = $(this).parent().siblings('.select2')
            $select2.find('option').prop('selected', 'selected')
            $select2.trigger('change')
        })
        $('.deselect-all').click(function () {
            let $select2 = $(this).parent().siblings('.select2')
            $select2.find('option').prop('selected', '')
            $select2.trigger('change')
        })

        $('.select2').select2()
    </script>

    <<x-alert />


    @yield('scripts')


    @livewireScripts
    @stack('component')
    @stack('js')

</body>

</html>
