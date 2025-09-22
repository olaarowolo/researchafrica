<?php

namespace App\Services;

use App\Models\Article;
use App\Models\SubArticle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ArticleService
{

    public function create(array $data): ?Article
    {
        $article = Article::create($data);
        return $article;
    }

    public function update(string $article_id, array $data): bool
    {
        $article = Article::where('id', $article_id)->update($data);
        return $article;
    }

    public function updateSubArticle($article_id, $stage): void
    {
        $sub_article = SubArticle::where('article_id', $article_id)->last();
        $sub_article->update(['status' => $stage]);
    }
}
