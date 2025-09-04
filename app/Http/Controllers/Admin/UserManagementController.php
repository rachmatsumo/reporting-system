<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;

class UserManagementController
{
    public function index()
    {
        $users = User::all();
        return view('admin.user.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));    
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:15',
            'role' => 'required|string|max:15',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only('name', 'email', 'phone', 'role'));

        return redirect()->route('user-management.index')->with('success', 'User updated successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:15', 
            'role' => 'required|string|max:15', 
        ]);

        $password = 'password123'; 

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => Hash::make($password),  
        ]);

        return redirect()->route('user-management.index')
            ->with('success', 'User created successfully.');
    }

    public function destroy(User $user)
    {    
        $user->delete();
        return redirect()->route('user-management.index')
                        ->with('success', 'User berhasil dihapus');
    }

}
