<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Traits\MailArticleTrait;
use App\Models\Article;
use App\Models\ReviewerAccept;
use App\Models\SubArticle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MemberTypeTrait;
use App\Models\EditorAccept;
use App\Models\ReviewerAcceptFinal;

class EditorController extends Controller
{
    use MemberTypeTrait, MailArticleTrait;
    public function index()
    {
        abort_unless($this->editor(), Response::HTTP_UNAUTHORIZED);
        return view('member.editor.index');
    }

    public function sendReview(Request $request, Article $article)
    {
        // abort_unless($this->editor(), Response::HTTP_UNAUTHORIZED);

        if (is_null($article)) {
            return back()->with('error', 'Article does not exist');
        }

        // $status = (int) $article->last->status + 1;
        $status = 3;
        $article->last->update(['status' => $status]);

        if ($request->has('member_id')) {
            $this->mailReviewer($article, $request->member_id);
        } else {
            $this->allReviewer($article);
        }

        ReviewerAccept::create([
            'article_id' => $article->id,
            'assigned_id' => $request->member_id ?? null
        ]);

        return back()->with('success', 'Article Sent to Reviewer successfully');

        // return redirect()->route('member.editor.index');
    }

    public function sendFinalReview(Request $request, Article $article)
    {

        if (is_null($article)) {
            return back()->with('error', 'Article does not exist');
        }

        // $status = (int) $article->last->status + 1;
        $status = 5;

        $article->last->update(['status' => $status]);

        if ($request->has('member_id')) {
            $this->mailFinalReviewer($article, $request->member_id);
        } else {
            $this->allFinalReviewer($article);
        }

        ReviewerAcceptFinal::create([
            'article_id' => $article->id,
            'assigned_id' => $request->member_id ?? null
        ]);

        return back()->with('success', 'Article Sent to Reviewer successfully');

        // return redirect()->route('member.editor.index');
    }

    public function sendToSecondEditor(Request $request, Article $article)
    {
        if (is_null($article)) {
            return back()->with('error', 'Article does not exist');
        }

        $status = 7;
        $article->last->update(['status' => $status]);
        $this->mailEditor($article);

        return back()->with('success', 'Article Sent to Editor successfully');
    }

    public function sendToThirdEditor(Request $request, Article $article)
    {
        if (is_null($article)) {
            return back()->with('error', 'Article does not exist');
        }

        $status = 11;
        $article->last->update(['status' => $status]);
        $this->mailEditor($article);

        return back()->with('success', 'Article Sent to Editor successfully');
    }

    public function sendEditor(Request $request, Article $article)
    {
        if (is_null($article)) {
            return back()->with('error', 'Article does not exist');
        }

        // $status = (int) $article->last->status + 1;
        $status =  1;

        $article->last->update(['status' => $status]);

        $this->allEditor($article);

        EditorAccept::create([
            'article_id' => $article->id,
        ]);

        return back()->with('success', 'Article Sent to Editor successfully');
    }
}
