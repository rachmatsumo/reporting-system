<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuService
{
    public function getMenu()
    {
        $user = Auth::user();

        return Menu::orderBy('order')->get()->filter(function ($menu) use ($user) {
            if ($menu->permission && !$user->can($menu->permission)) {
                return false;
            }
            return true;
        })->groupBy('parent_id');
    }
}

