<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Member;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ArticleKeyword;
use App\Models\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\KeywordTraits;
use App\Http\Controllers\Traits\MailArticleTrait;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\MassDestroyArticleRequest;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\SubArticle;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ArticleController extends Controller
{
    use MediaUploadingTrait, KeywordTraits, MailArticleTrait;

    public function index()
    {
        abort_if(Gate::denies('article_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $articles = Article::with(['member', 'article_category', 'sub_articles'])->latest()->get();

        $members = Member::get();

        $article_categories = ArticleCategory::notParent()->get();


        return view('admin.articles.index', compact('article_categories', 'articles', 'members'));
    }

    public function edit(Article $article)
    {
        abort_if(Gate::denies('article_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $members = Member::get(['email_address', 'id', 'title', 'first_name', 'last_name', 'middle_name']);

        $article_categories = ArticleCategory::notParent()->pluck('category_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $article_keywords = ArticleKeyword::whereNotIn('id', $article->article_keywords->pluck('id')->toArray())->pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $article->load('member', 'article_category');

        return view('admin.articles.edit', compact('article', 'article_categories', 'members', 'article_keywords'));
    }

    public function create()
    {
        abort_if(Gate::denies('article_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $members = Member::get(['email_address', 'id', 'title', 'first_name', 'last_name', 'middle_name']);

        $article_categories = ArticleCategory::notParent()->pluck('category_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $article_keywords = ArticleKeyword::all()->pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.articles.create', compact('article_categories', 'members', 'article_keywords'));
    }

    public function store(StoreArticleRequest $request)
    {
        abort_if(Gate::denies('article_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $input = $request->validated();

        $member = Member::where('id', $request->member_id)->first();

        // upload and update
        if ($request->has('pdf_doc')) {
            $file_name = uniqid() . '_' . Str::of($request->title . ' ' . $member->title . ' ' . $member->first_name)->slug('_');
            $path = $this->saveArticlePdf($request->file('pdf_doc'), $file_name);
            $input['file_path'] = $path;
        }

        if ($request->access_type == 1) {
            $input['amount'] = null;
        }

        $article = Article::create($input);
        if($request->article_status == 3){
            $article->published_online = now();
            $article->save();
        }

        $articleKeywords = $request->input('keywords', []);
        $keywords = $this->keywords($articleKeywords);
        $article->article_keywords()->sync($keywords);

        $input['article_id'] = $article->id;
        $input['status'] = 1;


        $sub_article = SubArticle::create($input);

        $paper = $request->file('upload_paper');


        if ($paper) {
            $paper = $this->manualStoreMedia($paper)['name'];
            $sub_article->addMedia(storage_path('tmp/uploads/' . basename($paper)))->toMediaCollection('upload_paper');
        }

        $article_mail = $this->articleMail($first_name = $member->first_name);

        $mailing = $this->allEditor($article);

        return redirect()->route('admin.articles.index')->with('success', 'Article Created Successfully');
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        // if ($article->article_status == 2) {
        //     return back()->with('error', 'You are not authorized to edit this article. Article is under review');
        // }

        $s = $article->last;
        $input = $request->except(['pdf_doc']);

        if ($request->has('pdf_doc')) {
            // upload and update
            $this->deleteExistingFile($s->file_path ?? '', $s->storage_disk);
            $file_name = uniqid() . '_' . Str::of($article->title . ' ' . $article->member->title . ' ' . $article->member->first_name)->slug('_');
            $path = $this->saveArticlePdf($request->file('pdf_doc'), $file_name);
            $input['file_path'] = $path;
        }

        // $input['member_id'] = auth('member')->id();
        $input['status'] = 2;

        $article->update($input);
        if($request->article_status == 3){
            $article->published_online = now();
            $article->save();
        }

        $articleKeywords = $request->input('keywords', []);
        $keywords = $this->keywords($articleKeywords);
        $article->article_keywords()->sync($keywords);

        $s->update($input);

        // $paper = $request->has('upload_paper');

        if ($request->has('upload_paper')) {
            $s->upload_paper?->delete();
            $paper = $this->manualStoreMedia($request->file('upload_paper'))['name'];
            $s->addMedia(storage_path('tmp/uploads/' . basename($paper)))->toMediaCollection('upload_paper');
        }

        return redirect()->route('admin.articles.index')->with('success', 'Article Updated Successfully');
    }

    public function show(Article $article)
    {
        abort_if(Gate::denies('article_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $article->load('member', 'article_category', 'comments');

        return view('admin.articles.show', compact('article'));
    }

    public function destroy(Article $article)
    {
        abort_if(Gate::denies('article_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $article->delete();

        return back();
    }

    public function massDestroy(MassDestroyArticleRequest $request)
    {
        $articles = Article::find(request('ids'));

        foreach ($articles as $article) {
            $article->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('article_create') && Gate::denies('article_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Article();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
