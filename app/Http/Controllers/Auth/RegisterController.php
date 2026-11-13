<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'level'     => User::LEVEL_USER,
            'is_active' => true,
        ]);

        event(new Registered($user)); // memicu pengiriman email verifikasi
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil. Silakan cek email untuk verifikasi.');
    }
}   