<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'nrp' => ['required'],
            'password' => ['required'],
        ]);

        // request untuk masuk pakai form 
        $credentials = $request->only('nrp', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard')->with('login_success', 'Halo '. Auth::user()->nama);
        }

        return back()->withErrors([
            'nrp' => 'NRP atau Password salah',
        ])->onlyInput('nrp');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
