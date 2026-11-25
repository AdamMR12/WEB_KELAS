<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pengaman modul Kas Kelas.
 *
 * Pemakaian di route:
 *   ->middleware('kas')                  semua pengguna kas yang sudah login
 *   ->middleware('kas:bendahara')        hanya bendahara
 *   ->middleware('kas:admin,bendahara')  admin atau bendahara
 */
class KasAuth
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::guard('kas')->user();

        if (! $user) {
            return redirect()->route('kas.login');
        }

        if ($roles && ! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Selama request ini auth()->user() / @auth merujuk ke pengguna kas.
        Auth::shouldUse('kas');

        return $next($request);
    }
}
