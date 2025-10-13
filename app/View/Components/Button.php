<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $type;
    public $label;
    public $color;
    public $icon;
    public $full;
    public $disabled;
    public $href;
    public $back;
    public $class;

    public function __construct(
        $type = 'button',
        $label = 'Tombol',
        $color = 'primary',
        $icon = null,
        $full = false,
        $disabled = false,
        $href = null,
        $back = false,
        $class = null
    ) {
        $this->type = $type;
        $this->label = $label;
        $this->color = $color;
        $this->icon = $icon;
        $this->full = $full;
        $this->disabled = $disabled;
        $this->href = $href;
        $this->back = $back;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.button');
    }
}
