<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectDropdown extends Component
{
    public $name;
    public $label;
    public $options;
    public $selected;
    public $placeholder;

    /**
     * Create a new component instance.
     */
    public function __construct($name, $label, $options = [], $selected = null, $placeholder = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->selected = $selected;
        $this->placeholder = $placeholder ?? '-- Pilih ' . $label . ' --';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.select-dropdown');
    }
}
