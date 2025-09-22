@extends('layouts.member')

@section('page-name', 'Login')


@section('content')


<div class="content-wrap py-0">

    <div class="section bg-dark p-0 m-0 h-100 position-absolute"></div>

    <div class="section bg-svg min-vh-100 p-0 m-0 d-flex">
        <div class="vertical-middle">
            <div class="container py-4">

                <div class="card mx-auto rounded-4 border-0 shadow-lg" style="max-width: 500px;">
                    <div class="card-body p-5">
                        <form name="login-form" class="mb-0" action="{{ route('member.submit-login') }}" method="post">
                            @csrf
                            <h1 class="fs-4 fw-semibold text-center mb-0">Sign In to Your Account</h1>
                            <h2 class="fs-5 text-center fw-medium mb-5 mt-1"><span class="op-06 nocolor">New?</span> <a
                                    href="{{ route('member.register') }}">Create Account</a></h2>

                            <div class="row">
                                <div class="col-12 form-group mb-4">
                                    <label for="email_address">Email</label>
                                    <input type="email" id="email_address" name="email_address" value=""
                                        class="form-control not-dark">
                                </div>

                                <div class="col-12 form-group mb-4">
                                    <div class="d-flex justify-content-between">
                                        <label for="password">Password</label>
                                        <a href="{{ route('member.forget-password') }}" class="fw-semibold text-smaller">Forgot
                                            Password?</a>
                                    </div>
                                    <input type="password" id="password" name="password" value=""
                                        class="form-control not-dark">
                                </div>
                            </div>
                            <x-submit-button label="Login" class="bg-dark w-100 mx-auto" />
                        </form>


                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection
