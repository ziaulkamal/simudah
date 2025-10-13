<?php

namespace App\View\Components;

use Illuminate\View\Component;

class WilayahDropdown extends Component
{
    public $provinceId;
    public $regencyId;
    public $districtId;
    public $villageId;

    /**
     * Create a new component instance.
     */
    public function __construct($provinceId = null, $regencyId = null, $districtId = null, $villageId = null)
    {
        $this->provinceId = $provinceId;
        $this->regencyId = $regencyId;
        $this->districtId = $districtId;
        $this->villageId = $villageId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.wilayah-dropdown');
    }
}
