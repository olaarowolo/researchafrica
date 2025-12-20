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

class ProfileSecurityController extends Controller
{
    use MediaUploadingTrait;
    use MemberTypeTrait;
    public function profilePage()
    {
        \Log::info('Profile page called', ['user' => auth('member')->user()]);
        $user = auth('member')->user();
        if (!$user) {
            \Log::error('No authenticated user');
            abort(401, 'Not authenticated');
        }

        $memberTypeId = $user->member_type_id;
        \Log::info('Member type', ['type' => $memberTypeId]);

        if ($memberTypeId == 1) { // Author
            $reviewArticlesStatus1 = $user->memberArticles->where('article_status', 1);
            $reviewArticlesStatus2 = $user->memberArticles->where('article_status', 2);
            $reviewArticles = $reviewArticlesStatus1->merge($reviewArticlesStatus2);
            $publishArticles = $user->memberArticles()->where('article_status', '3')->latest()->get();
            return view('member.profile.author', compact('reviewArticles', 'publishArticles'));
        }

        if ($memberTypeId == 2) { // Editor
            \Log::info('Editor section - starting queries');
            try {
                $unaccepted = EditorAccept::with(['member'])->distinct('article_id')->whereNull('member_id')->pluck('article_id')->toArray();
                \Log::info('Editor unaccepted query done', ['count' => count($unaccepted)]);
                $newArticles = Article::with(['member', 'article_category', 'journal_category', 'sub_articles'])->whereIn('id', $unaccepted)->latest()->get();
                \Log::info('Editor newArticles query done', ['count' => $newArticles->count()]);
                $accept = EditorAccept::with(['member'])->distinct('article_id')->where('member_id', $user->id)->pluck('article_id')->toArray();
                \Log::info('Editor accept query done', ['count' => count($accept)]);
                $articles = Article::with(['member', 'article_category', 'journal_category', 'sub_articles'])->whereIn('id', $accept)->latest()->get();
                \Log::info('Editor articles query done', ['count' => $articles->count()]);
            } catch (\Exception $e) {
                \Log::error('Editor query error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                throw $e;
            }
            $processed = $articles->where('article_status', 3);
            $processing = $articles->where('article_status', 2);
            \Log::info('About to render view', [
                'newArticles_count' => $newArticles->count(),
                'processed_count' => $processed->count(),
                'processing_count' => $processing->count(),
                'newArticles_type' => gettype($newArticles),
                'processed_type' => gettype($processed),
                'processing_type' => gettype($processing),
            ]);
            return view('member.profile.editor', compact('newArticles', 'processed', 'processing'));
            // return response()->json(['status' => 'success', 'type' => 'editor']);
        }

        if ($memberTypeId == 3 || $memberTypeId == 6) { // Reviewer or Reviewer Final
            \Log::info('Reviewer section - starting queries', ['memberTypeId' => $memberTypeId]);
            try {
            try {
                if ($memberTypeId == 3) {
                    \Log::info('Processing reviewer type 3');
                    $unaccepted = ReviewerAccept::with(['member'])->distinct('article_id')
                        ->where(function ($query) use ($user) {
                            $query->whereNull('assigned_id');
                            $query->orWhere('assigned_id', $user->id);
                        })->whereNull('member_id')
                        ->pluck('article_id')->toArray();
                    \Log::info('Reviewer unaccepted query done', ['count' => count($unaccepted)]);

                    $accept = ReviewerAccept::with(['member'])->distinct('article_id')->where('member_id', $user->id)->pluck('article_id')->toArray();
                    \Log::info('Reviewer accept query done', ['count' => count($accept)]);
                } else {
                    \Log::info('Processing reviewer type 6 (final)');
                    $unaccepted = ReviewerAcceptFinal::with(['member'])->distinct('article_id')->whereNull('member_id')->pluck('article_id')->toArray();
                    \Log::info('ReviewerFinal unaccepted query done', ['count' => count($unaccepted)]);

                    $accept = ReviewerAcceptFinal::with(['member'])->distinct('article_id')->where('member_id', $user->id)->pluck('article_id')->toArray();
                    \Log::info('ReviewerFinal accept query done', ['count' => count($accept)]);
                }

                $newArticles = Article::with('member')->whereIn('id', $unaccepted)->latest()->get();
                \Log::info('Reviewer newArticles query done', ['count' => $newArticles->count()]);
                $acceptedArticle = Article::with('member')->whereIn('id', $accept)->latest()->get();
                \Log::info('Reviewer acceptedArticle query done', ['count' => $acceptedArticle->count()]);
            } catch (\Exception $e) {
                \Log::error('Reviewer query error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                throw $e;
            }

            // try {
            //     return view('member.profile.reviewer', compact('newArticles', 'acceptedArticle'));
            // } catch (\Exception $e) {
            //     \Log::error('Reviewer view rendering error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            //     throw $e;
            // }
            return view('member.profile.reviewer', compact('newArticles', 'acceptedArticle'));
            // return response()->json(['status' => 'success', 'type' => 'reviewer']);
        }

        if ($memberTypeId == 5) { // Publisher
            \Log::info('Publisher section - starting queries');
            try {
                $unaccepted = PublisherAccept::with(['member'])->distinct('article_id')->whereNull('member_id')->pluck('article_id')->toArray();
                \Log::info('Publisher unaccepted query done', ['count' => count($unaccepted)]);

                $newArticles = Article::with('member')->whereIn('id', $unaccepted)->latest()->get();
                \Log::info('Publisher newArticles query done', ['count' => $newArticles->count()]);
                $accept = PublisherAccept::with(['member'])->distinct('article_id')->where('member_id', $user->id)->pluck('article_id')->toArray();
                \Log::info('Publisher accept query done', ['count' => count($accept)]);
                $acceptedArticle = Article::with('member')->whereIn('id', $accept)->latest()->get();
                \Log::info('Publisher acceptedArticle query done', ['count' => $acceptedArticle->count()]);
            } catch (\Exception $e) {
                \Log::error('Publisher query error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                throw $e;
            }

            return view('member.profile.publisher', compact('newArticles', 'acceptedArticle'));
            return response()->json(['status' => 'success', 'type' => 'publisher']);
            // return response()->json(['status' => 'success', 'type' => 'publisher']);
        }

        if ($memberTypeId == 4) { // Account/Researcher
            return view('member.profile.researcher');
        }

        // Default fallback
        abort(403, 'Unauthorized access to profile');
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
            'password' => $request->password
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
