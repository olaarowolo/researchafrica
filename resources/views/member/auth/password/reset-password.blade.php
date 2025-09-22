@extends('layouts.member')

@section('page-name', 'Reset Password')


@section('content')


<div class="content-wrap py-0">

    <div class="section bg-dark p-0 m-0 h-100 position-absolute"></div>

    <div class="section bg-transparent min-vh-100 p-0 m-0 d-flex">
        <div class="vertical-middle">
            <div class="container py-4">

                <div class="card mx-auto rounded-4 border-0 shadow-lg" style="max-width: 500px;">
                    <div class="card-body p-5">
                        <form id="login-form" name="login-form" class="mb-0" action="{{ route('member.reset-password-submit', $hash) }}" method="post">
                            @csrf
                            <h1 class="fs-4 fw-semibold text-center mb-3">
                                {{ __('Reset Password') }}
                            </h1>


                            <div class="row">
                                <div class="col-12 form-group mb-4">
                                    <label for="password">New Password</label>
                                    <input type="password" id="password"
                                    name="password" value="{{ old('password') }}" class="form-control not-dark @error('password') isInvalid @enderror">
                                    @if ($errors->has('password'))
                                        <small class="text-error">
                                            {{ $errors->first('password') }}
                                        </small>
                                    @endif

                                </div>
                                <div class="col-12 form-group mb-4">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" class="form-control not-dark">
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
