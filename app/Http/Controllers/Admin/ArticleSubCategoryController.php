<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyArticleCategoryRequest;
use App\Http\Requests\StoreArticleCategoryRequest;
use App\Http\Requests\UpdateArticleCategoryRequest;
use App\Models\ArticleCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleSubCategoryController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('article_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $articleCategories = ArticleCategory::where('parent_id', '!=' ,null)->get();

        return view('admin.articleSubCategories.index', compact('articleCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('article_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $articleCategories = ArticleCategory::where('parent_id', null)->pluck('category_name', 'id')->toArray();


        return view('admin.articleSubCategories.create', compact('articleCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cover_image' => [
                'required',
            ]
        ]);
        $articleCategory = ArticleCategory::create($request->all());

        $cover_image = $request->file('cover_image');

        if($cover_image){
            $cover_image = $this->manualStoreMedia($cover_image)['name'];

            $articleCategory->addMedia(storage_path('tmp/uploads/' . basename($cover_image)))->toMediaCollection('cover_image');
        }



        return redirect()->route('admin.article-sub-categories.index');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('article_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $articleCategory = ArticleCategory::find($id);
        $articleCategories = ArticleCategory::where('parent_id', null)->pluck('category_name', 'id')->toArray();

        return view('admin.articleSubCategories.edit', compact('articleCategory', 'articleCategories'));
    }
    public function update(Request $request,  $id)
    {
        $articleCategory = ArticleCategory::find($id);

        // dd($request->all());
        $articleCategory->update($request->all());

        $cover_image = $request->file('cover_image');

        if($cover_image){
            $cover_image = $this->manualStoreMedia($cover_image)['name'];

            $articleCategory->addMedia(storage_path('tmp/uploads/' . basename($cover_image)))->toMediaCollection('cover_image');
        }

        return redirect()->route('admin.article-sub-categories.index');
    }

    public function show($id)
    {
        abort_if(Gate::denies('article_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $articleCategory = ArticleCategory::find($id);

        return view('admin.articleSubCategories.show', compact('articleCategory'));
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('article_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $articleCategory = ArticleCategory::find($id)->delete();

        return back();
    }

    public function massDestroy(MassDestroyArticleCategoryRequest $request)
    {
        $articleCategories = ArticleCategory::find(request('ids'));

        foreach ($articleCategories as $articleCategory) {
            $articleCategory->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
