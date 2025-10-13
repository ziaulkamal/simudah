<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FilterCard extends Component
{
    public $title;
    public $action;

    /**
     * Create a new component instance.
     */
    public function __construct($title = 'Filter Pencarian', $action = null)
    {
        $this->title = $title;
        $this->action = $action ?? url()->current();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.filter-card');
    }
}
