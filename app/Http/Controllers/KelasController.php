<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $daftarKelas = Kelas::withCount('mahasiswa')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('ID_Kelas', 'like', "%{$search}%")
                      ->orWhere('Nama_Kelas', 'like', "%{$search}%")
                      ->orWhere('Program_Studi', 'like', "%{$search}%")
                      ->orWhere('Angkatan', 'like', "%{$search}%");
            })
            ->orderByDesc('Angkatan')
            ->orderBy('Nama_Kelas')
            ->get();

        return view('kelas.index', [
            'daftarKelas' => $daftarKelas,
            'search'      => $search,
            'isAdmin'     => auth()->user()?->isAdmin() ?? false,
        ]);
    }

    public function create()
    {
        return view('kelas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ID_Kelas'      => ['required', 'string', 'max:10', 'unique:kelas,ID_Kelas'],
            'Nama_Kelas'    => ['required', 'string', 'max:50'],
            'Program_Studi' => ['required', 'string', 'max:100'],
            'Angkatan'      => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 2)],
        ], [
            'ID_Kelas.required' => 'Kode/ID Kelas wajib diisi.',
            'ID_Kelas.unique'   => 'ID Kelas sudah terdaftar.',
        ]);

        Kelas::create($validated);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Profil kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kela)
    {
        $kela->load(['mahasiswa.pengurus.jabatan']);

        return view('kelas.show', [
            'kelas'   => $kela,
            'isAdmin' => auth()->user()?->isAdmin() ?? false,
        ]);
    }

    public function edit(Kelas $kela)
    {
        return view('kelas.edit', [
            'kelas' => $kela,
        ]);
    }

    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'Nama_Kelas'    => ['required', 'string', 'max:50'],
            'Program_Studi' => ['required', 'string', 'max:100'],
            'Angkatan'      => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 2)],
        ]);

        $kela->update($validated);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Profil kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Profil kelas berhasil dihapus.');
    }
}
