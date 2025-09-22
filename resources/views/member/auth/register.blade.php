@extends('layouts.member')

@section('page-name', 'Register')


@section('content')


<div class="content-wrap py-0">

    <div class="section bg-dark p-0 m-0 h-100 position-absolute"></div>

    <div class="section bg-svg min-vh-100 p-0 m-0 d-flex">
        <div class="vertical-middle">
            <div class="container py-4 px-3">

                <div class="card mx-auto rounded-4 border-0 shadow-lg m-container-md">
                    <div class="card-body p-3 px-lg-5">
                        <form name="login-form" class="mb-0" action="{{ route('member.submit-register') }}"
                            method="post">
                            @csrf
                            <h1 class="fs-4 fw-semibold text-center mb-0">Create Account</h1>


                            <div class="row">
                                <div class="mb-4">
                                    <label for="title" class="form-label">Title</label>
                                    <select class="form-select select2 @error('title') {{ 'isInvalid' }} @enderror" name="title" id="title">
                                        <option selected disabled>Choose Title</option>
                                        @foreach ($title as $key => $label)

                                        <option value="{{$key}}" {{ old('title', '' )==$key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <span class="text-error">@error('title') {{ $message }} @enderror</span>

                                </div>
                                <div class="mb-4">
                                    <label for="first_name" class="required">First Name</label>
                                    <input type="text" id="first_name" name="first_name"
                                        value="{{ old('first_name', '') }}"
                                        class="form-control not-dark @error('first_name') {{ 'isInvalid' }} @enderror">
                                    <span class="text-error">@error('first_name') {{ $message }} @enderror</span>

                                </div>

                                <div class="mb-4">
                                    <label for="last_name" class="required">Last Name</label>
                                    <input type="text" id="last_name" name="last_name"
                                        value="{{ old('last_name', '') }}"
                                        class="form-control not-dark @error('last_name') {{ 'isInvalid' }} @enderror">
                                    <span class="text-error">@error('last_name') {{ $message }} @enderror</span>

                                </div>

                                <div class="mb-4">
                                    <label for="middle_name">Middle Name</label>
                                    <input type="text" id="middle_name" name="middle_name"
                                        value="{{ old('middle_name', '') }}"
                                        class="form-control not-dark @error('middle_name') {{ 'isInvalid' }} @enderror">
                                    <span class="text-error">@error('middle_name') {{ $message }} @enderror</span>

                                </div>

                                <div class=" mb-4">
                                    <label for="email_address" class="required">Email</label>
                                    <input type="text" id="email_address" name="email_address"
                                        value="{{ old('email_address', '') }}"
                                        class="form-control not-dark @error('email_address') {{ 'isInvalid' }} @enderror">
                                    <span class="text-error">@error('email_address') {{ $message }} @enderror</span>

                                </div>

                                <div class="mb-4">
                                    <div>
                                        <label class="required">Account Type</label>
                                    </div>
                                    @foreach ($member_types as $id => $name)
                                        <div class="form-check form-check-inline">
                                            <label class="btn border border-2 border-primary text-primary" for="member_type_id{{ $id }}"> {{ $name }} <i class="fa fa-check-square text-primary" aria-hidden="true" style="display:none"></i>
                                                <input class="form-check-input checkRadio" hidden type="radio" name="member_type_id" id="member_type_id{{ $id }}" value="{{ $id }}" >
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class=" mb-4">
                                    <label for="phone_number" class="required">
                                        Phone Number
                                    </label>
                                    <input type="text" id="phone_number" name="phone_number"
                                        value="{{ old('phone_number', '') }}"
                                        class="form-control not-dark @error('phone_number') {{ 'isInvalid' }} @enderror">
                                    <span class="text-error">@error('phone_number') {{ $message }} @enderror</span>

                                </div>

                                <div class="form-group mb-4">
                                    <label for="country_id" class="w-100">Country <span class="text-danger">*</span>
                                        <select name="country_id" id="country_id"
                                            class="form-control select2 @error('country_id') {{ 'isInvalid' }} @enderror">
                                            <option value="" {{ old('country_id') ? 'selected' : '' }}>Select Country
                                            </option>
                                            @foreach ($countries as $label)
                                            <option value="{{$label->id }}">{{ $label->name }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <span class="text-error">@error('country_id') {{ $message }} @enderror</span>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="state_id" class="w-100">State <span class="text-danger">*</span>
                                        <select name="state_id" id="state_id"
                                            class="form-control select2 @error('state_id') {{ 'isInvalid' }} @enderror">
                                            <option value="">Select State</option>

                                        </select>
                                    </label>
                                    <span class="text-error">@error('state_id') {{ $message }} @enderror</span>
                                </div>

                                <div class="mb-3">
                                    <label for="member_role_id" class="form-label required">Role</label>
                                    <select class="form-select" name="member_role_id" id="member_role_id">
                                        <option value="" selected disabled>Select Role</option>
                                        @foreach ($member_roles as $key => $value)
                                        <option value="{{ $key }}" {{ old('role')==$key ? 'selected' : '' }}>{{ $value
                                            }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class=" mb-4">
                                    <div class="d-flex justify-content-between">
                                        <label for="password" class="required">Password</label>
                                    </div>
                                    <input type="password" id="password" name="password"
                                        value="{{ old('password', '') }}"
                                        class="form-control not-dark @error('password') {{ 'isInvalid' }} @enderror">
                                    <span class="text-error">@error('password') {{ $message }} @enderror</span>
                                </div>

                                <div class=" mb-4">
                                    <div class="d-flex justify-content-between">
                                        <label for="password_confirmation" class="required">Confirm Password</label>
                                    </div>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        value="{{ old('password_confirmation', '') }}" class="form-control not-dark">
                                </div>
                            </div>
                            <x-submit-button label="Register" class="bg-dark" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    $(function () {
        $('#country_id').change(function (e) {
            e.preventDefault();
            let sid = $(this).children('option:selected').val();

            $.ajax({
                type: "get",
                url: "/get-state/"+sid,
                dataType: "json",
                success: function (response) {
                    $('#state_id').html(response);
                }
            });
        });

        $('.checkRadio').change(function (e) {
            e.preventDefault();
            let thisRadio = $(this);

            $('.checkRadio').siblings('i').hide(0200);


            if(thisRadio.is(':checked')){
                thisRadio.siblings('i').show(0200);
            }

        });

    });
</script>
@endsection
