<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;  
use Spatie\Permission\Models\Role; 
use Spatie\Permission\Models\Permission; 
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('user.view');
        
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $this->authorize('user.view');

        $user = User::with('roles')->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        $this->authorize('user.create');

        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.create', compact('roles','permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('user.create');

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email', 
            'gender'    => 'nullable|in:male,female',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'roles'     => 'nullable|array',
            'permissions'     => 'nullable|array',
        ]);

        $validated['password'] = Hash::make('password123');
        $validated['is_active'] = $request->has('is_active') ? $validated['is_active'] : false;
 
        $user = User::create($validated); 

        $user->syncRoles($request->roles ?? []);
        $user->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('users.index')
            ->with('success', 'User baru berhasil ditambahkan.');
    }


    public function edit(User $user)
    {
        $this->authorize('user.edit');
        $roles = Role::all();
        $permissions = Permission::all();
        // dd($permissions);
        return view('admin.users.edit', compact('user','roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('user.edit');
 
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'gender'  => 'nullable|in:male,female',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500', 
            'is_active' => 'nullable|boolean',
            'roles'   => 'nullable|array',
            'permissions'   => 'nullable|array',
        ]);
        $validated['is_active'] = $request->has('is_active') ? $validated['is_active'] : false;
 
        $user->update($validated);
 
        $user->syncRoles($request->roles ?? []);
        $user->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }


}
