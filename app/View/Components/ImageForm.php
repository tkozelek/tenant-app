<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageForm extends Component
{
    public string $accept;

    public string $name;

    public function __construct(string $accept, string $name)
    {
        $this->accept = $accept;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.image-form');
    }
}
