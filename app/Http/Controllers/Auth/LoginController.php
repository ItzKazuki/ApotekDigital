<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Kalau user sudah login di device lain
            if ($user->is_logged_in) {
                Auth::logout();
                return redirect()->route('auth.login')
                    ->with('error', 'Akun ini sedang aktif di perangkat lain. Silakan logout dulu.');
            }

            // Tandai sebagai sedang login
            $user->is_logged_in = true;
            $user->save();

            // Arahkan sesuai role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('kasir.index');
        }

        // Jika login gagal
        return back()->with('error', 'Email atau password salah, silahkan coba lagi.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $user->is_logged_in = false;
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('success', 'You have been logged out successfully.');
    }
}
