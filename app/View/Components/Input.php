<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public function __construct(
        public string $label,
        public string $name,
        public string $type,
        public ?string $faIcon = null,
        public ?string $value = null,
        public ?bool $req = true,
        public ?string $placeholder = null,
        public ?string $autocomplete = null,
        public ?bool $disabled = false) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input');
    }
}
