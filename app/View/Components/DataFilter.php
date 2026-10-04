<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataFilter extends Component
{
    /**
     * Create a new component instance.
     */

    public string $action;
    public string $placeholder;
    public string $value;
    public function __construct(string $action, string $placeholder = 'Cari data...', ?string $value = null)
    {
        //
        $this->action = $action;
        $this->placeholder = $placeholder;
        $this->value = $value ?? request('search');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.data-search');
    }
}
