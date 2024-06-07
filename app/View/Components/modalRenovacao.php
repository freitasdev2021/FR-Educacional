<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class modalRenovacao extends Component
{
    //public $IDAluno;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //$this->IDAluno = $IDAluno;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal-renovacao');
    }
}
