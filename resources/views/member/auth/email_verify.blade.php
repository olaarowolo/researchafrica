@extends('layouts.member')

@section('page-name', 'Email Verification')


@section('content')


<div class="content-wrap py-0">

    <div class="section bg-dark p-0 m-0 h-100 position-absolute"></div>

    <div class="section bg-transparent min-vh-100 p-0 m-0 d-flex">
        <div class="vertical-middle">
            <div class="container">

                <div class="card mx-auto rounded-4 border-0 shadow-lg" style="max-width: 500px;">
                    <div class="card-body p-5">
                        <form id="login-form" name="login-form" class="mb-0" action="{{ route('member.verify_email') }}"
                            method="post">
                            @csrf
                            <h1 class="fs-4 fw-semibold text-center mb-0">Email Verification</h1>

                            <div class="row">
                                <div class="col-12 form-group mb-4">
                                    <label for="token">Verification Code</label>
                                    <input type="text" id="token" name="token" value="" class="form-control not-dark">
                                </div>


                            </div>
                            <x-submit-button />
                        </form>


                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection
