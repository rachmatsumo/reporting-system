<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Role; 
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

    public function show()
    {
        $id = Auth()->id();
        $user = User::findOrFail($id);

        // Pastikan hanya owner atau user dengan permission khusus yang bisa mengakses
        if (Auth::id() !== $user->id && !Auth::user()->can('user.edit')) {
            abort(403);
        }

        // Hanya kirim roles jika current user boleh assign role
        $roles = Auth::user()->can('role.assign') ? Role::orderBy('name')->get() : collect();

        return view('profile.show', compact('user', 'roles'));
    }
    /**
     * Display the user's profile form.
     */
    public function edit()
    {
        $id = Auth()->id();
        $user = User::findOrFail($id);

        // Pastikan hanya owner atau user dengan permission khusus yang bisa mengakses
        if (Auth::id() !== $user->id && !Auth::user()->can('user.edit')) {
            abort(403);
        }

        // Hanya kirim roles jika current user boleh assign role
        $roles = Auth::user()->can('role.assign') ? Role::orderBy('name')->get() : collect();

        return view('profile.edit', compact('user', 'roles'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'gender'  => 'nullable|in:male,female',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');

            // Path utama
            $avatarPath = public_path('assets/uploads/avatar');
            $thumbnailPath = public_path('assets/uploads/avatar/thumbnails');

            // Pastikan folder ada
            if (!File::isDirectory($avatarPath)) {
                File::makeDirectory($avatarPath, 0777, true, true);
            }
            if (!File::isDirectory($thumbnailPath)) {
                File::makeDirectory($thumbnailPath, 0777, true, true);
            }

            // Nama file unik
            $filename = time() . '_' . uniqid() . '.' . $photoFile->getClientOriginalExtension();

            // Simpan original
            $photoFile->move($avatarPath, $filename);

            // Buat ImageManager instance
            $manager = new ImageManager(new Driver());

            // Buat thumbnail (150x150 crop)
            $thumbnailImage = $manager->read($avatarPath . '/' . $filename)
                ->cover(150, 150); // cover() menggantikan fit()
                
            $thumbnailImage->save($thumbnailPath . '/' . $filename);

            // Hapus foto lama
            if ($user->photo && File::exists(public_path($user->photo))) {
                File::delete(public_path($user->photo));
            }
            if ($user->photo && File::exists(public_path('assets/uploads/avatar/thumbnails/' . basename($user->photo)))) {
                File::delete(public_path('assets/uploads/avatar/thumbnails/' . basename($user->photo)));
            }

            // Simpan path relatif
            $user->photo = 'assets/uploads/avatar/' . $filename;
        }

        $user->save();

        return redirect()->route('profile.show', Auth::id())
                         ->with('success', 'Profile updated successfully.');
    }

    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password baru
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully.'); 
    }
}
