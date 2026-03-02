<?php

namespace App\View\Components;

use App\Models\Tenant;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ShopCard extends Component
{
    public Tenant $shop;

    public ?bool $options;

    public function __construct(Tenant $shop, ?bool $options = false)
    {
        $this->shop = $shop;
        $this->options = $options;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tenant-card');
    }
}
