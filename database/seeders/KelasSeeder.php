<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $dataKelas = [
            [
                'ID_Kelas' => 'K001',
                'Nama_Kelas' => 'SI 2A',
                'Program_Studi' => 'D-III Sistem Informasi',
                'Angkatan' => 2026,
            ],
        ];

        foreach ($dataKelas as $kelas) {
            Kelas::updateOrCreate(
                ['ID_Kelas' => $kelas['ID_Kelas']],
                $kelas
            );
        }
    }
}
