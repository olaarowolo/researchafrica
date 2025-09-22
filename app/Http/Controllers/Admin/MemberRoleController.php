<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreMemberRoleRequest;
use App\Http\Requests\UpdateMemberRoleRequest;
use App\Models\MemberRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MemberRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = MemberRole::query()->where('status', 1)->get();
        return view('admin.memberRoles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.memberRoles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRoleRequest $request)
    {
        $input = $request->validated();
        MemberRole::create($input);

        return to_route('admin.member-roles.index')->with('success', 'Member Role Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(MemberRole $memberRole)
    {
        return view('admin.memberRoles.show', compact('memberRole'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MemberRole $memberRole)
    {
        return view('admin.memberRoles.edit', compact('memberRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRoleRequest $request, MemberRole $memberRole)
    {
        $input = $request->validated();
        $memberRole->update($input);

        return to_route('admin.member-roles.index')->with('success', 'Member Role Created Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MemberRole $memberRole)
    {
        $memberRole->delete();
        return back()->with('error', 'Member Role Deleted Successfully');
    }
}
