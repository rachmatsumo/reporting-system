<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PermissionController extends Controller
{
    public function index()
    {
        $this->authorize('permission.view');
        $permissions = Permission::orderBy('name')->paginate(10);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        $this->authorize('permission.create');
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $this->authorize('permission.create');
        $data = $request->validate(['name' => 'required|string|max:255|unique:permissions']);
        Permission::create($data);

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil ditambahkan');
    }

    public function edit(Permission $permission)
    {
        $this->authorize('permission.edit');
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $this->authorize('permission.edit');
        $data = $request->validate(['name' => 'required|string|max:255|unique:permissions,name,'.$permission->id]);

        $permission->update($data);

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil diupdate');
    }

    public function destroy(Permission $permission)
    {
        $this->authorize('permission.delete');
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission berhasil dihapus');
    }
}
