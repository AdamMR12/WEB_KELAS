<?php

namespace App\Http\Controllers\Kas;

use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController
{
    public function index(Request $request): View
    {
        $request->validate([
            'dari'   => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ]);

        $dari   = $request->query('dari');
        $sampai = $request->query('sampai');

        $pembayaran = Pembayaran::query()
            ->when($dari, fn ($q) => $q->whereDate('tanggal_bayar', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal_bayar', '<=', $sampai));

        $pengeluaran = Pengeluaran::query()
            ->when($dari, fn ($q) => $q->whereDate('tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal', '<=', $sampai));

        // Rekap pemasukan per periode iuran.
        $perPeriode = Periode::orderBy('jatuh_tempo')->get()->map(function (Periode $p) use ($dari, $sampai) {
            $bayar = $p->pembayaran()
                ->when($dari, fn ($q) => $q->whereDate('tanggal_bayar', '>=', $dari))
                ->when($sampai, fn ($q) => $q->whereDate('tanggal_bayar', '<=', $sampai));

            return [
                'periode' => $p,
                'jumlah'  => (clone $bayar)->count(),
                'total'   => (int) (clone $bayar)->sum('nominal'),
            ];
        });

        $totalMasuk  = (int) (clone $pembayaran)->sum('nominal');
        $totalKeluar = (int) (clone $pengeluaran)->sum('nominal');

        return view('kas.laporan', [
            'perPeriode'  => $perPeriode,
            'pengeluaran' => $pengeluaran->orderBy('tanggal')->orderBy('id_pengeluaran')->get(),
            'totalMasuk'  => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'selisih'     => $totalMasuk - $totalKeluar,
            'dari'        => $dari,
            'sampai'      => $sampai,
        ]);
    }
}
