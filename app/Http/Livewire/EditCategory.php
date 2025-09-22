<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ArticleCategory;

class EditCategory extends Component
{
    // public $categories;
    public $category;
    public $categories;
    public $category_id;
    public $sub_category_id;
    public $sub_categories;

    public $row;

    public function mount($article = null, bool $row = false)
    {
        $this->categories = ArticleCategory::notParent()->pluck('category_name', 'id');
        $this->category_id = $article?->article_category_id ?? $this->categories->take(1)->keys()->first();

        $this->sub_category_id =  $article?->article_sub_category_id;

        $this->sub_categories = ArticleCategory::parent($this->category_id)->pluck('category_name', 'id');


        $this->row = $row;
    }

    public function render()
    {
        return view('livewire.edit-category');
    }

    public function updatedCategoryId($value)
    {
        $this->sub_categories = ArticleCategory::parent($value)->pluck('category_name', 'id');
    }

    public function updatedCategory($value)
    {
        $this->sub_categories = ArticleCategory::parent($value)->pluck('category_name', 'id');
    }
}
