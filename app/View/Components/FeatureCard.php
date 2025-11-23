<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FeatureCard extends Component
{
    public string $title;
    public string $description;
    public string $image;
    public string $gradientFrom;
    public string $gradientTo;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title,
        string $description,
        string $image,
        string $gradientFrom = 'gray-400',
        string $gradientTo = 'gray-600'
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->gradientFrom = $gradientFrom;
        $this->gradientTo = $gradientTo;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.feature-card');
    }
}
