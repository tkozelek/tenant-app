<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class SearchableSelect extends Component
{
    public function __construct(
        public Collection $options,
        public array $selected = [],
        public string $label = '',
        public string $placeholder = 'Hladat...',
        public string $wireMethod = '',
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.searchable-select');
    }
}
