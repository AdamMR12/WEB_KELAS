<?php

namespace App\Http\Controllers\Kas;

use App\Models\Anggota;
use App\Models\Pembayaran;
use App\Models\Periode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PembayaranController
{
    public function index(): View
    {
        return view('kas.pembayaran.index', [
            'pembayaran' => Pembayaran::with(['anggota', 'periode', 'pencatat'])
                ->orderByDesc('tanggal_bayar')
                ->orderByDesc('id_pembayaran')
                ->get(),
            'anggota'    => Anggota::aktif()->orderBy('nama')->get(),
            'periode'    => Periode::orderByDesc('jatuh_tempo')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_anggota'    => ['required', Rule::exists('anggota', 'id_anggota')->where('status', 'aktif')],
            'id_periode'    => ['required', 'exists:periode,id_periode'],
            'tanggal_bayar' => ['required', 'date'],
            'nominal'       => ['required', 'integer', 'min:1', 'max:4294967295'],
        ], [
            'nominal.min' => 'Nominal harus lebih dari 0.',
        ]);

        // Satu anggota hanya punya satu catatan pembayaran per periode.
        $sudahAda = Pembayaran::where('id_anggota', $data['id_anggota'])
            ->where('id_periode', $data['id_periode'])
            ->exists();

        if ($sudahAda) {
            return back()->withInput()
                ->withErrors(['id_periode' => 'Anggota ini sudah tercatat membayar untuk periode tersebut.']);
        }

        // Pencatat selalu akun yang sedang login, bukan dari input form.
        Pembayaran::create($data + ['id_user' => Auth::guard('kas')->id()]);

        return redirect()->route('kas.pembayaran.index')->with('sukses', 'Pembayaran dicatat.');
    }

    /** Hanya untuk koreksi salah input; saldo otomatis dihitung ulang. */
    public function destroy(Pembayaran $pembayaran): RedirectResponse
    {
        $pembayaran->delete();

        return redirect()->route('kas.pembayaran.index')
            ->with('sukses', 'Catatan pembayaran dihapus. Saldo kas dihitung ulang.');
    }
}
