<?php

namespace App\Http\Controllers\Traits;

use App\Models\ArticleKeyword;

trait KeywordTraits
{
    public function keywords($keywords): array
    {
        // dd(count($keywords));
        for ($i = 0; $i < count($keywords); $i++) {
            # code...
            $check = ArticleKeyword::where('title', $keywords[$i])->first();
            if (is_null($check)) {
                $keyword = new ArticleKeyword();
                $keyword->title = $keywords[$i];
                $keyword->status = 'Active';
                $keyword->save();
            }
        }

        $getKeywords = ArticleKeyword::whereIn('title', $keywords)->pluck('id')->toArray();

        return $getKeywords;
    }
}
