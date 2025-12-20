<?php

namespace App\Http\Controllers\Members;

use App\Models\Article;
use App\Models\Comment;
use App\Models\SubArticle;
use App\Models\EditorAccept;
use Illuminate\Http\Request;
use App\Models\ArticleKeyword;
use App\Models\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MailArticleTrait;
use App\Http\Requests\Member\StoreArticleRequest;
use App\Http\Requests\Member\UpdateArticleRequest;
use App\Http\Controllers\Traits\MediaUploadingTrait;

class CommentController extends Controller
{
    use MailArticleTrait, MediaUploadingTrait;
    public function index(Article $article, Comment $comment)
    {
        if ($article->last->status == 2 || $article->last->status == 4){
            return back()->with('error', 'You are not authorized to edit this article. Article is under review');
        }

        $article->load(['member', 'article_category', 'sub_articles']);
        $sub_article = $article->sub_articles()->where('comment_id', $comment->id)->first();

        $journals = ArticleCategory::parent($article->article_category_id)->pluck('category_name', 'id')->toArray();
        $article_keywords = ArticleKeyword::whereNotIn('id', $article->article_keywords->pluck('id')->toArray())->pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $categories = ArticleCategory::notParent()->pluck('category_name', 'id');
        if(is_null($sub_article)){
            $sub_article = $article->last;
        }else{
            $sub_article = SubArticle::where('comment_id',$comment->id)->first();
        }

        $comment->load(['member', 'article']);
        $article_categories = ArticleCategory::pluck('category_name', 'id');
        // dd($comments);
        return view('member.comment.index', compact('comment', 'article', 'article_categories', 'sub_article', 'article_keywords', 'categories', 'journals'));
    }

    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            'message' => [
                'required'
            ],
            'correction_upload' => [
                'nullable'
            ]
        ]);

        $member_type = auth('member')->user()->member_type_id;

        if($member_type == 2){
            $status = 1;
        }else{
            $status = 3;
        }

        $correction_upload = $request->file('correction_upload');

        $input = $validated;
        $input['member_id'] = auth('member')->user()->id;
        $input['article_id'] = $article->id;
        $input['status'] = 3;

        $comment = Comment::create($input);

        if($correction_upload){
            $correction_upload = $this->manualStoreMedia($correction_upload)['name'];
            $comment->addMedia(storage_path('tmp/uploads/' . basename($correction_upload)))->toMediaCollection('correction_upload');
        }

        $article->last->update(['status' => $status]);

        $this->commentMail($article);
        return back()->with('success', 'Comment Sent successfully');
    }


    public function commentArticleUpdate(StoreArticleRequest $request, Article $article)
    {
        // dd($request->all());

        if ($article->last->status == 2 || $article->last->status == 4){
            return back()->with('error', 'You are not authorized to edit this article. Article is under review');
        }

        $input = $request->all();
        $input['article_id'] = $article->id;
        $input['article_status'] = 1;
        $input['status'] = 1;

        $article->update($input);


        $sub_article = SubArticle::create($input);

        $paper = $request->file('upload_paper');


        if ($paper) {
            $paper = $this->manualStoreMedia($paper)['name'];
            $sub_article->addMedia(storage_path('tmp/uploads/' . basename($paper)))->toMediaCollection('upload_paper');
        }

        $mailing = $this->allEditor($article);

        // Editor accept
        EditorAccept::create([
            'article_id' => $article->id,
        ]);


        return redirect()->route('member.profile')->with('success', 'Article Updated Successful, please wait for review');
    }
}
