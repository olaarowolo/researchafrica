@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.member.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.members.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.id') }}
                        </th>
                        <td>
                            {{ $member->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.email_address') }}
                        </th>
                        <td>
                            {{ $member->email_address }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.title') }}
                        </th>
                        <td>
                            {{ App\Models\Member::TITLE_SELECT[$member->title] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.first_name') }}
                        </th>
                        <td>
                            {{ $member->first_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.middle_name') }}
                        </th>
                        <td>
                            {{ $member->middle_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.last_name') }}
                        </th>
                        <td>
                            {{ $member->last_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.date_of_birth') }}
                        </th>
                        <td>
                            {{ $member->date_of_birth }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.member_type') }}
                        </th>
                        <td>
                            {{ $member->member_type->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Member Role
                        </th>
                        <td>
                            {{ $member->member_role->title ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.phone_number') }}
                        </th>
                        <td>
                            {{ $member->phone_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.country') }}
                        </th>
                        <td>
                            {{ $member->country->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.gender') }}
                        </th>
                        <td>
                            {{ App\Models\Member::GENDER_RADIO[$member->gender] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.address') }}
                        </th>
                        <td>
                            {{ $member->address }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.profile_picture') }}
                        </th>
                        <td>
                            @if($member->profile_picture)
                            <a href="{{ $member->profile_picture->getUrl() }}" target="_blank"
                                style="display: inline-block">
                                <img src="{{ $member->profile_picture->getUrl('thumb') }}">
                            </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.registration_via') }}
                        </th>
                        <td>
                            {{ App\Models\Member::REGISTRATION_VIA_SELECT[$member->registration_via] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.email_verified') }}
                        </th>
                        <td>
                            {{ App\Models\Member::EMAIL_VERIFIED_SELECT[$member->email_verified] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.email_verified_at') }}
                        </th>
                        <td>
                            {{ $member->email_verified_at }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.verified') }}
                        </th>
                        <td>
                            {{ App\Models\Member::VERIFIED_SELECT[$member->verified] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.member.fields.profile_completed') }}
                        </th>
                        <td>
                            {{ App\Models\Member::PROFILE_COMPLETED_SELECT[$member->profile_completed] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.members.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#member_articles" role="tab" data-toggle="tab">
                {{ trans('cruds.article.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="member_articles">
            @includeIf('admin.members.relationships.memberArticles', ['articles' => $member->memberArticles])
        </div>
    </div>
</div>

@endsection