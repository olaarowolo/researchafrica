<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ArticleCategory;

class Category extends Component
{
    public $categories;
    public $category;
    public $sub_categories;

    public function mount()
    {
        $this->categories = ArticleCategory::where(['parent_id' => null
        ])->pluck('category_name', 'id');
        $this->sub_categories = collect();

    }
    public function render()
    {
        return view('livewire.category');
    }

    public function updatedCategory($value)
    {
        $this->sub_categories = ArticleCategory::where(['parent_id' => $value
        ])->pluck('category_name', 'id');
    }
}
