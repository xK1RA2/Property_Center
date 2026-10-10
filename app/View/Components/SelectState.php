<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\State;
class SelectState extends Component
{
    public function __construct()
    {
        $this->State = State::orderBy('name')->get(); 
    }

    public function render(): View|Closure|string
    {
        return view('components.select-state');
    }
}
