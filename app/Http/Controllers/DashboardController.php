<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Pengurus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboard.admin', $this->buildDashboardData(true));
    }

    public function user()
    {
        return view('dashboard.user', $this->buildDashboardData(false));
    }

    /**
     * Menyusun seluruh data dashboard secara aman terhadap struktur skema proyek.
     */
    private function buildDashboardData(bool $isAdmin): array
    {
        Carbon::setLocale('id');
        $today = Carbon::today();

        // 1. Statistik Dasar (aman jika tabel kosong)
        $totalMahasiswa = Schema::hasTable('mahasiswa') ? Mahasiswa::count() : 0;
        $totalKelas     = Schema::hasTable('kelas') ? Kelas::count() : 0;
        $totalPengurus  = Schema::hasTable('pengurus') ? Pengurus::count() : 0;
        $totalJabatan   = Schema::hasTable('jabatan') ? Jabatan::count() : 0;

        $userStats = [
            'total'      => User::count(),
            'active'     => User::active()->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
        ];

        // 2. Data Kelas beserta jumlah mahasiswa
        $daftarKelas = Schema::hasTable('kelas')
            ? Kelas::withCount('mahasiswa')
                ->orderByDesc('Angkatan')
                ->orderBy('Nama_Kelas')
                ->get()
            : collect();

        // 3. Struktur Pengurus Inti (diurutkan berdasarkan prioritas jabatan)
        $prioritasJabatan = [
            'ketua'       => 1,
            'wakil ketua' => 2,
            'sekretaris'  => 3,
            'bendahara'   => 4,
        ];

        $pengurusInti = Schema::hasTable('pengurus')
            ? Pengurus::with(['mahasiswa.kelas', 'jabatan'])
                ->get()
                ->sortBy(function ($item) use ($prioritasJabatan) {
                    $namaJabatan = strtolower(trim($item->jabatan->Nama_Jabatan ?? ''));
                    return $prioritasJabatan[$namaJabatan] ?? 99;
                })
                ->values()
            : collect();

        // 4. Data Ulang Tahun (deteksi kolom tanggal lahir yang tersedia di mahasiswa / users)
        $birthdayData = $this->resolveBirthdays($today);

        // 5. Pintasan Cepat dinamis sesuai route yang terdaftar
        $quickLinks = $this->resolveQuickLinks($isAdmin);

        return [
            'isAdmin'           => $isAdmin,
            'today'             => $today,
            'totalMahasiswa'    => $totalMahasiswa,
            'totalKelas'        => $totalKelas,
            'totalPengurus'     => $totalPengurus,
            'totalJabatan'      => $totalJabatan,
            'userStats'         => $userStats,
            'daftarKelas'       => $daftarKelas,
            'pengurusInti'      => $pengurusInti,
            'todayBirthdays'    => $birthdayData['today'],
            'upcomingBirthdays' => $birthdayData['upcoming'],
            'hasBirthdayColumn' => $birthdayData['has_column'],
            'quickLinks'        => $quickLinks,
        ];
    }

    /**
     * Mengambil data ulang tahun hari ini & terdekat dari tabel mahasiswa atau users
     * tanpa merusak query jika kolom tanggal lahir belum ditambahkan di migrasi.
     */
    private function resolveBirthdays(Carbon $today): array
    {
        $candidateColumns = ['Tanggal_Lahir', 'tanggal_lahir', 'tgl_lahir', 'birth_date', 'dob'];
        $records = collect();
        $hasColumn = false;

        // Cek tabel mahasiswa terlebih dahulu
        if (Schema::hasTable('mahasiswa')) {
            $matchedCol = collect($candidateColumns)->first(fn ($col) => Schema::hasColumn('mahasiswa', $col));

            if ($matchedCol) {
                $hasColumn = true;
                $records = Mahasiswa::with(['kelas', 'pengurus.jabatan'])
                    ->whereNotNull($matchedCol)
                    ->get()
                    ->map(function ($mhs) use ($matchedCol) {
                        return [
                            'name'      => $mhs->Nama_Mahasiswa,
                            'nim'       => $mhs->NIM,
                            'foto'      => $mhs->Foto,
                            'kelas'     => $mhs->kelas->Nama_Kelas ?? null,
                            'jabatan'   => $mhs->pengurus?->jabatan?->Nama_Jabatan,
                            'whatsapp'  => $mhs->pengurus?->No_WhatsApp,
                            'raw_date'  => $mhs->{$matchedCol},
                        ];
                    });
            }
        }

        // Fallback ke tabel users jika di mahasiswa tidak ada kolom tanggal lahir
        if (!$hasColumn && Schema::hasTable('users')) {
            $matchedCol = collect($candidateColumns)->first(fn ($col) => Schema::hasColumn('users', $col));

            if ($matchedCol) {
                $hasColumn = true;
                $records = User::whereNotNull($matchedCol)
                    ->get()
                    ->map(function ($usr) use ($matchedCol) {
                        return [
                            'name'      => $usr->name,
                            'nim'       => null,
                            'foto'      => null,
                            'kelas'     => null,
                            'jabatan'   => $usr->isAdmin() ? 'Admin' : 'Anggota',
                            'whatsapp'  => $usr->phone,
                            'raw_date'  => $usr->{$matchedCol},
                        ];
                    });
            }
        }

        if (!$hasColumn || $records->isEmpty()) {
            return [
                'has_column' => $hasColumn,
                'today'      => collect(),
                'upcoming'   => collect(),
            ];
        }

        $processed = $records->map(function ($item) use ($today) {
            try {
                $birthDate = Carbon::parse($item['raw_date']);
            } catch (\Throwable $e) {
                return null;
            }

            $nextBirthday = $birthDate->copy()->year($today->year)->startOfDay();
            if ($nextBirthday->lt($today)) {
                $nextBirthday->addYear();
            }

            $daysRemaining = (int) $today->diffInDays($nextBirthday, false);
            $turningAge    = $nextBirthday->year - $birthDate->year;

            return array_merge($item, [
                'birth_date'     => $birthDate,
                'next_birthday'  => $nextBirthday,
                'days_remaining' => $daysRemaining,
                'turning_age'    => $turningAge > 0 ? $turningAge : null,
                'is_today'       => $daysRemaining === 0,
                'is_this_month'  => $nextBirthday->month === $today->month && $nextBirthday->year === $today->year,
            ]);
        })->filter()->values();

        $todayBirthdays = $processed
            ->where('is_today', true)
            ->sortBy('name')
            ->values();

        // Ulang tahun terdekat (H-1 s/d H-45 atau dalam bulan berjalan)
        $upcomingBirthdays = $processed
            ->where('is_today', false)
            ->filter(fn ($item) => $item['days_remaining'] <= 45 || $item['is_this_month'])
            ->sortBy('days_remaining')
            ->take(6)
            ->values();

        return [
            'has_column' => true,
            'today'      => $todayBirthdays,
            'upcoming'   => $upcomingBirthdays,
        ];
    }

    /**
     * Menyusun pintasan cepat ke modul lain tanpa menyebabkan RouteNotFoundException.
     */
    private function resolveQuickLinks(bool $isAdmin): array
    {
        $candidates = [
            [
                'label'       => 'Profil Kelas',
                'description' => 'Identitas prodi, angkatan & daftar kelas',
                'route'       => 'kelas.index',
                'badge'       => 'Aktif',
                'accent'      => 'indigo',
            ],
            [
                'label'       => 'Data Mahasiswa',
                'description' => 'Direktori & profil anggota kelas',
                'route'       => 'mahasiswa.index',
                'badge'       => 'Aktif',
                'accent'      => 'sky',
            ],
            [
                'label'       => 'Struktur Kelas & Pengurus',
                'description' => 'Lihat susunan pengurus aktif',
                'route'       => 'struktur-kelas.index',
                'badge'       => 'Aktif',
                'accent'      => 'emerald',
            ],
            [
                'label'       => 'Generate Laporan',
                'description' => 'Export data kelas & mahasiswa (PDF/Excel)',
                'route'       => 'laporan.index',
                'badge'       => 'Export',
                'accent'      => 'amber',
            ],
        ];

        return collect($candidates)->map(function ($item) {
            $exists = Route::has($item['route']);
            return array_merge($item, [
                'url'       => $exists ? route($item['route']) : '#struktur-kelas-section',
                'available' => $exists,
            ]);
        })->all();
    }
}
