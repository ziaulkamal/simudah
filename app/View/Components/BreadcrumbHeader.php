<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Closure;

class BreadcrumbHeader extends Component
{
    public $title;
    public $submenu;
    public $routeName;
    public $routeLabel;
    public $menuItems;

    public function __construct(
        $title = null,
        $submenu = null,
        $routeName = 'dashboard',
        $routeLabel = 'Dashboard',
        $menuItems = []
    ) {
        $this->title = $title;
        $this->submenu = $submenu;
        $this->routeName = $routeName;
        $this->routeLabel = $routeLabel;
        $this->menuItems = $menuItems;
    }

    public function render(): View|Closure|string
    {
        return view('components.breadcrumb-header');
    }
}
