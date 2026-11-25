<?php

namespace App\Http\Controllers\Kas;

use App\Services\KasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController
{
    public function __construct(private KasService $kas)
    {
    }

    public function index(Request $request): View
    {
        $pengguna = Auth::guard('kas')->user();

        // Anggota hanya boleh melihat data miliknya sendiri.
        $idAnggota = $pengguna->bisaKelola() ? null : $pengguna->id_anggota;

        $jenis = $request->query('jenis');
        $iuran = $this->kas->ringkasanIuran($request->query('q'), $idAnggota);

        return view('kas.dashboard', [
            'pengguna'    => $pengguna,
            'saldo'       => $this->kas->saldo(),
            'totalMasuk'  => $this->kas->totalMasuk(),
            'totalKeluar' => $this->kas->totalKeluar(),
            'iuran'       => $iuran,
            'riwayat'     => $this->kas->riwayat($jenis, $idAnggota),
            'jenis'       => in_array($jenis, ['masuk', 'keluar'], true) ? $jenis : 'semua',
            'q'           => $request->query('q'),
            'terakhir'    => now(),
        ]);
    }
}
