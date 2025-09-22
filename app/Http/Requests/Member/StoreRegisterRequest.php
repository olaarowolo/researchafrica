<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "title" => [
                'nullable',
            ],
            "first_name" => [
                'required',
                'string',
            ],
            "last_name" => [
                'required',
                'string',
            ],
            "member_type_id" => [
                'required',
                'integer',
            ],
            "middle_name" => [
                'nullable',
                'string',
            ],
            "email_address" => [
                'required',
                'email',
                'unique:members,email_address'
            ],
            "phone_number" => [
                'required',
            ],
            "member_role_id" => [
                'required',
                'integer',
            ],
            "country_id" => [
                'required',
                'integer',
            ],
            "state_id" => [
                'required',
                'integer',
            ],
            "password" => [
                'required',
                'min:8',
                'confirmed'
            ],
        ];
    }
}
