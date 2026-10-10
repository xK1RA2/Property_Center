<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Role;

class SelectDealer extends Component
{
    public function __construct()
    {
        $this->Property = Property::orderBy('dealer_name')->get(); 
    }

    public function render(): View|Closure|string
    {
        return view('components.select-dealer');
    }
}
