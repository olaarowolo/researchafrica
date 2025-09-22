@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.setting.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="website_name">{{ trans('cruds.setting.fields.website_name') }}</label>
                    <input class="form-control {{ $errors->has('website_name') ? 'is-invalid' : '' }}" type="text"
                        name="website_name" id="website_name" value="{{ old('website_name', '') }}" required>
                    @if ($errors->has('website_name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('website_name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.setting.fields.website_name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="website_email">{{ trans('cruds.setting.fields.website_email') }}</label>
                    <input class="form-control {{ $errors->has('website_email') ? 'is-invalid' : '' }}" type="email"
                        name="website_email" id="website_email" value="{{ old('website_email') }}">
                    @if ($errors->has('website_email'))
                        <div class="invalid-feedback">
                            {{ $errors->first('website_email') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.setting.fields.website_email_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="phone_number">{{ trans('cruds.setting.fields.phone_number') }}</label>
                    <input class="form-control {{ $errors->has('phone_number') ? 'is-invalid' : '' }}" type="text"
                        name="phone_number" id="phone_number" value="{{ old('phone_number', '') }}" required>
                    @if ($errors->has('phone_number'))
                        <div class="invalid-feedback">
                            {{ $errors->first('phone_number') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.setting.fields.phone_number_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="address">{{ trans('cruds.setting.fields.address') }}</label>
                    <input class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text"
                        name="address" id="address" value="{{ old('address', '') }}" required>
                    @if ($errors->has('address'))
                        <div class="invalid-feedback">
                            {{ $errors->first('address') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.setting.fields.address_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="" for="about">About</label>
                    <textarea class="form-control textarea {{ $errors->has('about') ? 'is-invalid' : '' }}" name="about" id="about">{{ old('about', '') }}</textarea>
                    @if ($errors->has('about'))
                        <div class="invalid-feedback">
                            {{ $errors->first('about') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="" for="facebook_url"> Facebook URL </label>
                    <input class="form-control {{ $errors->has('facebook_url') ? 'is-invalid' : '' }}" type="text"
                        name="facebook_url" id="facebook_url" value="{{ old('facebook_url', '') }}" placeholder="">
                    @if ($errors->has('facebook_url'))
                        <div class="invalid-feedback">
                            {{ $errors->first('facebook_url') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="" for="twitter_url"> Twitter URL </label>
                    <input class="form-control {{ $errors->has('twitter_url') ? 'is-invalid' : '' }}" type="text"
                        name="twitter_url" id="twitter_url" value="{{ old('twitter_url', '') }}" placeholder="">
                    @if ($errors->has('twitter_url'))
                        <div class="invalid-feedback">
                            {{ $errors->first('twitter_url') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="" for="instagram_url"> Instagram URL </label>
                    <input class="form-control {{ $errors->has('instagram_url') ? 'is-invalid' : '' }}" type="text"
                        name="instagram_url" id="instagram_url" value="{{ old('instagram_url', '') }}" placeholder="">
                    @if ($errors->has('instagram_url'))
                        <div class="invalid-feedback">
                            {{ $errors->first('instagram_url') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="required" for="logo">{{ trans('cruds.setting.fields.logo') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('logo') ? 'is-invalid' : '' }}" id="logo-dropzone">
                    </div>
                    @if ($errors->has('logo'))
                        <div class="invalid-feedback">
                            {{ $errors->first('logo') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.setting.fields.logo_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="favicon">{{ trans('cruds.setting.fields.favicon') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('favicon') ? 'is-invalid' : '' }}"
                        id="favicon-dropzone">
                    </div>
                    @if ($errors->has('favicon'))
                        <div class="invalid-feedback">
                            {{ $errors->first('favicon') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.setting.fields.favicon_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.setting.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                        id="status" required>
                        <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                            {{ trans('global.pleaseSelect') }}</option>
                        @foreach (App\Models\Setting::STATUS_SELECT as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('status', '') === (string) $key ? 'selected' : '' }}>{{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('status'))
                        <div class="invalid-feedback">
                            {{ $errors->first('status') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.setting.fields.status_helper') }}</span>
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
        Dropzone.options.logoDropzone = {
            url: '{{ route('admin.settings.storeMedia') }}',
            maxFilesize: 2, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 2,
                width: 1024,
                height: 1024
            },
            success: function(file, response) {
                $('form').find('input[name="logo"]').remove()
                $('form').append('<input type="hidden" name="logo" value="' + response.name + '">')
            },
            removedfile: function(file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="logo"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function() {
                @if (isset($setting) && $setting->logo)
                    var file = {!! json_encode($setting->logo) !!}
                    this.options.addedfile.call(this, file)
                    this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="logo" value="' + file.file_name + '">')
                    this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function(file, response) {
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
    <script>
        Dropzone.options.faviconDropzone = {
            url: '{{ route('admin.settings.storeMedia') }}',
            maxFilesize: 2, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 2,
                width: 1024,
                height: 1024
            },
            success: function(file, response) {
                $('form').find('input[name="favicon"]').remove()
                $('form').append('<input type="hidden" name="favicon" value="' + response.name + '">')
            },
            removedfile: function(file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="favicon"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function() {
                @if (isset($setting) && $setting->favicon)
                    var file = {!! json_encode($setting->favicon) !!}
                    this.options.addedfile.call(this, file)
                    this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="favicon" value="' + file.file_name + '">')
                    this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function(file, response) {
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
