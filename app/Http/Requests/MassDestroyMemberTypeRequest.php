<?php

namespace App\Http\Requests;

use App\Models\MemberType;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMemberTypeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('member_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:member_types,id',
        ];
    }
}
