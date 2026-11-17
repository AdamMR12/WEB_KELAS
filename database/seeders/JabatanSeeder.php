<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $daftarJabatan = [
            'Ketua',
            'Wakil Ketua',
            'Sekretaris',
            'Bendahara',
        ];

        foreach ($daftarJabatan as $namaJabatan) {
            Jabatan::firstOrCreate([
                'Nama_Jabatan' => $namaJabatan,
            ]);
        }
    }
}
