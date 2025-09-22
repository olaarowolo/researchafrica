<?php

namespace App\Http\Controllers\Members;

use App\Models\ArticleKeyword;
use App\Models\EditorAccept;
use App\Models\SubArticle;
use App\Models\Member;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\KeywordTraits;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\MemberTypeTrait;
use App\Http\Requests\MassDestroyArticleRequest;
use App\Http\Controllers\Traits\MailArticleTrait;
use App\Http\Requests\Member\StoreArticleRequest;
use App\Http\Requests\Member\UpdateArticleRequest;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\User;

class ArticleController extends Controller
{
    use MediaUploadingTrait;
    use MemberTypeTrait;
    use MailArticleTrait;
    use KeywordTraits;


    public function index()
    {
        return back();
        // abort_unless($this->author(), Response::HTTP_UNAUTHORIZED);
        // $articles = auth('member')->user()->memberArticles;
        // $articles->load(['member', 'article_category']);

        // // dd($articles);


        // return view('member.articles.index', compact('articles'));
    }

    public function create()
    {
        abort_unless($this->author(), Response::HTTP_UNAUTHORIZED);

        $categories = ArticleCategory::notParent()->pluck('category_name', 'id');
        $keywords = ArticleKeyword::pluck('title', 'id');
        return view('member.articles.create', compact('keywords', 'categories'));
    }

    public function store(StoreArticleRequest $request)
    {
        abort_unless($this->author(), Response::HTTP_UNAUTHORIZED);

        $input = $request->validated();
        $input['member_id'] = auth('member')->id();

        $articleKeywords = $request->input('articleKeywords', []);
        $keywords = $this->keywords($articleKeywords);

        if ($request->access_type == 1) {
            $input['amount'] = null;
        }


        $article = Article::create($input);
        $article->article_keywords()->sync($keywords);

        $input['article_id'] = $article->id;
        $input['status'] = 1;


        $sub = SubArticle::create($input);

        $paper = $request->file('upload_paper');

        if ($paper) {
            $paper = $this->manualStoreMedia($paper)['name'];
            $sub->addMedia(storage_path('tmp/uploads/' . basename($paper)))->toMediaCollection('upload_paper');
        }

        $full_name = auth('member')->user()->first_name . ' ' . auth('member')->user()->last_name;
        $this->articleMail($full_name);

        $this->allEditor($article);

        EditorAccept::create([
            'article_id' => $article->id,
            // 'assigned_id' => ''
        ]);

        return redirect()->route('member.profile')->with('success', 'Article Uploaded Successful, please wait for review');
    }

    public function edit(Article $article)
    {
        abort_unless($this->author(), Response::HTTP_UNAUTHORIZED);

        if ($article->article_status == 2) {
            return back()->with('error', 'You are not authorized to edit this article. Article is under review');
        }

        $categories = ArticleCategory::notParent()->pluck('category_name', 'id');
        $keywords = ArticleKeyword::whereNotIn('id', $article->article_keywords->pluck('id')->toArray())->pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $article->load('member', 'article_category');
        $sub = $article->last;

        return view('member.articles.edit', compact('article', 'categories', 'sub', 'keywords'));
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        abort_unless($this->author(), Response::HTTP_UNAUTHORIZED);

        if ($article->article_status == 2) {
            return back()->with('error', 'You are not authorized to edit this article. Article is under review');
        }

        $s = $article->last;

        $input = $request->all();
        // $input['member_id'] = auth('member')->id();
        $input['status'] = 2;

        $article->update($input);
        if ($keywords = $request->input('article_keyword_id', [])) {
            $article->article_keywords()->attach($keywords);
        }

        $s->update($input);

        $paper = $request->file('upload_paper');

        if ($paper) {
            $s->upload_paper->delete();
            $paper = $this->manualStoreMedia($paper)['name'];
            $s->addMedia(storage_path('tmp/uploads/' . basename($paper)))->toMediaCollection('upload_paper');
        }

        return redirect()->route('member.profile')->with('success', 'Article Updated Successful, please wait for review');
    }

    public function show(Article $article)
    {
        abort_unless($article->member_id == auth('member')->id() || $this->editor() || $this->reviewer() || $this->reviewerFinal() || $this->publisher(), Response::HTTP_UNAUTHORIZED);

        $article->load('member', 'article_category', 'comments');

        $reviewer1 = Member::where('member_type_id', 3)->get();
        $reviewer2 = Member::where('member_type_id', 6)->get();
        $publishers = Member::where('member_type_id', 5)->get();

        return view('member.articles.show', compact('article', 'reviewer1', 'reviewer2', 'publishers'));
    }

    public function destroy(Article $article)
    {
        abort_unless($this->author(), Response::HTTP_UNAUTHORIZED);

        // if ($article->article_status == 2){
        //     return back()->with('error', 'You are not authorized to Delete this article. Article is under review');
        // }

        $article->delete();

        return back()->with('success', 'Deleted Successfully');
    }

    public function massDestroy(MassDestroyArticleRequest $request)
    {
        abort_unless($this->author(), Response::HTTP_UNAUTHORIZED);

        $articles = Article::find(request('ids'));

        foreach ($articles as $article) {
            $article->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
    public function underReview()
    {
        abort_unless($this->author(), Response::HTTP_UNAUTHORIZED);

        $articles = auth('member')->user()->under_review->get();
        $articles->load(['member', 'article_category', 'media']);

        return view('member.articles.under-review', compact('articles'));
    }

    public function publishArticle(Article $article)
    {
        if ($article->access_type == 2 && ($article->amount == 0 || $article->amount == null)) {
            return back()->withErrors([
                'message' => [
                    'Amount Can\'t Be Empty, Please Set Amount'
                ]
            ]);
        }

        $article->last->update(['status' => 10]);
        $article->update(['article_status' => 3, 'published_online' => now()]);

        $this->publishMail($article);

        return back()->with('success', 'Article published successfully');
    }
}
