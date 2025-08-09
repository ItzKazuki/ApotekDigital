<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Wavey\Sweetalert\Sweetalert;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot');
    }

    public function showResetPasswordForm($token = null)
    {
        if (!$token) {
            return redirect()->route('auth.reset-password.request')->with('error', 'Token tidak valid atau telah kedaluwarsa.');
        }

        return view('auth.reset', ['token' => $token]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email:dns',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Silakan masukkan alamat email yang valid.',
            'email.dns' => 'Domain email harus memiliki catatan DNS yang valid.',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status == Password::ResetLinkSent) {
            return redirect()->route('auth.login')->with('success', 'Silahkan cek email anda untuk reset password');
        } else {
            return redirect()->route('auth.login')->with('error', 'Gagal mengirim link reset password, silahkan coba lagi nanti atau hubungi admin.');
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:3|confirmed',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Silakan masukkan alamat email yang valid.',
            'email.dns' => 'Domain email harus memiliki catatan DNS yang valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password harus minimal 3 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password
                ]);
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PasswordReset) {
            return redirect()->route('auth.login')->with('success', 'Password berhasil diubah, silahkan login kembali.');
        }

        return redirect()->route('auth.login')->with('error', 'Gagal mengubah password, silahkan coba lagi nanti atau hubungi admin.');
    }
}
