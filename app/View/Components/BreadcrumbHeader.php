<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BreadcrumbHeader extends Component
{
    /**
     * Create a new component instance.
     */

    public $title;
    public $submenu;

    public function __construct($title = null, $submenu = null)
    {
        $this->title = $title;
        $this->submenu = $submenu;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumb-header');
    }
}
