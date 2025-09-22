<?php

namespace App\Http\Requests;

use App\Models\Article;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreArticleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('article_create');
    }

    public function rules()
    {
        return [
            'member_id' => [
                'required',
                'integer',
            ],
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
                'file',
                'mimes:doc,docx',
            ],
            'pdf_doc' => [
                'required_if: article_status, 3',
                'file',
                'mimes:pdf',
            ],
            'keywords' => [
                'nullable',
                'array',
            ],
            'article_status' => [
                'required',
            ],
            'access_type' => [
                'required',
            ],
            'amount' => [
                'required_if: access_type, 2',
            ],
            'publish_date' => [
                'nullable',
                'date:mm/dd/yyyy',
            ],
            'volume' => [
                'required_if: article_status, 3',
            ],
            'issue_no' => [
                'required_if: article_status, 3',
            ],
            'doi_link' => [
                'nullable',
                'url',
            ],
        ];
    }
}
