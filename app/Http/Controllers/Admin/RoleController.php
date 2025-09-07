<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Menu;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('role.view');
        $roles = Role::with('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('role.create');
        $permissions = Permission::all();
        $menus = Menu::orderBy('order', 'ASC')->get(); 
        $role = [];
        return view('admin.roles.create', compact('permissions', 'menus', 'role'));
    }

    public function store(Request $request)
    {
        $this->authorize('role.create');
        $data = $request->validate(['name' => 'required|string|max:255|unique:roles']);

        $role = Role::create($data);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan');
    }

    public function edit(Role $role)
    {
        $this->authorize('role.edit');
        $permissions = Permission::all();
        $menus = Menu::orderBy('order', 'ASC')->get(); 
        return view('admin.roles.edit', compact('role','permissions', 'menus'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('role.edit');
        $data = $request->validate(['name' => 'required|string|max:255|unique:roles,name,'.$role->id]);

        $role->update($data);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', 'Role berhasil diupdate');
    }

    public function destroy(Role $role)
    {
        $this->authorize('role.delete');
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus');
    }
}
