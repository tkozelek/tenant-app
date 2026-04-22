<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SocialLinks extends Component
{
    public array $socials;

    public array $colors;

    public function __construct(array $socials = [])
    {
        $this->socials = $socials;

        $this->colors = [
            'website' => 'blue-600',
            'facebook' => 'blue-600',
            'instagram' => 'pink-600',
            'x-twitter' => 'black',
            'linkedin' => 'blue-700',
        ];
    }

    public function hasLinks(): bool
    {
        return collect($this->socials)->filter()->isNotEmpty();
    }

    public function render(): View
    {
        return view('components.social-links');
    }
}
