@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.memberSubscription.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.member-subscriptions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.memberSubscription.fields.id') }}
                        </th>
                        <td>
                            {{ $memberSubscription->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.memberSubscription.fields.member_email') }}
                        </th>
                        <td>
                            {{ $memberSubscription->member_email->email_address ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.memberSubscription.fields.subscription_name') }}
                        </th>
                        <td>
                            {{ $memberSubscription->subscription_name->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.memberSubscription.fields.payment_method') }}
                        </th>
                        <td>
                            {{ App\Models\MemberSubscription::PAYMENT_METHOD_SELECT[$memberSubscription->payment_method] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.memberSubscription.fields.amount') }}
                        </th>
                        <td>
                            {{ $memberSubscription->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.memberSubscription.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\MemberSubscription::STATUS_SELECT[$memberSubscription->status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.memberSubscription.fields.expiry_date') }}
                        </th>
                        <td>
                            {{ $memberSubscription->expiry_date }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.member-subscriptions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection