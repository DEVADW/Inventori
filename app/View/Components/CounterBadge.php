<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CounterBadge extends Component
{
    /**
     * Create a new component instance.
     */

    public string $title;
    public string $counter;
    public string $bgColor;

    public function __construct(string $title, string $counter, string $bgColor)
    {
        //

        $this->title = $title;
        $this->counter = $counter;
        $this->bgColor = $bgColor;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.counter-badge');
    }
}
