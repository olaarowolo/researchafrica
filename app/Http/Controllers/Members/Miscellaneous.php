<?php

namespace App\Http\Controllers\Members;

use App\Models\Article;
use App\Models\EditorAccept;
use App\Models\ReviewerAccept;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MemberTypeTrait;
use App\Http\Controllers\Traits\MailArticleTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\PublisherAccept;
use App\Models\ReviewerAcceptFinal;
use App\Services\ArticleService;

class Miscellaneous extends Controller
{
    use MemberTypeTrait, MailArticleTrait, MediaUploadingTrait;
    public function EditorAccept(Request $request, Article $article)
    {
        $accept = EditorAccept::where('article_id', $article->id)->latest()->first();

        if ($accept) {
            $accept->update([
                'member_id' => auth('member')->id(),
            ]);

            $article->update([
                'article_status' => 2
            ]);

            // $status = (int) $article->last->status + 1;
            $status =  2;
            $article->last->update(['status' => $status]);

            $this->acceptMail($article);

            return to_route('member.articles.show', $article->id)->with('success', 'Article accepted for review successfully');
        }

        return back()->with('error', 'No editor acceptance record found for this article.');
    }

    public function SecondEditorAccept(Request $request, Article $article)
    {
        $accept = EditorAccept::where('article_id', $article->id)->latest()->first();

        if ($accept) {
            // $accept->update([
            //     'member_id' => auth('member')->id(),
            // ]);

            // $status = (int) $article->last->status + 1;
            $status =  8;

            $article->last->update(['status' => $status]);

            $this->mailEditor($article);
            $this->acceptMail($article);

            return to_route('member.articles.show', $article->id)->with('success', 'Article accepted for review successfully');
        }

        return back()->with('error', 'No editor acceptance record found for this article.');
    }

    public function ThirdEditorAccept(Request $request, Article $article)
    {
        $accept = EditorAccept::where('article_id', $article->id)->latest()->first();

        if ($accept) {
            // $accept->update([
            //     'member_id' => auth('member')->id(),
            // ]);

            // $status = (int) $article->last->status + 1;
            $status =  12;

            $article->last->update(['status' => $status]);

            $this->mailEditor($article);
            $this->acceptMail($article);

            return to_route('member.articles.show', $article->id)->with('success', 'Article accepted for review successfully');
        }

        return back()->with('error', 'No editor acceptance record found for this article.');
    }

    public function ReviewerAccept(Request $request, Article $article)
    {
        $accept = ReviewerAccept::where('article_id', $article->id)->latest()->first();

        if ($accept) {
            $accept->update([
                'member_id' => auth('member')->id(),
            ]);

            $article->update([
                'article_status' => 2
            ]);

            // $status = (int) $article->last->status + 1;
            $status = 4;
            $article->last->update(['status' => $status]);

            $this->acceptMail($article);

            return to_route('member.articles.show', $article->id)->with('success', 'Article accepted for review successfully');
        }

        return back()->with('error', 'No reviewer acceptance record found for this article.');
    }

    public function ReviewerAcceptFinal(Request $request, Article $article)
    {
        $accept = ReviewerAcceptFinal::where('article_id', $article->id)->latest()->first();

        if ($accept) {
            $accept->update([
                'member_id' => auth('member')->id(),
                'article_status' => 2,
            ]);

            // $status = (int) $article->last->status + 1;
            $status = 6;
            $article->last->update(['status' => $status]);

            $this->acceptMail($article);

            return to_route('member.articles.show', $article->id)->with('success', 'Article accepted for review successfully');
        }

        return back()->with('error', 'No final reviewer acceptance record found for this article.');
    }

    public function PublisherAccept(Request $request, Article $article)
    {
        if ($article->publisher_accept && is_null($article->publisher_accept->member)) {
            $article->publisher_accept->update([
                'member_id' => auth('member')->id(),
            ]);

            $this->acceptMail($article);

            return to_route('member.articles.show', $article->id)->with('success', 'Article accepted for review successfully');
        }

        return back()->with('error', 'Publisher acceptance not available or already accepted.');
    }

    public function becomeAuthor(Request $request)
    {
        auth('member')->user()->update(['member_type_id' => 1]);
        return redirect()->route('home')->with('success', 'Congratulations, You are now an Author');
    }

    public function viewBookmark()
    {
        return view('member.profile.bookmark');
    }

    public function viewPublishArticle()
    {
        return view('member.profile.article-published');
    }

    // Send to publisher
    public function updateAmount(Request $request, Article $article)
    {
        $validated = $request->validate([
            'volume' => ['required'],
            'issue_no' => ['required'],
            'doi_link' => ['nullable', 'url'],
            'pdf_doc' => ['required', 'file', 'mimes:pdf'],
            'amount' => [Rule::requiredIf($article->access_type == 2), 'numeric', 'min:500'],
        ]);

        $validated = Arr::except($validated, ['pdf_doc']);

        // upload and update
        if ($request->has('pdf_doc') && $article) {
            $this->deleteExistingFile($article->file_path ?? '', $article->storage_disk);
            $file_name = uniqid() . '_' . Str::of($article->title . ' ' . $article->member->title . ' ' . $article->member->first_name)->slug('_');
            $path = $this->saveArticlePdf($request->file('pdf_doc'), $file_name);
            $validated['file_path'] = $path;
        }

        $validated["is_recommended"] = true;

        if ($article) {
            $article->update($validated);

            // $status = (int) $article->last->status + 1;
            $status = 9;
            $article->last->update(['status' => $status]);

            PublisherAccept::create([
                'article_id' => $article->id,
            ]);

            $this->allPublisher($article);
        }

        return back()->with('success', 'Article Sent to Publishers Successfully');
    }
}
