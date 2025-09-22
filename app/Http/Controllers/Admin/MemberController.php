<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Member;
use App\Models\Country;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\EmailVerify;
use Illuminate\Http\Request;
use App\Mail\EmailVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyMemberRequest;
use App\Http\Controllers\Traits\MediaUploadingTrait;

class MemberController extends Controller
{
    use MediaUploadingTrait;
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('member_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $members = Member::with(['member_type', 'country', 'media', 'member_role'])->get();

        $member_types = MemberType::get();
        $member_roles = MemberRole::get();


        $countries = Country::get();

        return view('admin.members.index', compact('countries', 'member_types', 'members', 'member_roles'));
    }

    public function create()
    {
        abort_if(Gate::denies('member_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $member_types = MemberType::whereNotIn('id', [1])->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $member_roles = MemberRole::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $countries = Country::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.members.create', compact('countries', 'member_types', 'member_roles'));
    }

    public function store(StoreMemberRequest $request)
    {

        $input = $request->all();
        // $input['password'] = 'password';

        $member = Member::create($input);

        $profile_picture = $request->file('profile_picture');

        if ($profile_picture) {
            $profile_picture = $this->manualStoreMedia($profile_picture)['name'];
            $member->addMedia(storage_path('tmp/uploads/' . basename($profile_picture)))->toMediaCollection('profile_picture');
        }

        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerify::create([
            'token' => $token,
            'member_id' => $member->id,
        ]);


        Mail::to($member->email_address)->send(new EmailVerification($member, $token));

        return redirect()->route('admin.members.index');
    }

    public function edit(Member $member)
    {
        abort_if(Gate::denies('member_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $member_types = MemberType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $member_roles = MemberRole::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');


        $countries = Country::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $member->load('member_type', 'country', 'member_role');

        return view('admin.members.edit', compact('countries', 'member', 'member_types', 'member_roles'));
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        $member->update($request->all());

        if ($request->input('profile_picture', false)) {
            if (! $member->profile_picture || $request->input('profile_picture') !== $member->profile_picture->file_name) {
                if ($member->profile_picture) {
                    $member->profile_picture->delete();
                }
                $member->addMedia(storage_path('tmp/uploads/' . basename($request->input('profile_picture'))))->toMediaCollection('profile_picture');
            }
        } elseif ($member->profile_picture) {
            $member->profile_picture->delete();
        }

        return redirect()->route('admin.members.index');
    }

    public function show(Member $member)
    {
        abort_if(Gate::denies('member_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $member->load('member_type', 'country', 'memberArticles', 'member_role');

        return view('admin.members.show', compact('member'));
    }

    public function destroy(Member $member)
    {
        abort_if(Gate::denies('member_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rand = rand(0000, 99999);
        $email = $member->email_address;
        $member->update([
            'email_address' => $email."".$rand
        ]);
        $member->delete();

        return back();
    }

    public function massDestroy(MassDestroyMemberRequest $request)
    {
        $members = Member::find(request('ids'));

        foreach ($members as $member) {$rand = rand(0000, 99999);
        $email = $member->email_address;
        $member->update([
            'email_address' => $email."".$rand
        ]);
        $member->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    // public function storeCKEditorImages(Request $request)
    // {
    //     abort_if(Gate::denies('member_create') && Gate::denies('member_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    //     $model         = new Member();
    //     $model->id     = $request->input('crud_id', 0);
    //     $model->exists = true;
    //     $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

    //     return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    // }
}
