<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
            'title' => [
                'string',
                'required',
            ],
            'article_category_id' => [
                'required',
                'integer',
            ],
            'article_sub_category_id' => [
                'required',
                'integer',
            ],
            'other_authors' => [
                'string',
                'nullable',
            ],            
            'corresponding_authors' => [
                'string',
                'nullable',
            ],
            'institute_organization' => [
                'string',
                'nullable',
            ],
            'abstract' => [
                'required',
            ],
            'upload_paper' => [
                'required',
            ],
            // 'publish_date' => [
            //     'required',
            // ],
            'article_keyword_id' => [
                'array',
                'nullable',
            ],
            'access_type' => [
                'required',
                'integer',
            ],
            'amount' => [
                'nullable',
            ],

        ];
    }
}
