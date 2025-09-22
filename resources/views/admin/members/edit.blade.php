@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.member.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.members.update", [$member->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="email_address">{{ trans('cruds.member.fields.email_address') }}</label>
                <input class="form-control {{ $errors->has('email_address') ? 'is-invalid' : '' }}" type="email"
                    name="email_address" id="email_address" value="{{ old('email_address', $member->email_address) }}"
                    required>
                @if($errors->has('email_address'))
                <div class="invalid-feedback">
                    {{ $errors->first('email_address') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.email_address_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.member.fields.title') }}</label>
                <select class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" name="title" id="title">
                    <option value disabled {{ old('title', null)===null ? 'selected' : '' }}>{{
                        trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Member::TITLE_SELECT as $key => $label)
                    <option value="{{ $key }}" {{ old('title', $member->title) === (string) $key ? 'selected' : '' }}>{{
                        $label }}</option>
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
                    name="first_name" id="first_name" value="{{ old('first_name', $member->first_name) }}" required>
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
                    name="middle_name" id="middle_name" value="{{ old('middle_name', $member->middle_name) }}">
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
                    name="last_name" id="last_name" value="{{ old('last_name', $member->last_name) }}" required>
                @if($errors->has('last_name'))
                <div class="invalid-feedback">
                    {{ $errors->first('last_name') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.last_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="date_of_birth">{{ trans('cruds.member.fields.date_of_birth') }}</label>
                <input class="form-control date {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}" type="text"
                    name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth) }}">
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
                    <option value="{{ $id }}" {{ (old('member_type_id') ? old('member_type_id') : $member->
                        member_type->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
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
                    name="phone_number" id="phone_number" value="{{ old('phone_number', $member->phone_number) }}"
                    required>
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
                    <option value="{{ $id }}" {{ (old('country_id') ? old('country_id') : $member->country->id ?? '') ==
                        $id ? 'selected' : '' }}>{{ $entry }}</option>
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
                <label>{{ trans('cruds.member.fields.gender') }}</label>
                @foreach(App\Models\Member::GENDER_RADIO as $key => $label)
                <div class="form-check {{ $errors->has('gender') ? 'is-invalid' : '' }}">
                    <input class="form-check-input" type="radio" id="gender_{{ $key }}" name="gender" value="{{ $key }}"
                        {{ old('gender', $member->gender) === (string) $key ? 'checked' : '' }}>
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
                    id="address" value="{{ old('address', $member->address) }}">
                @if($errors->has('address'))
                <div class="invalid-feedback">
                    {{ $errors->first('address') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.address_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="profile_picture">{{ trans('cruds.member.fields.profile_picture') }}</label>
                <div class="needsclick dropzone {{ $errors->has('profile_picture') ? 'is-invalid' : '' }}"
                    id="profile_picture-dropzone">
                </div>
                @if($errors->has('profile_picture'))
                <div class="invalid-feedback">
                    {{ $errors->first('profile_picture') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.profile_picture_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.member.fields.registration_via') }}</label>
                <select class="form-control {{ $errors->has('registration_via') ? 'is-invalid' : '' }}"
                    name="registration_via" id="registration_via">
                    <option value disabled {{ old('registration_via', null)===null ? 'selected' : '' }}>{{
                        trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Member::REGISTRATION_VIA_SELECT as $key => $label)
                    <option value="{{ $key }}" {{ old('registration_via', $member->registration_via) === (string) $key ?
                        'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('registration_via'))
                <div class="invalid-feedback">
                    {{ $errors->first('registration_via') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.registration_via_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.member.fields.email_verified') }}</label>
                <select class="form-control {{ $errors->has('email_verified') ? 'is-invalid' : '' }}"
                    name="email_verified" id="email_verified">
                    <option value disabled {{ old('email_verified', null)===null ? 'selected' : '' }}>{{
                        trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Member::EMAIL_VERIFIED_SELECT as $key => $label)
                    <option value="{{ $key }}" {{ old('email_verified', $member->email_verified) === (string) $key ?
                        'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('email_verified'))
                <div class="invalid-feedback">
                    {{ $errors->first('email_verified') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.email_verified_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="email_verified_at">{{ trans('cruds.member.fields.email_verified_at') }}</label>
                <input class="form-control datetime {{ $errors->has('email_verified_at') ? 'is-invalid' : '' }}"
                    type="text" name="email_verified_at" id="email_verified_at"
                    value="{{ old('email_verified_at', $member->email_verified_at) }}">
                @if($errors->has('email_verified_at'))
                <div class="invalid-feedback">
                    {{ $errors->first('email_verified_at') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.email_verified_at_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.member.fields.verified') }}</label>
                <select class="form-control {{ $errors->has('verified') ? 'is-invalid' : '' }}" name="verified"
                    id="verified">
                    <option value disabled {{ old('verified', null)===null ? 'selected' : '' }}>{{
                        trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Member::VERIFIED_SELECT as $key => $label)
                    <option value="{{ $key }}" {{ old('verified', $member->verified) === (string) $key ? 'selected' : ''
                        }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('verified'))
                <div class="invalid-feedback">
                    {{ $errors->first('verified') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.verified_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.member.fields.profile_completed') }}</label>
                <select class="form-control {{ $errors->has('profile_completed') ? 'is-invalid' : '' }}"
                    name="profile_completed" id="profile_completed">
                    <option value disabled {{ old('profile_completed', null)===null ? 'selected' : '' }}>{{
                        trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Member::PROFILE_COMPLETED_SELECT as $key => $label)
                    <option value="{{ $key }}" {{ old('profile_completed', $member->profile_completed) === (string) $key
                        ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('profile_completed'))
                <div class="invalid-feedback">
                    {{ $errors->first('profile_completed') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.profile_completed_helper') }}</span>
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
    Dropzone.options.profile_pictureDropzone = {
    url: '{{ route('admin.members.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="profile_picture"]').remove()
      $('form').append('<input type="hidden" name="profile_picture" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="profile_picture"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($member) && $member->profile_picture)
      var file = {!! json_encode($member->profile_picture) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="profile_picture" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
    error: function (file, response) {
        if ($.type(response) === 'string') {
            var message = response //dropzone sends it's own error messages in string
        } else {
            var message = response.errors.file
        }
        file.previewElement.classList.add('dz-error')
        _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
        _results = []
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i]
            _results.push(node.textContent = message)
        }

        return _results
    }
}

</script>
@endsection
