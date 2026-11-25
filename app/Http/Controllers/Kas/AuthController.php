<?php

namespace App\Http\Controllers\Kas;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController
{
    public function showForm(): View|RedirectResponse
    {
        if (Auth::guard('kas')->check()) {
            return redirect()->route('kas.dashboard');
        }

        return view('kas.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'NIM / username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (! Auth::guard('kas')->attempt($data, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'NIM / username atau password salah.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('kas.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('kas')->logout();

        // Hanya batalkan token CSRF, jangan hapus session login kelompok lain.
        $request->session()->regenerateToken();

        return redirect()->route('kas.login');
    }
}
