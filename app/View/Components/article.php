<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class article extends Component
{
    public $articles;
    public $randomArticle;
    public $count;
    public $categories;

    /**
     * Create a new component instance.
     */
    public function __construct($articles, $categories, int $count, $randomArticle)
    {
        $this->articles = $articles;
        $this->randomArticle = $randomArticle;
        $this->count = $count;
        $this->categories = $categories;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.article');
    }
}
