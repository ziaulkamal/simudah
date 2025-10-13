<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PersonTable extends Component
{
    public $peoples;

    public function __construct($peoples)
    {
        $this->peoples = $peoples;
    }

    public function render()
    {
        return view('components.person-table');
    }
}
