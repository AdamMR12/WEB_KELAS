<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use Illuminate\Support\Collection;

/**
 * Perhitungan kas. Saldo dan tunggakan tidak disimpan di database,
 * selalu dihitung dari tabel pembayaran, pengeluaran, dan periode.
 */
class KasService
{
    public function totalMasuk(): int
    {
        return (int) Pembayaran::sum('nominal');
    }

    public function totalKeluar(): int
    {
        return (int) Pengeluaran::sum('nominal');
    }

    public function saldo(): int
    {
        return $this->totalMasuk() - $this->totalKeluar();
    }

    /** Pengeluaran tidak boleh melebihi saldo yang tersedia. */
    public function bolehKeluar(int $nominal, ?int $kecualiId = null): bool
    {
        $saldo = $this->saldo();

        if ($kecualiId) {
            $saldo += (int) Pengeluaran::whereKey($kecualiId)->value('nominal');
        }

        return $nominal <= $saldo;
    }

    /**
     * Riwayat gabungan pemasukan dan pengeluaran, terbaru di atas,
     * lengkap dengan saldo berjalan pada tiap baris.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function riwayat(?string $jenis = null, ?int $idAnggota = null): Collection
    {
        $masuk = Pembayaran::with(['anggota', 'periode'])->get()->map(fn (Pembayaran $p) => [
            'tanggal'    => $p->tanggal_bayar,
            'jenis'      => 'masuk',
            'keterangan' => 'Iuran ' . ($p->periode?->nama_periode ?? '-'),
            'dari'       => $p->anggota?->nama,
            'id_anggota' => $p->id_anggota,
            'nominal'    => $p->nominal,
            'urut'       => 'm' . str_pad((string) $p->id_pembayaran, 10, '0', STR_PAD_LEFT),
        ]);

        $keluar = Pengeluaran::all()->map(fn (Pengeluaran $p) => [
            'tanggal'    => $p->tanggal,
            'jenis'      => 'keluar',
            'keterangan' => $p->keterangan,
            'dari'       => null,
            'id_anggota' => null,
            'nominal'    => $p->nominal,
            'urut'       => 'k' . str_pad((string) $p->id_pengeluaran, 10, '0', STR_PAD_LEFT),
        ]);

        $saldo = 0;

        $baris = $masuk->concat($keluar)
            ->sortBy([['tanggal', 'asc'], ['urut', 'asc']])
            ->values()
            ->map(function (array $row) use (&$saldo) {
                $saldo += $row['jenis'] === 'masuk' ? $row['nominal'] : -$row['nominal'];
                $row['saldo'] = $saldo;

                return $row;
            })
            ->reverse()
            ->values();

        // Anggota hanya melihat pembayarannya sendiri. Saldo berjalan tetap
        // dihitung dari seluruh transaksi, baru kemudian baris difilter.
        if ($idAnggota !== null) {
            $baris = $baris->where('jenis', 'masuk')->where('id_anggota', $idAnggota)->values();
        }

        return in_array($jenis, ['masuk', 'keluar'], true)
            ? $baris->where('jenis', $jenis)->values()
            : $baris;
    }

    /**
     * Ringkasan status iuran seluruh anggota aktif.
     *
     * @return array{daftar: Collection, jumlah: int, lunas: int, persen: int}
     */
    public function ringkasanIuran(?string $kata = null, ?int $idAnggota = null): array
    {
        $semua = Anggota::aktif()->when($idAnggota, fn ($q) => $q->whereKey($idAnggota))->orderBy('nama')->get()
            ->map(fn (Anggota $a) => ['anggota' => $a, 'status' => $a->statusIuran()]);

        $lunas = $semua->where('status.lunas', true)->count();
        $jumlah = $semua->count();

        $kata = mb_strtolower(trim((string) $kata));

        $daftar = $kata === ''
            ? $semua
            : $semua->filter(fn (array $x) => str_contains(mb_strtolower($x['anggota']->nama), $kata)
                || str_contains(mb_strtolower($x['anggota']->nim), $kata))->values();

        return [
            'daftar' => $daftar,
            'jumlah' => $jumlah,
            'lunas'  => $lunas,
            'persen' => $jumlah ? (int) round($lunas / $jumlah * 100) : 0,
        ];
    }
}
