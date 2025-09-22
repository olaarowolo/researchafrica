<?php

namespace App\Http\Requests;

use App\Models\MemberSubscription;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMemberSubscriptionRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('member_subscription_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:member_subscriptions,id',
        ];
    }
}
