<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\PropertyType;

class SelectPropertyType extends Component
{
    public function __construct()
    {
      
    }

    public function render(): View|Closure|string
    {
        return view('components.select-property-type');
    }
}
