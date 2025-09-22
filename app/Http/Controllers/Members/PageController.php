<?php

namespace App\Http\Controllers\Members;

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\FaqCategory;
use App\Models\ViewArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\ArticleCategory;
use App\Models\PurchasedArticle;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function home()
    {
        $categories = \App\Models\ArticleCategory::notParent()->get();
        $articles = \App\Models\Article::where('article_status', 3)->latest()->paginate(8);
        return view('welcome', compact('categories', 'articles'));
    }
    // About
    public function about()
    {
        return view('member.pages.about');
    }

    // Faq
    public function faq()
    {
        $faqCategories = FaqCategory::all();
        return view('member.pages.faq', compact('faqCategories'));
    }

    // contact
    public function contact()
    {
        return view('member.pages.contact');
    }

    public function search(Request $request)
    {

        $categories = ArticleCategory::notParent()->get();

        if ((request()->has('category') && request('category')) || (request('type') === 'journal')) {

            if ((request()->has('category') && request('category'))) {

                $get_category = str_replace("_", " ", request('category'));
                $category = ArticleCategory::where([
                    'category_name' => $get_category
                ])->first();


                if (is_null($category)) {
                    return back()->with('error', 'Category does not exist');
                }

                $get_journal = ArticleCategory::parent($category->id);
            } elseif ((request('type') === 'journal' && request('q'))) {
                $q = request('q');
                $cat = request('with_category');

                $categories = ArticleCategory::withCount('categories')->notParent()->get();
                $get_journal = ArticleCategory::where('parent_id', '!=', null)
                    ->where('category_name', 'like', '%' . request('q') . '%')
                    ->when(request()->has('with_category') && request('with_category'), function ($query) use ($cat) {
                        return $query->where('parent_id', $cat);
                    })
                    ->when(!request()->has('with_category') && request('with_category') == null, function ($query) {
                        return $query->where('parent_id', '!=', null);
                    });
            } else {
                return back()->with('error', 'something went wrong');
            }

            $count = $get_journal->count();

            $journals = $get_journal->paginate(3)->withQueryString();


            return view('search', compact('categories', 'journals', 'count'));
        } elseif (request()->has('q') && request('q')) {

            $q = request('q');

            // dd($q);

            if (is_null($q) || $q == '') {
                return to_route('member.search')->withErrors([
                    'message' => [
                        'Category does not exist'
                    ]
                ]);
            }

            $categories = ArticleCategory::withCount([
                'article' => function ($query) use ($q) {
                    return $query->where('article_status', 3)->where('title', 'like', '%' . $q . '%');
                }
            ])->notParent()->get();

            $get_articles = Article::where('article_status', 3)
                ->where(
                    function ($query) use ($q) {
                        $query->where('title', 'like', '%' . $q . '%')
                            ->orWhere('author_name', 'like', '% ' .  $q . '%')
                            ->orWhere('other_authors', 'like', '% ' .  $q . '%')
                            ->orWhere('corresponding_authors', 'like', '% ' .  $q . '%');
                    }
                )
                ->when(request('with_category') != '' || request()->has('with_category'), function ($query) {
                    return $query->where('article_category_id', request('with_category'));
                });
            $count = $get_articles->count();

            $randomArticle = $get_articles->inRandomOrder()->paginate(3)->withQueryString();

            $articles = $get_articles->latest()->paginate(3)->withQueryString();


            return view('search', compact('categories', 'articles', 'count', 'randomArticle'));
        }

        return to_route('home');
    }

    public function advanceSearchView(Request $request)
    {
        $articles = collect();
        $count = null;
        $categories = ArticleCategory::notParent()->get();
        $search = false;


        if (request()->has('content') && request('content')) {
            $search = true;

            $content = request('content');
            $from_date = request('from_date');
            $to_date = request('to_date');
            $access = request('access');
            $category = request('with_category');


            $categories = ArticleCategory::withCount([
                'article' => function ($query) use ($content, $from_date, $to_date, $access, $category) {
                    return $query->where('article_status', 3)
                        ->where(
                            function ($query) use ($content) {
                                $query->where('title', 'like', '%' . $content . '%')
                                    ->orWhere('author_name', 'like', '% ' .  $content . '%')
                                    ->orWhere('other_authors', 'like', '% ' .  $content . '%')
                                    ->orWhere('corresponding_authors', 'like', '% ' .  $content . '%');
                            }
                        )
                        ->when($from_date != '' || $from_date != null, function ($query) use ($from_date, $to_date) {
                            return $query->whereYear('updated_at', '>=', $from_date)->whereYear('updated_at', '<=', ($to_date ?? now()->format('Y')));
                        })
                        ->when($category != '' || $category != null, function ($query) use ($category) {
                            return $query->where('article_category_id', $category);
                        })
                        ->when(request()->has('access') && request('access') != 'on', function ($query) use ($access) {
                            return $query->where('access_type', $access);
                        });
                }
            ])->notParent()->get();


            if (is_null($content) || $content == '') {
                return back()->with('error', 'Content does not exist');
            }

            $get_articles = Article::where('article_status', 3)
                ->where(
                    function ($query) use ($content) {
                        $query->where('title', 'like', '%' . $content . '%')
                            ->orWhere('author_name', 'like', '% ' .  $content . '%')
                            ->orWhere('other_authors', 'like', '% ' .  $content . '%')
                            ->orWhere('corresponding_authors', 'like', '% ' .  $content . '%');
                    }
                )
                ->when($from_date != '' || $from_date != null, function ($query) use ($from_date, $to_date) {
                    return $query->whereYear('updated_at', '>=', $from_date)->whereYear('updated_at', '<=', ($to_date ?? now()->format('Y')));
                })
                ->when($category != '' || $category != null, function ($query) use ($category) {
                    return $query->where('article_category_id', $category);
                })
                ->when(request()->has('access') && request('access') != 'on', function ($query) use ($access) {
                    return $query->where('access_type', $access);
                });
            $count = $get_articles->count();

            $articles = $get_articles->paginate(3)->withQueryString();

            $articles->load('article_category');



            return view('advance_search', compact('content', 'articles', 'count', 'search', 'categories'));
        }

        return view('advance_search', compact('categories', 'articles', 'count', 'search'));
    }


    public function viewArticle(Request $request, Article $article)
    {
        $purchased = false;
        $bookmark = false;
        if (auth('member')->user()) {
            $purchased = PurchasedArticle::where('article_id', $article->id)->where('member_id', auth('member')->id())->exists();

            $bookmark = Bookmark::where('article_id', $article->id)->where('member_id', auth('member')->id())->exists();
        }

        if ($article->views) {
            $article->views->increment('view', 1);
        } else {
            ViewArticle::create([
                'article_id' => $article->id,
                'view' => 1,
            ]);
        }

        return view('article', compact('article', 'purchased', 'bookmark'));
    }

    public function catSub(Request $request, $cat, $sub, $journal)
    {
        $sort_order = $request->has('sort') ? $request->sort : 'latest';
        $articles = Article::where([
            'article_category_id' => $cat,
            'article_sub_category_id' => $sub,
        ])->publish()
            ->when($sort_order == 'open_access', function ($query) {
                return $query->where('access_type', 1);
            })
            ->when($sort_order == 'latest', function ($query) {
                return $query->latest('publish_date');
            });

        if ($sort_order == 'most_read') {
            $articles = $articles->join('view_articles', 'view_articles.article_id', '=', 'articles.id')
                ->select('articles.*')
                ->orderByDesc('view_articles.view');
        }

        $articles = $articles->paginate(5)->withQueryString();

        \Illuminate\Support\Str::title(str_replace("_", " ", $journal));
        $categories = ArticleCategory::notParent()->get();
        $sub_cat = ArticleCategory::find($sub);

        $count = $articles->count();

        return view('category_search', compact('articles', 'journal', 'categories', 'count', 'cat', 'sub_cat', 'sub'));
    }
}
