@extends('layouts.profile')


@section('page-name', 'Edit Profile')

@section('content')

<div id="content" class="p-4 p-md-5">
    <x-profile-bar />

    <div class="card shadow">
        <div class="card-header">
            <h2 class="mb-4">Edit Profile</h2>
        </div>

        <div class="card-header px-4">
            <div class="row mb-5">
                {{-- <div class="col-md-6 px-4">
                    @if ($errors->any())
                    @foreach ($errors->all() as $error)
                    <div class="bg-danger p-2 fs-lg text-light my-2 mx-2 rounded rounded-lg">
                        {{ $error }}
                    </div>
                    @endforeach
                    @endif

                    <form class="form-widget p-3 border shadow rounded-2" method="post"
                        action="{{ route('member.password.changePassword') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Old password</label>
                            <input type="password"
                                class="{{ $errors->has('old_password') ? 'isInvalid' : '' }} form-control"
                                name="old_password" id="old_password" aria-describedby="helpId"
                                placeholder="Old password">
                            <span class="text-danger">@error('old_password') {{ $message }} @enderror</span>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New password</label>
                            <input type="password"
                                class="{{ $errors->has('password') ? 'isInvalid' : '' }} form-control" name="password"
                                id="password" aria-describedby="helpId" placeholder="New Password">
                            <span class="text-danger">@error('password') {{ $message }} @enderror</span>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password_confirmation" class="form-control" name="password_confirmation"
                                id="password_confirmation" aria-describedby="helpId" placeholder="Confirm Password">
                        </div>

                        <x-submit-button label="Update Password" />
                    </form>
                </div> --}}
                <div class="col-md-6">

                    {{-- <form action="{{ route('member.profile_picture') }}"
                        class="form-widget p-3 border shadow rounded-2 text-center" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex justify-content-center">
                            <div class="mb-3">


                                <label for="profile_picture" class="form-label">
                                    <div class="mb-3 gallery" style="cursor: pointer">
                                        <div style="width: 220px;height:220px;" class="">
                                            <img src="{{ auth('member')->user()->profile_picture ? auth('member')->user()->profile_picture->getUrl() : '/lib/avata.png'}}"
                                                alt="" style="width: 220px;height:220px;"
                                                class="rounded-3 shadow rounded-circle">
                                        </div>
                                    </div>

                                    <input type="file" class="form-control" hidden name="profile_picture"
                                        id="profile_picture" placeholder="select File" aria-describedby="fileHelpId">
                                </label>
                            </div>
                        </div>

                        <x-submit-button label="Change Profile Picture" />
                    </form> --}}
                </div>
            </div>
            <main class="postcontent col-lg-12 mb-5">

                <form action="{{ route('member.profile_picture') }}"
                class="form-widget p-3 border shadow rounded-2 text-center" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="d-flex justify-content-center">
                    <div class="mb-3">


                        <label for="profile_picture" class="form-label">
                            <div class="mb-3 gallery" style="cursor: pointer">
                                <div style="width: 220px;height:220px;" class="">
                                    <img src="{{ auth('member')->user()->profile_picture ? auth('member')->user()->profile_picture->getUrl() : '/lib/avata.png'}}"
                                        alt="" style="width: 220px;height:220px;"
                                        class="rounded-3 shadow rounded-circle">
                                </div>
                            </div>

                            <input type="file" class="form-control" hidden name="profile_picture"
                                id="profile_picture" placeholder="select File" aria-describedby="fileHelpId">
                        </label>
                    </div>
                </div>

                <x-submit-button label="Upload Profile Picture" />
            </form>

                <div class="form-widget p-3 border shadow">

                    <form class="mb-0" id="template-contactform" name="template-contactform"
                        action="{{ route('member.profile.update') }}" method="post">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="first_name" class="required">First Name <small>*</small></label>
                                <input type="text" id="first_name" name="first_name"
                                    value="{{ old('first_name', $user->first_name) }}" class="form-control">
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="middle_name" class="required">Middle Name</label>
                                <input type="text" id="middle_name" name="middle_name"
                                    value="{{ old('middle_name', $user->middle_name) }}" class="form-control">
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="last_name" class="required">Last Name <small>*</small></label>
                                <input type="text" id="last_name" name="last_name"
                                    value="{{ old('last_name', $user->last_name) }}" class="form-control">
                            </div>


                            <div class="col-md-7 form-group">
                                <label for="email_address">Email Address</label>
                                <input disabled readonly type="email" id="email_address" name="email_address"
                                    value="{{ old('email_address', $user->email_address) }}" class="email form-control"
                                    style="cursor: not-allowed">
                            </div>

                            <div class="col-md-5 form-group">
                                <label for="phone_number">Phone</label>
                                <input type="text" id="phone_number" name="phone_number"
                                    value="{{ old('phone_number', $user->phone_number) }}" class="form-control">
                            </div>

                            <div class="col-md-5 form-group">
                                <label for="gender">Gender</label>
                                <select class="form-control" name="gender">
                                    <option value="" {{ old('gender', $user->gender ?? null) == null ?
                                        'selected' : '' }}>Select Gender</option>

                                    @foreach (\App\Models\Member::GENDER_RADIO as $key => $value)
                                    <option value="{{ $key }}" {{ old('gender', $user->gender ?? '') == $key ?
                                        'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7 form-group">
                                <label for="date_of_birth">Date Of Birth</label>
                                <input type="text" readonly id="date_of_birth" name="date_of_birth"
                                    value="{{ $user->date_of_birth }}" class="form-control datepicker">
                            </div>

                            <div class="w-100"></div>

                            <div class="col-md-6 form-group">
                                <label for="country_id" class="required">Country</label>
                                <select class="form-control" name='country_id' id='country_id'>
                                    <option value="" {{ old('country_id', null)==$user->country_id ? 'selected' : ''
                                        }}>Select Country</option>
                                    @foreach ($countries as $label)
                                    <option value="{{$label->id }}" {{ old('country_id', $user->country_id) ==
                                        $label->id ? 'selected' : '' }}>{{ $label->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="state_id" class="required">State</label>
                                <select class="form-control" name='state_id' id='state_id'>
                                    <option value="" {{ old('state_id', null)==$user->state_id ? 'selected' : ''
                                        }}>Select State</option>
                                    @foreach ($states as $label)
                                    <option value="{{$label->id }}" {{ old('state_id', $user->state_id) == $label->id ?
                                        'selected' : '' }}>{{ $label->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <x-submit-button class="bg-dark" label='Update Profile' />

                    </form>
                    
                </div>

            </main>

            <div class="col-md-6 px-4">
                @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="bg-danger p-2 fs-lg text-light my-2 mx-2 rounded rounded-lg">
                    {{ $error }}
                </div>
                @endforeach
                @endif

                <form class="form-widget p-3 border shadow rounded-2" method="post"
                    action="{{ route('member.password.changePassword') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Old password</label>
                        <input type="password"
                            class="{{ $errors->has('old_password') ? 'isInvalid' : '' }} form-control"
                            name="old_password" id="old_password" aria-describedby="helpId"
                            placeholder="Old password">
                        <span class="text-danger">@error('old_password') {{ $message }} @enderror</span>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New password</label>
                        <input type="password"
                            class="{{ $errors->has('password') ? 'isInvalid' : '' }} form-control" name="password"
                            id="password" aria-describedby="helpId" placeholder="New Password">
                        <span class="text-danger">@error('password') {{ $message }} @enderror</span>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password_confirmation" class="form-control" name="password_confirmation"
                            id="password_confirmation" aria-describedby="helpId" placeholder="Confirm Password">
                    </div>

                    <x-submit-button label="Update Password" />
                </form>
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


        $('#profile_picture').change(function() {
            let thisInput = $(this);
            let gallery = thisInput.siblings('div.gallery');
            gallery.children().hide();
            $('#text').removeClass('d-none').addClass('d-block');
            imagesPreview(this, gallery);
        });


        var imagesPreview = function(input, placeToInsertImagePreview) {
            if (input.files) {
                var filesAmount = input.files.length;
                // console.log(filesAmount);
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $($.parseHTML('<div class="border shadow rounded-3 preview " style="height: 220px;width:220px;background-size: 100% 100%;background-position:cover;border-radius: 50%;">')).css('background-image', 'url("'+event.target.result+'")').appendTo(placeToInsertImagePreview);
                    }
                    reader.readAsDataURL(input.files[i]);
                }
            }
        };
    });
</script>
@endsection