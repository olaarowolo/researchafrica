<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyMemberSubscriptionRequest;
use App\Http\Requests\StoreMemberSubscriptionRequest;
use App\Http\Requests\UpdateMemberSubscriptionRequest;
use App\Models\Member;
use App\Models\MemberSubscription;
use App\Models\Subscription;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberSubscriptionsController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('member_subscription_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberSubscriptions = MemberSubscription::with(['member_email', 'subscription_name'])->get();

        $members = Member::get();

        $subscriptions = Subscription::get();

        return view('admin.memberSubscriptions.index', compact('memberSubscriptions', 'members', 'subscriptions'));
    }

    public function create()
    {
        abort_if(Gate::denies('member_subscription_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $member_emails = Member::pluck('email_address', 'id')->prepend(trans('global.pleaseSelect'), '');

        $subscription_names = Subscription::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.memberSubscriptions.create', compact('member_emails', 'subscription_names'));
    }

    public function store(StoreMemberSubscriptionRequest $request)
    {
        $memberSubscription = MemberSubscription::create($request->all());

        return redirect()->route('admin.member-subscriptions.index');
    }

    public function edit(MemberSubscription $memberSubscription)
    {
        abort_if(Gate::denies('member_subscription_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $member_emails = Member::pluck('email_address', 'id')->prepend(trans('global.pleaseSelect'), '');

        $subscription_names = Subscription::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $memberSubscription->load('member_email', 'subscription_name');

        return view('admin.memberSubscriptions.edit', compact('memberSubscription', 'member_emails', 'subscription_names'));
    }

    public function update(UpdateMemberSubscriptionRequest $request, MemberSubscription $memberSubscription)
    {
        $memberSubscription->update($request->all());

        return redirect()->route('admin.member-subscriptions.index');
    }

    public function show(MemberSubscription $memberSubscription)
    {
        abort_if(Gate::denies('member_subscription_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberSubscription->load('member_email', 'subscription_name');

        return view('admin.memberSubscriptions.show', compact('memberSubscription'));
    }

    public function destroy(MemberSubscription $memberSubscription)
    {
        abort_if(Gate::denies('member_subscription_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $memberSubscription->delete();

        return back();
    }

    public function massDestroy(MassDestroyMemberSubscriptionRequest $request)
    {
        $memberSubscriptions = MemberSubscription::find(request('ids'));

        foreach ($memberSubscriptions as $memberSubscription) {
            $memberSubscription->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
