<?php

namespace App\Http\Requests;

use App\Models\MemberSubscription;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMemberSubscriptionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('member_subscription_create');
    }

    public function rules()
    {
        return [
            'member_email_id' => [
                'required',
                'integer',
            ],
            'subscription_name_id' => [
                'required',
                'integer',
            ],
            'payment_method' => [
                'required',
            ],
            'amount' => [
                'required',
            ],
            'status' => [
                'required',
            ],
            'expiry_date' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
        ];
    }
}
