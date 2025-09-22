<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ArticleCreate extends Component
{
    public $categories;
    public $journal;
    public $keywords;
    /**
     * Create a new component instance.
     */
    public function __construct($categories, $keywords, $journal = array())
    {
        $this->categories = $categories;
        $this->journal = $journal;
        $this->keywords = $keywords;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.article-create');
    }
}
