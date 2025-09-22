<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class journal extends Component
{
    public $journals;
    public $categories;
    public $count;
    /**
     * Create a new component instance.
     */
    public function __construct($journals, $categories, int $count)
    {
        $this->journals = $journals;
        $this->count = $count;
        $this->categories = $categories;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.journal');
    }
}
