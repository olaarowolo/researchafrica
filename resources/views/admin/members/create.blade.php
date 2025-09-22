@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.member.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.members.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>{{ trans('cruds.member.fields.title') }}</label>
                <select class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" name="title" id="title">
                    <option value disabled {{ old('title', null)===null ? 'selected' : '' }}>{{
                        trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Member::TITLE_SELECT as $key => $label)
                    <option value="{{ $key }}" {{ old('title', '' )===(string) $key ? 'selected' : '' }}>{{ $label }}
                    </option>
                    @endforeach
                </select>
                @if($errors->has('title'))
                <div class="invalid-feedback">
                    {{ $errors->first('title') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.title_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="first_name">{{ trans('cruds.member.fields.first_name') }}</label>
                <input class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" type="text"
                    name="first_name" id="first_name" value="{{ old('first_name', '') }}" required>
                @if($errors->has('first_name'))
                <div class="invalid-feedback">
                    {{ $errors->first('first_name') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.first_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="middle_name">{{ trans('cruds.member.fields.middle_name') }}</label>
                <input class="form-control {{ $errors->has('middle_name') ? 'is-invalid' : '' }}" type="text"
                    name="middle_name" id="middle_name" value="{{ old('middle_name', '') }}">
                @if($errors->has('middle_name'))
                <div class="invalid-feedback">
                    {{ $errors->first('middle_name') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.middle_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="last_name">{{ trans('cruds.member.fields.last_name') }}</label>
                <input class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" type="text"
                    name="last_name" id="last_name" value="{{ old('last_name', '') }}" required>
                @if($errors->has('last_name'))
                <div class="invalid-feedback">
                    {{ $errors->first('last_name') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.last_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="email_address">{{ trans('cruds.member.fields.email_address') }}</label>
                <input class="form-control {{ $errors->has('email_address') ? 'is-invalid' : '' }}" type="email"
                    name="email_address" id="email_address" value="{{ old('email_address') }}" required>
                @if($errors->has('email_address'))
                <div class="invalid-feedback">
                    {{ $errors->first('email_address') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.email_address_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="date_of_birth">{{ trans('cruds.member.fields.date_of_birth') }}</label>
                <input class="form-control date {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}" type="text"
                    name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}">
                @if($errors->has('date_of_birth'))
                <div class="invalid-feedback">
                    {{ $errors->first('date_of_birth') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.date_of_birth_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="member_type_id">{{ trans('cruds.member.fields.member_type') }}</label>
                <select class="form-control select2 {{ $errors->has('member_type') ? 'is-invalid' : '' }}"
                    name="member_type_id" id="member_type_id" required>
                    @foreach($member_types as $id => $entry)
                    <option value="{{ $id }}" {{ old('member_type_id')==$id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('member_type'))
                <div class="invalid-feedback">
                    {{ $errors->first('member_type') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.member_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="phone_number">{{ trans('cruds.member.fields.phone_number') }}</label>
                <input class="form-control {{ $errors->has('phone_number') ? 'is-invalid' : '' }}" type="text"
                    name="phone_number" id="phone_number" value="{{ old('phone_number', '') }}" required>
                @if($errors->has('phone_number'))
                <div class="invalid-feedback">
                    {{ $errors->first('phone_number') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.phone_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="country_id">{{ trans('cruds.member.fields.country') }}</label>
                <select class="form-control select2 {{ $errors->has('country') ? 'is-invalid' : '' }}" name="country_id"
                    id="country_id" required>
                    @foreach($countries as $id => $entry)
                    <option value="{{ $id }}" {{ old('country_id')==$id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('country'))
                <div class="invalid-feedback">
                    {{ $errors->first('country') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.country_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="state_id">
                    State
                </label>
                <select class="form-control select2 {{ $errors->has('country') ? 'is-invalid' : '' }}" name="state_id"
                    id="state_id" required>
                </select>
                @if($errors->has('country'))
                <div class="invalid-feedback">
                    {{ $errors->first('country') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.country_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.member.fields.gender') }}</label>
                @foreach(App\Models\Member::GENDER_RADIO as $key => $label)
                <div class="form-check {{ $errors->has('gender') ? 'is-invalid' : '' }}">
                    <input class="form-check-input" type="radio" id="gender_{{ $key }}" name="gender" value="{{ $key }}"
                        {{ old('gender', '' )===(string) $key ? 'checked' : '' }}>
                    <label class="form-check-label" for="gender_{{ $key }}">{{ $label }}</label>
                </div>
                @endforeach
                @if($errors->has('gender'))
                <div class="invalid-feedback">
                    {{ $errors->first('gender') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.gender_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="address">{{ trans('cruds.member.fields.address') }}</label>
                <input class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text" name="address"
                    id="address" value="{{ old('address', '') }}">
                @if($errors->has('address'))
                <div class="invalid-feedback">
                    {{ $errors->first('address') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.address_helper') }}</span>
            </div>
            <x-input-photo name="profile_picture" />
            <div class="form-group">
                <label for="password">{{ trans('cruds.member.fields.password') }}</label>
                <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password"
                    name="password" id="password" value="{{ old('password', '') }}">
                @if($errors->has('password'))
                <div class="invalid-feedback">
                    {{ $errors->first('password') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.password_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
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
    });
</script>
@endsection
