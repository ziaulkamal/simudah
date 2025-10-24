<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $type;
    public $title;

    /**
     * Create a new component instance.
     *
     * @param string $type
     * @param string $title
     */
    public function __construct($type = 'success', $title = 'Information')
    {
        $this->type = $type;
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.modal');
    }
}
