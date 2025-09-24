<?php

namespace App\Modules\AfriScribe\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'ra_service' => 'required|string',
            'product' => 'required|string',
            'location' => 'required|string',
            'service_type' => 'required|string',
            'word_count' => 'nullable|integer|min:100',
            'addons' => 'nullable|array',
            'addons.*' => 'string',
            'referral' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'file' => 'required|file|mimes:doc,docx,pdf,txt|max:10240', // 10MB max
        ];
    }
}