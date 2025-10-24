<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Table extends Component
{
    public $data;
    public $columns;

    public function __construct($data, $columns = [])
    {
        $this->data = $data;
        $this->columns = $columns;
    }

    public function render()
    {
        return view('components.table');
    }
}
