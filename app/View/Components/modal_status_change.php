<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class modal_status_change extends Component
{
    /**
     * Create a new component instance.
     */

    public string $action;
    public function __construct(string $action, )
    {
        //
        $this->action = $action;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal-status-change');
    }
}
