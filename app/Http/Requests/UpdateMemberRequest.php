<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('member_edit');
    }

    public function rules()
    {
        return [
            'email_address' => [
                'required',
                'unique:members,email_address,' . request()->route('member')->id,
            ],
            'first_name' => [
                'string',
                'required',
            ],
            'middle_name' => [
                'string',
                'nullable',
            ],
            'last_name' => [
                'string',
                'required',
            ],
            'date_of_birth' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'member_type_id' => [
                'required',
                'integer',
            ],
            'member_role_id' => [
                'nullable',
                'integer',
            ],
            'phone_number' => [
                'string',
                'required',
            ],
            'country_id' => [
                'required',
                'integer',
            ],
            'address' => [
                'string',
                'nullable',
            ],
            'email_verified_at' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
        ];
    }
}