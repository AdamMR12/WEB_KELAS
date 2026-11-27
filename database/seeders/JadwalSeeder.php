<?php

namespace Database\Seeders;

use App\Models\AgendaCategory;
use App\Models\AgendaStatus;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['kode' => 'ORGANISASI', 'nama' => 'Organisasi & BEM', 'warna_bg' => 'amber'],
            ['kode' => 'WORKSHOP', 'nama' => 'Workshop & Pelatihan', 'warna_bg' => 'lime'],
            ['kode' => 'KOMPETISI', 'nama' => 'Kompetisi Mahasiswa', 'warna_bg' => 'cyan'],
            ['kode' => 'SOSIAL', 'nama' => 'Sosial & Pengabdian', 'warna_bg' => 'rose'],
        ];
        foreach ($categories as $cat) {
            AgendaCategory::updateOrCreate(['kode' => $cat['kode']], $cat);
        }

        foreach (['Mendatang', 'Pendaftaran Dibuka', 'Tahap Seleksi', 'Pengumpulan Donasi', 'Sedang Berlangsung'] as $st) {
            AgendaStatus::firstOrCreate(['nama' => $st]);
        }
    }
}
