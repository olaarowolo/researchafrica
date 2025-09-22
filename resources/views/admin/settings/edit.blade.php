@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.setting.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            {{-- @method('PUT') --}}
            @csrf
            <div class="form-group">
                <label class="required" for="website_name">{{ trans('cruds.setting.fields.website_name') }}</label>
                <input class="form-control {{ $errors->has('website_name') ? 'is-invalid' : '' }}" type="text"
                    name="website_name" id="website_name" value="{{ old('website_name', $setting->website_name) }}"
                    required>
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
                    name="website_email" id="website_email" value="{{ old('website_email', $setting->website_email) }}">
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
                    name="phone_number" id="phone_number" value="{{ old('phone_number', $setting->phone_number) }}"
                    required>
                @if ($errors->has('phone_number'))
                <div class="invalid-feedback">
                    {{ $errors->first('phone_number') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.phone_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="address">{{ trans('cruds.setting.fields.address') }}</label>
                <input class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text" name="address"
                    id="address" value="{{ old('address', $setting->address) }}" required>
                @if ($errors->has('address'))
                <div class="invalid-feedback">
                    {{ $errors->first('address') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.address_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="" for="description">Description</label>
                <textarea class="form-control textarea {{ $errors->has('description') ? 'is-invalid' : '' }}"
                    name="description" id="description">{{ old('description', $setting->description) }}</textarea>
                @if ($errors->has('description'))
                <div class="invalid-feedback">
                    {{ $errors->first('description') }}
                </div>
                @endif
            </div>
            <div class="form-group">
                <label class="" for="facebook_url"> Facebook URL </label>
                <input class="form-control {{ $errors->has('facebook_url') ? 'is-invalid' : '' }}" type="url"
                    name="facebook_url" id="facebook_url" value="{{ old('facebook_url', $setting->facebook_url) }}"
                    placeholder="">
                @if ($errors->has('facebook_url'))
                <div class="invalid-feedback">
                    {{ $errors->first('facebook_url') }}
                </div>
                @endif
            </div>
            <div class="form-group">
                <label class="" for="twitter_url"> Twitter URL </label>
                <input class="form-control {{ $errors->has('twitter_url') ? 'is-invalid' : '' }}" type="url"
                    name="twitter_url" id="twitter_url" value="{{ old('twitter_url', $setting->twitter_url) }}"
                    placeholder="">
                @if ($errors->has('twitter_url'))
                <div class="invalid-feedback">
                    {{ $errors->first('twitter_url') }}
                </div>
                @endif
            </div>
            <div class="form-group">
                <label class="" for="linkedin_url"> Linkedin URL </label>
                <input class="form-control {{ $errors->has('linkedin_url') ? 'is-invalid' : '' }}" type="url"
                    name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url', $setting->linkedin_url) }}"
                    placeholder="">
                @if ($errors->has('linkedin_url'))
                <div class="invalid-feedback">
                    {{ $errors->first('linkedin_url') }}
                </div>
                @endif
            </div>
            
            <div class="form-group">
                <label class="" for="instagram_url"> Instagram URL </label>
                <input class="form-control {{ $errors->has('instagram_url') ? 'is-invalid' : '' }}" type="url"
                    name="instagram_url" id="instagram_url" value="{{ old('instagram_url', $setting->instagram_url) }}"
                    placeholder="">
                @if ($errors->has('instagram_url'))
                <div class="invalid-feedback">
                    {{ $errors->first('instagram_url') }}
                </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-6">
                    <x-input-photo name="logo" photo="{{ $setting->logo ? $setting->logo->getUrl() : null }}" />
                </div>
                <div class="col-md-6">
                    <x-input-photo name="favicon"
                        photo="{{ $setting->favicon ? $setting->favicon->getUrl() : null }}" />
                </div>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.setting.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status"
                    required>
                    <option value disabled {{ old('status', null)===null ? 'selected' : '' }}>
                        {{ trans('global.pleaseSelect') }}</option>
                    @foreach (App\Models\Setting::STATUS_SELECT as $key => $label)
                    <option value="{{ $key }}" {{ old('status', $setting->status) === (string) $key ? 'selected' : ''
                        }}>
                        {{ $label }}</option>
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
@endsection
