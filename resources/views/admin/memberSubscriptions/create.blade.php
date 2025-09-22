@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.memberSubscription.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.member-subscriptions.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="member_email_id">{{ trans('cruds.memberSubscription.fields.member_email') }}</label>
                <select class="form-control select2 {{ $errors->has('member_email') ? 'is-invalid' : '' }}" name="member_email_id" id="member_email_id" required>
                    @foreach($member_emails as $id => $entry)
                        <option value="{{ $id }}" {{ old('member_email_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('member_email'))
                    <div class="invalid-feedback">
                        {{ $errors->first('member_email') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.memberSubscription.fields.member_email_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="subscription_name_id">{{ trans('cruds.memberSubscription.fields.subscription_name') }}</label>
                <select class="form-control select2 {{ $errors->has('subscription_name') ? 'is-invalid' : '' }}" name="subscription_name_id" id="subscription_name_id" required>
                    @foreach($subscription_names as $id => $entry)
                        <option value="{{ $id }}" {{ old('subscription_name_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('subscription_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('subscription_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.memberSubscription.fields.subscription_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.memberSubscription.fields.payment_method') }}</label>
                <select class="form-control {{ $errors->has('payment_method') ? 'is-invalid' : '' }}" name="payment_method" id="payment_method" required>
                    <option value disabled {{ old('payment_method', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\MemberSubscription::PAYMENT_METHOD_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('payment_method', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('payment_method'))
                    <div class="invalid-feedback">
                        {{ $errors->first('payment_method') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.memberSubscription.fields.payment_method_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="amount">{{ trans('cruds.memberSubscription.fields.amount') }}</label>
                <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', '') }}" step="0.01" required>
                @if($errors->has('amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.memberSubscription.fields.amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.memberSubscription.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\MemberSubscription::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', '2') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.memberSubscription.fields.status_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="expiry_date">{{ trans('cruds.memberSubscription.fields.expiry_date') }}</label>
                <input class="form-control datetime {{ $errors->has('expiry_date') ? 'is-invalid' : '' }}" type="text" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}">
                @if($errors->has('expiry_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('expiry_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.memberSubscription.fields.expiry_date_helper') }}</span>
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