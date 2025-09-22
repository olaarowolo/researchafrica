<?php

namespace App\View\Components;

use App\Models\ArticleCategory;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ArticleEdit extends Component
{
    public $article;
    public $keywords;
    public $categories;
    public $journals;
    /**
     * Create a new component instance.
     */
    public function __construct($article, $keywords, $categories)
    {
        $this->article = $article;
        $this->keywords = $keywords;
        $this->categories = $categories;
        $this->journals = ArticleCategory::parent($article->article_category_id)
                                        ->pluck('category_name', 'id')->toArray();      
                                         
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.article-edit');
    }
}
