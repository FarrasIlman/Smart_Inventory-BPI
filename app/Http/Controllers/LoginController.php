<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // 2. Cek ke database
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Jika benar -> Ke Dashboard
            return redirect()->intended('/dashboard');
        }

        // 3. Jika salah -> Balik ke Login dengan pesan error
        return back()->withErrors([
            'email' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}