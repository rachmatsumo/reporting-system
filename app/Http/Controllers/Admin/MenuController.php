<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Menu;
use App\Models\Permission;

class MenuController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('menu.view');
        $menus = Menu::whereNull('parent_id')
                ->orderBy('order')
                ->with(['children' => function ($q) {
                    $q->orderBy('order')
                    ->with(['children' => function ($q2) {
                        $q2->orderBy('order');
                    }]);
                }])
                ->get();

        return view('admin.menu.index', compact('menus'));
    }

    public function getMenuTree($parentId = null)
    {
        return Menu::where('parent_id', $parentId)
            ->orderBy('title') // urut sesuai abjad
            ->get()
            ->map(function ($menu) {
                $menu->children = $this->getMenuTree($menu->id);
                return $menu;
            });
    }

    public function create()
    {
        $this->authorize('menu.create');
        $parents = Menu::pluck('title','id');
        return view('admin.menu.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $this->authorize('menu.create');

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'route' => 'nullable|string', 
            'icon'  => 'nullable|string',
            'parent_id' => 'nullable|exists:menus,id',
            'permission' => 'nullable|exists:permissions,name',
            'order' => 'nullable|integer',
        ]);

        Menu::create($data);

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit(Menu $menu)
    {
        $this->authorize('menu.edit');
        $parents = Menu::where('id','!=',$menu->id)->pluck('title','id');
        return view('admin.menu.edit', compact('menu','parents'));
    }

    public function update(Request $request, Menu $menu)
    {
        $this->authorize('menu.edit');

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'route' => 'nullable|string', 
            'icon'  => 'nullable|string',
            'parent_id' => 'nullable|exists:menus,id',
            'permission' => 'nullable|exists:permissions,name',
            'order' => 'nullable|integer',
        ]);

        $menu->update($data);

        return redirect()->route('menus.index')->with('success', 'Menu berhasil diupdate');
    }

    public function destroy(Menu $menu)
    {
        $this->authorize('menu.delete');
        $menu->delete();

        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus');
    }

}
