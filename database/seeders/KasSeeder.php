<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\Pengguna;
use App\Models\Periode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Anggota kelas SI 2A (dari data lama index.html)
        $mahasiswa = [
            ['10112001', 'Aakip Hasan Mutakin'],
            ['10112004', 'Adam Muhammad Rizqy'],
            ['10112007', 'Ahmad Septian Ramdani'],
            ['10112010', 'Al Ikhsan'],
            ['10112013', 'Alya Putri'],
            ['10112016', 'Aulia Putri Ramadina'],
            ['10112019', 'Cendra Kholifah Nuryadin'],
            ['10112022', 'Dealova Frazelia Setiawan'],
            ['10112025', 'Dian Dwi Nazira'],
            ['10112031', 'Farid Nur Fahrudin'],
            ['10112034', 'Gendis Wismaya Arimbi'],
            ['10112037', 'Haikal Rizki Atallah'],
            ['10112043', 'Meidina Fauziah Ulmi'],
            ['10112049', 'Muhammad Nizar Zulhaqy'],
            ['10112052', 'Nadyla Dwi Gayantry'],
            ['10112055', 'Nazira Gilda'],
            ['10112058', 'Nizar Misbahul Ulum'],
            ['10112061', 'Putri Nazila Syahrani'],
            ['10112064', 'Rafli El Shirazy'],
            ['10112067', 'Regina Ayu Cahyanti'],
            ['10112070', 'Riza Aditya Pirmansyah'],
            ['10112073', 'Siti Nuraniyyah'],
            ['10112076', 'Sukmadirjawijaya'],
            ['10112079', 'Vina Noviyanti'],
            ['10112082', 'Yessy Aiska'],
            ['10112085', 'Zidan Maulana Haitami'],
        ];

        foreach ($mahasiswa as [$nim, $nama]) {
            Anggota::updateOrCreate(
                ['nim' => $nim],
                ['nama' => $nama, 'status' => 'aktif']
            );
        }

        // 2. Akun pengguna: admin, bendahara, dan seluruh anggota
        //    (password default = NIM untuk anggota/bendahara; admin123 untuk admin)
        Pengguna::updateOrCreate(
            ['username' => 'admin'],
            ['password' => Hash::make('admin123'), 'role' => Pengguna::ROLE_ADMIN, 'id_anggota' => null]
        );

        $bendahara = Anggota::where('nim', '10112058')->first();
        Pengguna::updateOrCreate(
            ['username' => '10112058'],
            ['password' => Hash::make('10112058'), 'role' => Pengguna::ROLE_BENDAHARA, 'id_anggota' => $bendahara?->id_anggota]
        );

        foreach ($mahasiswa as [$nim]) {
            if ($nim === '10112058') {
                continue; // sudah dibuat sebagai bendahara
            }

            $anggota = Anggota::where('nim', $nim)->first();
            Pengguna::updateOrCreate(
                ['username' => $nim],
                ['password' => Hash::make($nim), 'role' => Pengguna::ROLE_ANGGOTA, 'id_anggota' => $anggota?->id_anggota]
            );
        }

        // 3. Periode iuran
        $periode = Periode::updateOrCreate(
            ['nama_periode' => 'Iuran September 2026'],
            ['nominal_iuran' => 20000, 'jatuh_tempo' => '2026-09-30']
        );

        // 4. Contoh pembayaran (sebagian anggota)
        $bendaharaUser = Pengguna::where('username', '10112058')->first();
        $sudahBayar = ['10112058', '10112085', '10112079', '10112061'];
        $tglBayar = ['10112058' => '2026-09-01', '10112085' => '2026-09-01', '10112079' => '2026-09-02', '10112061' => '2026-09-03'];

        foreach ($sudahBayar as $nim) {
            $anggota = Anggota::where('nim', $nim)->first();
            if (! $anggota || ! $bendaharaUser) {
                continue;
            }

            Pembayaran::updateOrCreate(
                ['id_anggota' => $anggota->id_anggota, 'id_periode' => $periode->id_periode],
                [
                    'id_user' => $bendaharaUser->id_user,
                    'tanggal_bayar' => $tglBayar[$nim],
                    'nominal' => $periode->nominal_iuran,
                ]
            );
        }

        // 5. Contoh pengeluaran
        if ($bendaharaUser) {
            Pengeluaran::updateOrCreate(
                ['tanggal' => '2026-09-08', 'keterangan' => 'Fotokopi materi praktikum'],
                ['id_user' => $bendaharaUser->id_user, 'nominal' => 15000]
            );
            Pengeluaran::updateOrCreate(
                ['tanggal' => '2026-09-15', 'keterangan' => 'Spidol dan penghapus papan tulis'],
                ['id_user' => $bendaharaUser->id_user, 'nominal' => 25000]
            );
        }
    }
}
