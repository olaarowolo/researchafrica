<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            "first_name" => [
                'required',
                'string',
            ],
            "middle_name" => [
                'nullable',
                'string',
            ],
            "last_name" => [
                'required',
                'string',
            ],
            "phone_number" => [
                'required',
                'string',
            ],
            "gender" => [
                'required',
                'string',
            ],
            "date_of_birth" => [
                'required',
            ],
            "country_id" => [
                'required',
                'integer',
            ],
            "state_id" => [
                'required',
                'integer',
            ],
        ];
    }
}
