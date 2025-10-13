<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormInput extends Component
{
    public $name;
    public $label;
    public $type;
    public $value;
    public $placeholder;
    public $readonly;
    public $icon;

    public function __construct($name, $label, $type = 'text', $value = null, $placeholder = null, $readonly = false, $icon = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->readonly = $readonly;
        $this->icon = $icon;
    }

    public function render()
    {
        return view('components.form-input');
    }
}
