<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatCard extends Component
{
    public string $title;
    public string $value;
    public ?string $icon;

    public function __construct(string $title, string $value, ?string $icon = null)
    {
        $this->title = $title;
        $this->value = $value;
        $this->icon = $icon;
    }

    public function render()
    {
        return view('components.stat-card');
    }
}
