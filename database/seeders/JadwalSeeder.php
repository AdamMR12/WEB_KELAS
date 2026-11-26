<?php

namespace Database\Seeders;

use App\Models\AgendaCategory;
use App\Models\AgendaNonAkademik;
use App\Models\AgendaStatus;
use App\Models\Dosen;
use App\Models\JadwalAgenda;
use App\Models\JadwalMk;
use App\Models\MataKuliah;
use App\Models\Ruangan;
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

        // Seed Jadwal Kuliah hanya jika kosong agar idempoten
        if (JadwalMk::count() === 0) {
            $schedules = [
                ['kode' => 'SI201', 'mk' => 'Pemrograman Web', 'sks' => 3, 'hari' => 'Rabu', 'tanggal' => '2026-11-18', 'mulai' => '08:00:00', 'selesai' => '10:00:00', 'ruang' => 'Lab Komputer 3', 'nip' => '198801012015041001', 'dosen' => 'Pak Hendra, M.Kom.', 'jenis' => 'Mata Kuliah Wajib', 'metode' => 'Praktikum Lab', 'warna' => 'lime', 'pengganti' => false],
                ['kode' => 'SI202', 'mk' => 'Basis Data', 'sks' => 3, 'hari' => 'Rabu', 'tanggal' => '2026-11-18', 'mulai' => '10:15:00', 'selesai' => '12:15:00', 'ruang' => 'Lab Basis Data 1', 'nip' => '198902022016042001', 'dosen' => 'Bu Sri Wahyuni, M.T.', 'jenis' => 'Mata Kuliah Wajib', 'metode' => 'Praktikum Lab', 'warna' => 'purple', 'pengganti' => false],
                ['kode' => 'SI203', 'mk' => 'Praktikum Web Lanjut', 'sks' => 2, 'hari' => 'Rabu', 'tanggal' => '2026-11-25', 'mulai' => '13:00:00', 'selesai' => '14:40:00', 'ruang' => 'Lab Komputer 3', 'nip' => '198801012015041001', 'dosen' => 'Pak Hendra, M.Kom.', 'jenis' => 'Kuliah Pengganti', 'metode' => 'Praktikum Lab', 'warna' => 'navy', 'pengganti' => true],
                ['kode' => 'SI204', 'mk' => 'Algoritma & Pemrograman', 'sks' => 3, 'hari' => 'Selasa', 'tanggal' => '2026-11-24', 'mulai' => '08:00:00', 'selesai' => '10:00:00', 'ruang' => 'Lab Komputer 1', 'nip' => '198703032014041002', 'dosen' => 'Pak Agus Hermawan, M.T.', 'jenis' => 'Mata Kuliah Wajib', 'metode' => 'Praktikum Lab', 'warna' => 'emerald', 'pengganti' => false],
                ['kode' => 'SI205', 'mk' => 'Sistem Operasi', 'sks' => 3, 'hari' => 'Selasa', 'tanggal' => '2026-11-24', 'mulai' => '10:15:00', 'selesai' => '12:15:00', 'ruang' => 'Lab Komputer 2', 'nip' => '199104042018042003', 'dosen' => 'Bu Dewi Lestari, M.Kom.', 'jenis' => 'Mata Kuliah Wajib', 'metode' => 'Teori Kelas', 'warna' => 'amber', 'pengganti' => false],
                ['kode' => 'SI206', 'mk' => 'Arsitektur Komputer', 'sks' => 2, 'hari' => 'Selasa', 'tanggal' => '2026-11-24', 'mulai' => '13:00:00', 'selesai' => '14:40:00', 'ruang' => 'Ruang Teori 301', 'nip' => '199205052019041004', 'dosen' => 'Pak Dimas Tri, M.T.', 'jenis' => 'Kuliah Pengganti', 'metode' => 'Kuliah Tambahan', 'warna' => 'rose', 'pengganti' => true],
                ['kode' => 'SI207', 'mk' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'hari' => 'Senin', 'tanggal' => '2026-11-23', 'mulai' => '08:00:00', 'selesai' => '10:00:00', 'ruang' => 'Ruang Teori 204', 'nip' => '199006062017042005', 'dosen' => 'Bu Rina Maryana, M.Kom.', 'jenis' => 'Mata Kuliah Wajib', 'metode' => 'Teori Kelas', 'warna' => 'purple', 'pengganti' => false],
                ['kode' => 'SI208', 'mk' => 'Jaringan Komputer', 'sks' => 3, 'hari' => 'Senin', 'tanggal' => '2026-11-23', 'mulai' => '10:15:00', 'selesai' => '12:15:00', 'ruang' => 'Lab Jaringan', 'nip' => '199205052019041004', 'dosen' => 'Pak Dimas Tri, M.T.', 'jenis' => 'Mata Kuliah Wajib', 'metode' => 'Praktikum Lab', 'warna' => 'cyan', 'pengganti' => false],
                ['kode' => 'SI209', 'mk' => 'Interaksi Manusia & Komputer', 'sks' => 3, 'hari' => 'Kamis', 'tanggal' => '2026-11-19', 'mulai' => '08:00:00', 'selesai' => '10:00:00', 'ruang' => 'Lab Multimedia', 'nip' => '198902022016042001', 'dosen' => 'Bu Sri Wahyuni, M.T.', 'jenis' => 'Mata Kuliah Wajib', 'metode' => 'Praktikum Lab', 'warna' => 'cyan', 'pengganti' => false],
                ['kode' => 'SI210', 'mk' => 'Proyek Sistem Informasi', 'sks' => 4, 'hari' => 'Jumat', 'tanggal' => '2026-11-20', 'mulai' => '08:00:00', 'selesai' => '11:30:00', 'ruang' => 'Lab Riset TI', 'nip' => '198705122015041001', 'dosen' => 'Rahmat Irsyada, M.Pd.', 'jenis' => 'Mata Kuliah Wajib', 'metode' => 'Praktikum Lab', 'warna' => 'lime', 'pengganti' => false],
            ];

            foreach ($schedules as $s) {
                $mk = MataKuliah::firstOrCreate(['nama_mk' => $s['mk']], ['kode_mk' => $s['kode'], 'sks' => $s['sks']]);
                $dosen = Dosen::firstOrCreate(['nama' => $s['dosen']], ['nip' => $s['nip']]);
                $ruang = Ruangan::firstOrCreate(['nama_ruangan' => $s['ruang']]);

                JadwalMk::create([
                    'id_mk' => $mk->id_mk,
                    'id_dosen' => $dosen->id_dosen,
                    'id_ruangan' => $ruang->id_ruangan,
                    'hari' => $s['hari'],
                    'tanggal' => $s['tanggal'],
                    'jam_mulai' => $s['mulai'],
                    'jam_selesai' => $s['selesai'],
                    'tipe_sks' => $s['metode'],
                    'jenis' => $s['jenis'],
                    'warna_bg' => $s['warna'],
                    'is_pengganti' => $s['pengganti'],
                ]);
            }
        }

        if (JadwalAgenda::count() === 0) {
            $agendas = [
                ['judul' => 'Rapat Kerja BEM TI: Evaluasi Program Kerja', 'kategori' => 'ORGANISASI', 'status' => 'Mendatang', 'tgl' => '2026-11-20', 'selesai' => null, 'display' => 'Jumat, 20 Nov 2026', 'jam' => '15:30:00', 'waktu' => '15:30 WIB', 'lokasi' => 'Ruang Sidang Utama Gedung TI', 'koord' => 'BEM Jurusan TI', 'ml' => 'Peserta', 'mv' => 'Delegasi SI-2A'],
                ['judul' => 'Workshop UI/UX Design System with FigCraft', 'kategori' => 'WORKSHOP', 'status' => 'Pendaftaran Dibuka', 'tgl' => '2026-11-22', 'selesai' => null, 'display' => 'Minggu, 22 Nov 2026', 'jam' => '13:30:00', 'waktu' => '13:30 WIB', 'lokasi' => 'Lab Komputer Riset TI', 'koord' => 'Divisi Litbang TI', 'ml' => 'Fasilitas', 'mv' => 'E-Sertifikat & Modul'],
                ['judul' => 'Hackathon Inovasi Digital Mahasiswa 2026', 'kategori' => 'KOMPETISI', 'status' => 'Tahap Seleksi', 'tgl' => '2026-11-25', 'selesai' => '2026-11-27', 'display' => '25 - 27 Nov 2026', 'jam' => '09:00:00', 'waktu' => '09:00 WIB', 'lokasi' => 'Hall Pusat Inovasi Politeknik', 'koord' => 'Skuad SI-2A', 'ml' => 'Pembimbing', 'mv' => 'Pak Hendra, M.Kom.'],
                ['judul' => 'Bakti Sosial & Literasi Digital Panti Asuhan', 'kategori' => 'SOSIAL', 'status' => 'Pengumpulan Donasi', 'tgl' => '2026-11-30', 'selesai' => null, 'display' => 'Senin, 30 Nov 2026', 'jam' => '08:30:00', 'waktu' => '08:30 WIB', 'lokasi' => 'Aula Mitra Masyarakat', 'koord' => 'Komunitas Peduli TI', 'ml' => 'Kategori', 'mv' => 'Pengabdian Masyarakat'],
            ];

            foreach ($agendas as $a) {
                $ruang = Ruangan::firstOrCreate(['nama_ruangan' => $a['lokasi']]);
                $master = AgendaNonAkademik::create([
                    'nama_agenda' => $a['judul'],
                    'kategori' => $a['kategori'],
                    'status' => $a['status'],
                    'koordinator' => $a['koord'],
                    'meta_label' => $a['ml'],
                    'meta_value' => $a['mv'],
                ]);

                JadwalAgenda::create([
                    'id_agenda' => $master->id_agenda,
                    'id_ruangan' => $ruang->id_ruangan,
                    'tanggal' => $a['tgl'],
                    'tanggal_selesai' => $a['selesai'],
                    'tanggal_display' => $a['display'],
                    'jam_mulai' => $a['jam'],
                    'waktu_label' => $a['waktu'],
                ]);
            }
        }
    }
}
