<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Invoice extends Component
{
    public $transaction;

    public function __construct($transaction = null)
    {
        $this->transaction = $transaction;
    }

    public function render()
    {
        return view('components.invoice');
    }
}
