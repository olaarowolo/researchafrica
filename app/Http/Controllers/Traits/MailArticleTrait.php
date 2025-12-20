<?php

namespace App\Http\Controllers\Traits;


use App\Mail\ForwardedArticle;
use App\Models\Member;
use App\Mail\ArticleMail;
use App\Mail\CommentMail;
use App\Mail\AcceptedMail;
use App\Mail\NewArticle;
use App\Mail\PublishArticle;
use App\Models\EditorAccept;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

trait MailArticleTrait
{
    public function allEditor($article, $editor_id = "")
    {
        $editors = $editor_id ? Member::where('member_type_id', 2)->where('id', $editor_id)->get() :  Member::where('member_type_id', 2)->get();
        $sender = auth('member')->user();
        foreach ($editors as $editor) {
            try {
                if ($article->last->status == 1) {
                    Mail::to($editor->email_address)->send(new NewArticle($article, $editor));
                } else {
                    Mail::to($editor->email_address)->send(new ForwardedArticle($article, $editor, $sender));
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        };

        return true;
    }

    public function mailEditor($article)
    {
        $editor_accept = EditorAccept::where('article_id', $article->id)->latest()->first();
        $editor = Member::where('id', $editor_accept->member_id)->first();
        $sender = auth('member')->user();
        try {
            if ($article->last->status == 1) {
                Mail::to($editor->email_address)->send(new NewArticle($article, $editor));
            } else {
                Mail::to($editor->email_address)->send(new ForwardedArticle($article, $editor, $sender));
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return true;
    }

    public function allReviewer($article)
    {
        $reviewers = Member::where('member_type_id', 3)->get();
        $sender = auth('member')->user();

        foreach ($reviewers as $reviewer) {
            try {
                Mail::to($reviewer->email_address)->send(new ForwardedArticle($article, $reviewer, $sender));
            } catch (\Throwable $th) {
                throw $th;
            }
        };

        return true;
    }

    public function mailReviewer($article, $reviewer_id)
    {
        $reviewer = Member::where('id', $reviewer_id)->first();
        $sender = auth('member')->user();

        try {
            Mail::to($reviewer->email_address)->send(new ForwardedArticle($article, $reviewer, $sender));
        } catch (\Throwable $th) {
            throw $th;
        }

        return true;
    }

    public function allFinalReviewer($article)
    {
        $reviewers = Member::where('member_type_id', 6)->get();
        $sender = auth('member')->user();

        foreach ($reviewers as $reviewer) {
            try {
                Mail::to($reviewer->email_address)->send(new ForwardedArticle($article, $reviewer, $sender));
            } catch (\Throwable $th) {
                throw $th;
            }
        };

        return true;
    }


    public function mailFinalReviewer($article, $reviewer_id)
    {
        $reviewer = Member::where('id', $reviewer_id)->first();
        $sender = auth('member')->user();

        try {
            Mail::to($reviewer->email_address)->send(new ForwardedArticle($article, $reviewer, $sender));
        } catch (\Throwable $th) {
            throw $th;
        }

        return true;
    }

    public function allPublisher($article)
    {
        $publishers = Member::where('member_type_id', 5)->get();
        $sender = auth('member')->user();

        foreach ($publishers as $publisher) {
            try {
                Mail::to($publisher->email_address)->send(new ForwardedArticle($article, $publisher, $sender));
            } catch (\Throwable $th) {
                throw $th;
            }
        };

        return true;
    }


    public function articleMail($fullname, $memberEmail = null)
    {
        try {
            $email = $memberEmail ?? (auth('member')->check() ? auth('member')->user()->email_address : null);
            if ($email) {
                Mail::to($email)->send(new ArticleMail($fullname));
            }
        } catch (\Throwable $th) {
            // Log the error but don't throw - article creation shouldn't fail due to email issues
            \Log::warning('Article mail sending failed: ' . $th->getMessage());
        }

        return true;
    }

    public function commentMail($article)
    {
        try {
            Mail::to($article->member->email_address)->send(new CommentMail($article));
        } catch (\Throwable $th) {
            throw $th;
        }

        return true;
    }

    public function acceptMail($article)
    {
        $fullname = $article->member->fullname;
        $stage = $article->last->status;
        $title = $article->title;

        try {
            Mail::to($article->member->email_address)->send(new AcceptedMail($fullname, $stage, $title));
        } catch (\Throwable $th) {
            throw $th;
        }

        return true;
    }

    public function publishMail($article)
    {
        $fullname = $article->member->fullname;
        $title = $article->title;
        try {
            Mail::to($article->member->email_address)->send(new PublishArticle($fullname, $title));
        } catch (\Throwable $th) {
            throw $th;
        }

        return true;
    }
}
