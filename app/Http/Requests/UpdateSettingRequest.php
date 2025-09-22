<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('setting_edit');
    }

    public function rules()
    {
        return [
            'website_name' => [
                'string',
                'required',
            ],
            'phone_number' => [
                'string',
                'required',
            ],
            'address' => [
                'string',
                'required',
            ],
            'about' => [
                'nullable',
            ],
            'facebook_url' => [
                'nullable',
                'url'
            ],
            'twitter_url' => [
                'nullable',
                'url'
            ],
            'logo' => [
                'nullable',
            ],
            'favicon' => [
                'nullable',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
