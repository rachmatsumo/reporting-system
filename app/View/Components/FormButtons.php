<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormButtons extends Component
{
    public string $routePrefix;
    public string $mode;

    public function __construct(string $routePrefix, string $mode = 'create')
    {
        $this->routePrefix = $routePrefix;
        $this->mode = $mode;
    }

    public function render()
    {
        return view('components.form-buttons');
    }
}
