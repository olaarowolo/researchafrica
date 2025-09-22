<?php

namespace App\Http\Requests;

use App\Models\ArticleCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateArticleCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('article_category_edit');
    }

    public function rules()
    {
        return [
            'category_name' => [
                'string',
                'required',
            ],
        ];
    }
}
