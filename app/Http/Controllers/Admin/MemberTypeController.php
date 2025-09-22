<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyMemberTypeRequest;
use App\Http\Requests\StoreMemberTypeRequest;
use App\Http\Requests\UpdateMemberTypeRequest;
use App\Models\MemberType;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberTypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('member_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberTypes = MemberType::all();

        return view('admin.memberTypes.index', compact('memberTypes'));
    }

    public function create()
    {
        abort_if(Gate::denies('member_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.memberTypes.create');
    }

    public function store(StoreMemberTypeRequest $request)
    {
        $memberType = MemberType::create($request->all());

        return redirect()->route('admin.member-types.index');
    }

    public function edit(MemberType $memberType)
    {
        abort_if(Gate::denies('member_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.memberTypes.edit', compact('memberType'));
    }

    public function update(UpdateMemberTypeRequest $request, MemberType $memberType)
    {
        $memberType->update($request->all());

        return redirect()->route('admin.member-types.index');
    }

    public function show(MemberType $memberType)
    {
        abort_if(Gate::denies('member_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.memberTypes.show', compact('memberType'));
    }

    public function destroy(MemberType $memberType)
    {
        abort_if(Gate::denies('member_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberType->delete();

        return back();
    }

    public function massDestroy(MassDestroyMemberTypeRequest $request)
    {
        $memberTypes = MemberType::find(request('ids'));

        foreach ($memberTypes as $memberType) {
            $memberType->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
