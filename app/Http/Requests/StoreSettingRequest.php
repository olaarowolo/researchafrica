<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSettingRequest extends FormRequest
{
    public function authorize()
    {
        return \Illuminate\Support\Facades\Gate::allows('setting_create');
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
            'instagram_url' => [
                'nullable',
                'url'
            ],
            'logo' => [
                'required',
            ],
            'favicon' => [
                'required',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
