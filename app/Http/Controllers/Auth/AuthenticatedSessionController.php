<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        // return view('auth.login');
        return view('session.login-session');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store()
    {
        $attributes = request()->validate([
            'email'=>'required|email',
            'password'=>'required' 
        ]);

        $remember = request()->has('remember'); 

        $user = User::where('email', $attributes['email'])->first();

        if (!$user || !\Hash::check($attributes['password'], $user->password)) {
            return back()->withErrors(['email' => 'Email or password invalid.'])->onlyInput('email');
        }
 
        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Your account is not active.'])->onlyInput('email');
        }


        if(Auth::attempt($attributes, $remember))
        {
            session()->regenerate();
            return redirect('dashboard')->with(['success'=>'You are logged in.']);
        }
        else{

            return back()->withErrors(['email'=>'Email or password invalid.']);
        }
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
