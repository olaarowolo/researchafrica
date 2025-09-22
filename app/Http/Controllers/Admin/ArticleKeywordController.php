<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ArticleKeyword;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class ArticleKeywordController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('article_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $articleKeywords = ArticleKeyword::all();

        return view('admin.articleKeywords.index', compact('articleKeywords'));
    }

    public function create()
    {
        abort_if(Gate::denies('article_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.articleKeywords.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max: 20'
            ],
            'status' => [
               'required',
            ],
        ]);

        $ArticleKeyword = ArticleKeyword::create($validated);

        return redirect()->route('admin.article-keywords.index');
    }

    public function edit(ArticleKeyword $articleKeyword)
    {
        abort_if(Gate::denies('article_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.articleKeywords.edit', compact('articleKeyword'));
    }

    public function update(Request $request, ArticleKeyword $articleKeyword)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max: 20'
            ],
            'status' => [
               'required',
            ],
        ]);
        $articleKeyword->update($validated);
        // $articleKeyword->update($request->all());

        return redirect()->route('admin.article-keywords.index');
    }

    public function show(ArticleKeyword $articleKeyword)
    {
        abort_if(Gate::denies('article_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.articleKeywords.show', compact('articleKeyword'));
    }

    public function destroy(ArticleKeyword $articleKeyword)
    {
        abort_if(Gate::denies('article_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $articleKeyword->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        $articleKeywords = ArticleKeyword::find(request('ids'));

        foreach ($articleKeywords as $articleKeyword) {
            $articleKeyword->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
