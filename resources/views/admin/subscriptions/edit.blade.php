@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.subscription.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.subscriptions.update', [$subscription->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.subscription.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                        id="name" value="{{ old('name', $subscription->name) }}" required>
                    @if ($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.subscription.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="description">{{ trans('cruds.subscription.fields.description') }}</label>
                    <textarea class="form-control textarea {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description"
                        id="description">{!! old('description', $subscription->description) !!}</textarea>
                    @if ($errors->has('description'))
                        <div class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.subscription.fields.description_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="features">{{ trans('cruds.subscription.fields.features') }}</label>
                    <textarea class="form-control {{ $errors->has('features') ? 'is-invalid' : '' }}" name="features" id="features"
                        required>{{ old('features', $subscription->features) }}</textarea>
                    @if ($errors->has('features'))
                        <div class="invalid-feedback">
                            {{ $errors->first('features') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.subscription.fields.features_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.subscription.fields.plan_type') }}</label>
                    <select class="form-control {{ $errors->has('plan_type') ? 'is-invalid' : '' }}" name="plan_type"
                        id="plan_type" required>
                        <option value disabled {{ old('plan_type', null) === null ? 'selected' : '' }}>
                            {{ trans('global.pleaseSelect') }}</option>
                        @foreach (App\Models\Subscription::PLAN_TYPE_SELECT as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('plan_type', $subscription->plan_type) === (string) $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('plan_type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('plan_type') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.subscription.fields.plan_type_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.subscription.fields.cycle_type') }}</label>
                    <select class="form-control {{ $errors->has('cycle_type') ? 'is-invalid' : '' }}" name="cycle_type"
                        id="cycle_type" required>
                        <option value disabled {{ old('cycle_type', null) === null ? 'selected' : '' }}>
                            {{ trans('global.pleaseSelect') }}</option>
                        @foreach (App\Models\Subscription::CYCLE_TYPE_SELECT as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('cycle_type', $subscription->cycle_type) === (string) $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('cycle_type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('cycle_type') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.subscription.fields.cycle_type_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required"
                        for="cycle_number">{{ trans('cruds.subscription.fields.cycle_number') }}</label>
                    <input class="form-control {{ $errors->has('cycle_number') ? 'is-invalid' : '' }}" type="number"
                        name="cycle_number" id="cycle_number"
                        value="{{ old('cycle_number', $subscription->cycle_number) }}" step="1" required>
                    @if ($errors->has('cycle_number'))
                        <div class="invalid-feedback">
                            {{ $errors->first('cycle_number') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.subscription.fields.cycle_number_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.subscription.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                        id="status" required>
                        <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                            {{ trans('global.pleaseSelect') }}</option>
                        @foreach (App\Models\Subscription::STATUS_SELECT as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('status', $subscription->status) === (string) $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('status'))
                        <div class="invalid-feedback">
                            {{ $errors->first('status') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.subscription.fields.status_helper') }}</span>
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
        $(document).ready(function() {
            function SimpleUploadAdapter(editor) {
                editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
                    return {
                        upload: function() {
                            return loader.file
                                .then(function(file) {
                                    return new Promise(function(resolve, reject) {
                                        // Init request
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('POST',
                                            '{{ route('admin.subscriptions.storeCKEditorImages') }}',
                                            true);
                                        xhr.setRequestHeader('x-csrf-token', window._token);
                                        xhr.setRequestHeader('Accept', 'application/json');
                                        xhr.responseType = 'json';

                                        // Init listeners
                                        var genericErrorText =
                                            `Couldn't upload file: ${ file.name }.`;
                                        xhr.addEventListener('error', function() {
                                            reject(genericErrorText)
                                        });
                                        xhr.addEventListener('abort', function() {
                                            reject()
                                        });
                                        xhr.addEventListener('load', function() {
                                            var response = xhr.response;

                                            if (!response || xhr.status !== 201) {
                                                return reject(response && response
                                                    .message ?
                                                    `${genericErrorText}\n${xhr.status} ${response.message}` :
                                                    `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`
                                                    );
                                            }

                                            $('form').append(
                                                '<input type="hidden" name="ck-media[]" value="' +
                                                response.id + '">');

                                            resolve({
                                                default: response.url
                                            });
                                        });

                                        if (xhr.upload) {
                                            xhr.upload.addEventListener('progress', function(
                                            e) {
                                                if (e.lengthComputable) {
                                                    loader.uploadTotal = e.total;
                                                    loader.uploaded = e.loaded;
                                                }
                                            });
                                        }

                                        // Send request
                                        var data = new FormData();
                                        data.append('upload', file);
                                        data.append('crud_id', '{{ $subscription->id ?? 0 }}');
                                        xhr.send(data);
                                    });
                                })
                        }
                    };
                }
            }

            var allEditors = document.querySelectorAll('.textarea');
            for (var i = 0; i < allEditors.length; ++i) {
                ClassicEditor.create(
                    allEditors[i], {
                        extraPlugins: [SimpleUploadAdapter]
                    }
                );
            }
        });
    </script>
@endsection
