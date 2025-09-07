<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Str;

class PageHeader extends Component
{
    public string $title;
    public string $routePrefix;
    public string $mode;

    public function __construct(string $routePrefix, string $mode = 'index', string $title = '')
    {
        $this->routePrefix = $routePrefix;
        $this->mode = $mode;

        // Generate title otomatis
        $modelName = Str::headline(Str::singular($routePrefix)); // roles -> Role
        $this->title = $title ?: match($mode) {
            'index' => "Daftar $modelName",
            'create' => "Tambah $modelName",
            'edit' => "Edit $modelName",
            'show' => "Detail $modelName",
            default => $modelName,
        };
    }

    public function render()
    {
        return view('components.page-header');
    }
}
