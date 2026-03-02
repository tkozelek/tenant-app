<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Radio extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $label,
        public string $name,
        public array $options = [],
        public ?string $value = null,
        public bool $disabled = false
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.radio');
    }
}
