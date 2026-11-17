<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Pengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StrukturKelasController extends Controller
{
    /**
     * Menampilkan halaman Struktur Kelas & Kepengurusan (untuk Admin & User).
     */
    public function index(Request $request)
    {
        $selectedKelasId = $request->query('kelas');
        $search          = trim((string) $request->query('q', ''));

        $daftarKelas = Schema::hasTable('kelas')
            ? Kelas::withCount('mahasiswa')
                ->orderByDesc('Angkatan')
                ->orderBy('Nama_Kelas')
                ->get()
            : collect();

        $activeKelas = $selectedKelasId
            ? $daftarKelas->firstWhere('ID_Kelas', $selectedKelasId)
            : $daftarKelas->first();

        $prioritasJabatan = [
            'ketua'       => 1,
            'wakil ketua' => 2,
            'sekretaris'  => 3,
            'bendahara'   => 4,
        ];

        $pengurusQuery = Schema::hasTable('pengurus')
            ? Pengurus::with(['mahasiswa.kelas', 'jabatan'])
            : null;

        if ($pengurusQuery && $selectedKelasId) {
            $pengurusQuery->whereHas('mahasiswa', function ($q) use ($selectedKelasId) {
                $q->where('ID_Kelas', $selectedKelasId);
            });
        }

        $daftarPengurus = $pengurusQuery
            ? $pengurusQuery->get()
                ->sortBy(function ($item) use ($prioritasJabatan) {
                    $nama = strtolower(trim($item->jabatan->Nama_Jabatan ?? ''));
                    return $prioritasJabatan[$nama] ?? 99;
                })
                ->values()
            : collect();

        $mahasiswaQuery = Schema::hasTable('mahasiswa')
            ? Mahasiswa::with(['kelas', 'pengurus.jabatan'])
                ->orderBy('Nama_Mahasiswa')
            : null;

        if ($mahasiswaQuery && $selectedKelasId) {
            $mahasiswaQuery->where('ID_Kelas', $selectedKelasId);
        }

        if ($mahasiswaQuery && $search !== '') {
            $mahasiswaQuery->where(function ($q) use ($search) {
                $q->where('Nama_Mahasiswa', 'like', "%{$search}%")
                  ->orWhere('NIM', 'like', "%{$search}%");
            });
        }

        $daftarMahasiswa = $mahasiswaQuery ? $mahasiswaQuery->get() : collect();

        $isAdmin = auth()->user()?->isAdmin() ?? false;
        $daftarJabatan = ($isAdmin && Schema::hasTable('jabatan'))
            ? Jabatan::orderBy('ID_Jabatan')->get()
            : collect();

        $kandidatMahasiswa = ($isAdmin && Schema::hasTable('mahasiswa'))
            ? Mahasiswa::with('kelas')->orderBy('Nama_Mahasiswa')->get()
            : collect();

        return view('struktur-kelas.index', [
            'isAdmin'           => $isAdmin,
            'daftarKelas'       => $daftarKelas,
            'activeKelas'       => $activeKelas,
            'selectedKelasId'   => $selectedKelasId,
            'search'            => $search,
            'daftarPengurus'    => $daftarPengurus,
            'daftarMahasiswa'   => $daftarMahasiswa,
            'daftarJabatan'     => $daftarJabatan,
            'kandidatMahasiswa' => $kandidatMahasiswa,
        ]);
    }

    /**
     * Menambahkan atau menetapkan pengurus kelas (khusus Admin).
     */
    public function storePengurus(Request $request)
    {
        $validated = $request->validate([
            'NIM'         => ['required', 'string', 'max:15', 'exists:mahasiswa,NIM'],
            'ID_Jabatan'  => ['required', 'integer', 'exists:jabatan,ID_Jabatan'],
            'No_WhatsApp' => ['nullable', 'string', 'max:15'],
            'Email'       => ['nullable', 'email', 'max:100'],
        ], [
            'NIM.required'        => 'Silakan pilih mahasiswa terlebih dahulu.',
            'NIM.exists'          => 'NIM mahasiswa tidak ditemukan.',
            'ID_Jabatan.required' => 'Silakan pilih jabatan kepengurusan.',
            'ID_Jabatan.exists'   => 'Jabatan yang dipilih tidak valid.',
            'Email.email'         => 'Format alamat email tidak valid.',
        ]);

        Pengurus::updateOrCreate(
            ['NIM' => $validated['NIM']],
            [
                'ID_Jabatan'  => $validated['ID_Jabatan'],
                'No_WhatsApp' => $validated['No_WhatsApp'] ?? null,
                'Email'       => $validated['Email'] ?? null,
            ]
        );

        return redirect()
            ->route('struktur-kelas.index')
            ->with('success', 'Data pengurus kelas berhasil disimpan.');
    }

    /**
     * Memperbarui jabatan atau kontak pengurus (khusus Admin).
     */
    public function updatePengurus(Request $request, string $nim)
    {
        $pengurus = Pengurus::findOrFail($nim);

        $validated = $request->validate([
            'ID_Jabatan'  => ['required', 'integer', 'exists:jabatan,ID_Jabatan'],
            'No_WhatsApp' => ['nullable', 'string', 'max:15'],
            'Email'       => ['nullable', 'email', 'max:100'],
        ]);

        $pengurus->update([
            'ID_Jabatan'  => $validated['ID_Jabatan'],
            'No_WhatsApp' => $validated['No_WhatsApp'] ?? null,
            'Email'       => $validated['Email'] ?? null,
        ]);

        return redirect()
            ->route('struktur-kelas.index')
            ->with('success', 'Data pengurus berhasil diperbarui.');
    }

    /**
     * Menghapus status pengurus mahasiswa (khusus Admin).
     */
    public function destroyPengurus(string $nim)
    {
        $pengurus = Pengurus::findOrFail($nim);
        $pengurus->delete();

        return redirect()
            ->route('struktur-kelas.index')
            ->with('success', 'Mahasiswa telah dihapus dari daftar pengurus.');
    }
}
