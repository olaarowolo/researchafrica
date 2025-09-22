@extends('layouts.member')

@section('page-name', 'Forget Password')


@section('content')


<div class="content-wrap py-0">

    <div class="section bg-dark p-0 m-0 h-100 position-absolute"></div>

    <div class="section bg-transparent min-vh-100 p-0 m-0 d-flex">
        <div class="vertical-middle">
            <div class="container py-4">

                <div class="card mx-auto rounded-4 border-0 shadow-lg" style="max-width: 500px;">
                    <div class="card-body p-5">
                        <form id="login-form" name="login-form" class="mb-0" action="{{ route('member.email-password') }}" method="post">
                            @csrf
                            <h1 class="fs-4 fw-semibold text-center mb-0">Enter Your Email Address</h1>


                            <div class="row">
                                <div class="col-12 form-group mb-4">
                                    <label for="email_address">Email</label>
                                    <input type="email" id="email_address" name="email_address" value="{{ old('email_address') }}" class="form-control not-dark">
                                </div>
                            </div>

                            <x-submit-button label="Submit" class="bg-dark w-100 mx-auto" />
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
