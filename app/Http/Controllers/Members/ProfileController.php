<?php

namespace App\Http\Controllers\Members;

// use App\Http\Requests\UpdateProfileRequest;
use App\Models\ReviewerAccept;
use App\Models\State;
use App\Models\Article;
use App\Models\Country;
use App\Models\EditorAccept;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\MemberTypeTrait;
use App\Http\Requests\Member\UpdateProfileRequest;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\PublisherAccept;
use App\Models\ReviewerAcceptFinal;

class ProfileController extends Controller
{
    use MediaUploadingTrait;
    use MemberTypeTrait;
    public function profilePage()
    {
        if ($this->author()) {
            abort_unless($this->author(), Response::HTTP_UNAUTHORIZED);
            $reviewArticlesStatus1 = auth('member')->user()->memberArticles->where('article_status', 1);
            $reviewArticlesStatus2 = auth('member')->user()->memberArticles->where('article_status', 2);
            $reviewArticles = $reviewArticlesStatus1->merge($reviewArticlesStatus2);
            // $reviewArticles = auth('member')->user()->memberArticles?->where('article_status', 2)?->orWhere('article_status', 1)->latest()->get();
            $publishArticles = auth('member')->user()->memberArticles()->where('article_status', '3')->latest()->get();
            return view('member.profile.author', compact('reviewArticles', 'publishArticles'));
        }

        if ($this->editor()) {
            abort_unless($this->editor(), Response::HTTP_UNAUTHORIZED);
            $unaccepted = EditorAccept::with(['member'])->distinct('article_id')->whereNull('member_id')->pluck('article_id')->toArray();

            $newArticles = Article::with('member')->whereIn('id', $unaccepted)->latest()->get();
            $accept = EditorAccept::with(['member'])->distinct('article_id')->where('member_id', auth('member')->id())->pluck('article_id')->toArray();
            $articles = Article::with('member')->whereIntegerInRaw('id', $accept)->latest()->get();
            $processed = $articles->where('article_status', 3);
            $processing = $articles->where('article_status', 2);
            // dd($newArticles);
            return view('member.profile.editor', compact('newArticles', 'processed', 'processing'));
        }

        if ($this->reviewer() || $this->reviewerFinal()) {
            abort_unless($this->reviewer() || $this->reviewerFinal(), Response::HTTP_UNAUTHORIZED);

            if ($this->reviewer()) {
                $unaccepted = ReviewerAccept::with(['member'])->distinct('article_id')
                    ->where(function ($query) {
                        $query->whereNull('assigned_id');
                        $query->orWhere('assigned_id', auth('member')->user()->id);
                    })->whereNull('member_id')
                    ->pluck('article_id')->toArray();

                $accept = ReviewerAccept::with(['member'])->distinct('article_id')->where('member_id', auth('member')->id())->pluck('article_id')->toArray();
            } else {
                $unaccepted = ReviewerAcceptFinal::with(['member'])->distinct('article_id')->whereNull('member_id')->pluck('article_id')->toArray();

                $accept = ReviewerAcceptFinal::with(['member'])->distinct('article_id')->where('member_id', auth('member')->id())->pluck('article_id')->toArray();
            }


            $newArticles = Article::with('member')->whereIn('id', $unaccepted)->latest()->get();

            $acceptedArticle = Article::with('member')->whereIntegerInRaw('id', $accept)->latest()->get();

            // dd($accept);
            return view('member.profile.reviewer', compact('newArticles', 'acceptedArticle'));
        }

        if ($this->publisher()) {
            $unaccepted = PublisherAccept::with(['member'])->distinct('article_id')->whereNull('member_id')->pluck('article_id')->toArray();


            $newArticles = Article::with('member')->whereIn('id', $unaccepted)->latest()->get();
            $accept = PublisherAccept::with(['member'])->distinct('article_id')->where('member_id', auth('member')->id())->pluck('article_id')->toArray();
            $acceptedArticle = Article::with('member')->whereIntegerInRaw('id', $accept)->latest()->get();

            return view('member.profile.publisher', compact('newArticles', 'acceptedArticle'));
        }

        if ($this->account()) {
            return view('member.profile.researcher');
        }
    }


    public function editProfile()
    {
        $user = Auth::guard('member')->user();
        $countries = Country::get(['name', 'id']);
        $states = State::where('country_id', $user->country_id)->get(['name', 'id']);

        return view('member.profile.edit', compact('user', 'countries', 'states'));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $input = $request->validated();
        auth('member')->user()->update($input);

        return back()->with('success', 'Profile Updated Successfully');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            "old_password" => [
                'required',
            ],
            "password" => [
                'required',
                'confirmed'
            ],
        ]);

        $check = Hash::check($request->old_password, auth('member')->user()->password);

        if ($check == false) {
            return back()->withErrors([
                'message' => [
                    'Old Password is Incorrect'
                ],
            ]);
        }


        auth('member')->user()->update([
            'password' => bcrypt($request->password)
        ]);

        return back()->with('success', 'Password Updated Successfully');
    }

    public function profile_picture(Request $request)
    {
        $validated = $request->validate([
            'profile_picture' => [
                'required',
                'mimes: png,jpg,jpeg,gif,tiff',
                'max:2048',
            ]
        ]);

        $profile_picture = $request->file('profile_picture');

        if ($profile_picture) {
            if ($photo = auth('member')->user()->profile_picture) {
                $photo->delete();
            }
            $profile_picture = $this->manualStoreMedia($profile_picture)['name'];
            auth('member')->user()->addMedia(storage_path('tmp/uploads/' . basename($profile_picture)))->toMediaCollection('profile_picture');
        }

        return back()->with('success', 'Photo Uploaded Successfully');
    }
}
