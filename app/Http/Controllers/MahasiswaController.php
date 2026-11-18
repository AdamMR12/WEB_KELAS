<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Pengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $search    = trim((string) $request->query('q', ''));
        $kelasId   = $request->query('kelas');
        $jabatanId = $request->query('jabatan');

        $daftarMahasiswa = Mahasiswa::with(['kelas', 'pengurus.jabatan'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('NIM', 'like', "%{$search}%")
                      ->orWhere('Nama_Mahasiswa', 'like', "%{$search}%");
                });
            })
            ->when($kelasId, fn ($q) => $q->where('ID_Kelas', $kelasId))
            ->when($jabatanId, function ($q) use ($jabatanId) {
                if ($jabatanId === 'anggota') {
                    $q->doesntHave('pengurus');
                } else {
                    $q->whereHas('pengurus', fn ($sub) => $sub->where('ID_Jabatan', $jabatanId));
                }
            })
            ->orderBy('Nama_Mahasiswa')
            ->paginate(12)
            ->withQueryString();

        return view('mahasiswa.index', [
            'daftarMahasiswa' => $daftarMahasiswa,
            'daftarKelas'     => Kelas::orderBy('Nama_Kelas')->get(),
            'daftarJabatan'   => Jabatan::orderBy('ID_Jabatan')->get(),
            'search'          => $search,
            'kelasId'         => $kelasId,
            'jabatanId'       => $jabatanId,
            'isAdmin'         => auth()->user()?->isAdmin() ?? false,
        ]);
    }

    public function create()
    {
        return view('mahasiswa.create', [
            'daftarKelas'   => Kelas::orderBy('Nama_Kelas')->get(),
            'daftarJabatan' => Jabatan::orderBy('ID_Jabatan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'NIM'            => ['required', 'string', 'max:15', 'unique:mahasiswa,NIM'],
            'Nama_Mahasiswa' => ['required', 'string', 'max:100'],
            'ID_Kelas'       => ['required', 'string', 'exists:kelas,ID_Kelas'],
            'Foto'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'ID_Jabatan'     => ['nullable', 'integer', 'exists:jabatan,ID_Jabatan'],
            'No_WhatsApp'    => ['nullable', 'string', 'max:15'],
            'Email'          => ['nullable', 'email', 'max:100'],
        ], [
            'NIM.unique'        => 'NIM mahasiswa sudah terdaftar.',
            'ID_Kelas.required' => 'Kelas wajib dipilih.',
            'Foto.image'        => 'File foto harus berupa gambar (JPG, PNG, WebP).',
        ]);

        $fotoPath = null;
        if ($request->hasFile('Foto')) {
            $fotoPath = $request->file('Foto')->store('mahasiswa', 'public');
        }

        $mahasiswa = Mahasiswa::create([
            'NIM'            => $validated['NIM'],
            'Nama_Mahasiswa' => $validated['Nama_Mahasiswa'],
            'ID_Kelas'       => $validated['ID_Kelas'],
            'Foto'           => $fotoPath,
        ]);

        if (!empty($validated['ID_Jabatan'])) {
            Pengurus::updateOrCreate(
                ['NIM' => $mahasiswa->NIM],
                [
                    'ID_Jabatan'  => $validated['ID_Jabatan'],
                    'No_WhatsApp' => $validated['No_WhatsApp'] ?? null,
                    'Email'       => $validated['Email'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['kelas', 'pengurus.jabatan']);

        return view('mahasiswa.show', [
            'mahasiswa' => $mahasiswa,
            'isAdmin'   => auth()->user()?->isAdmin() ?? false,
        ]);
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['kelas', 'pengurus']);

        return view('mahasiswa.edit', [
            'mahasiswa'     => $mahasiswa,
            'daftarKelas'   => Kelas::orderBy('Nama_Kelas')->get(),
            'daftarJabatan' => Jabatan::orderBy('ID_Jabatan')->get(),
        ]);
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'Nama_Mahasiswa' => ['required', 'string', 'max:100'],
            'ID_Kelas'       => ['required', 'string', 'exists:kelas,ID_Kelas'],
            'Foto'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'ID_Jabatan'     => ['nullable', 'integer', 'exists:jabatan,ID_Jabatan'],
            'No_WhatsApp'    => ['nullable', 'string', 'max:15'],
            'Email'          => ['nullable', 'email', 'max:100'],
        ]);

        $fotoPath = $mahasiswa->Foto;
        if ($request->hasFile('Foto')) {
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('Foto')->store('mahasiswa', 'public');
        }

        $mahasiswa->update([
            'Nama_Mahasiswa' => $validated['Nama_Mahasiswa'],
            'ID_Kelas'       => $validated['ID_Kelas'],
            'Foto'           => $fotoPath,
        ]);

        if (!empty($validated['ID_Jabatan'])) {
            Pengurus::updateOrCreate(
                ['NIM' => $mahasiswa->NIM],
                [
                    'ID_Jabatan'  => $validated['ID_Jabatan'],
                    'No_WhatsApp' => $validated['No_WhatsApp'] ?? null,
                    'Email'       => $validated['Email'] ?? null,
                ]
            );
        } else {
            Pengurus::where('NIM', $mahasiswa->NIM)->delete();
        }

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        if ($mahasiswa->Foto && Storage::disk('public')->exists($mahasiswa->Foto)) {
            Storage::disk('public')->delete($mahasiswa->Foto);
        }

        $mahasiswa->delete();

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil dihapus.');
    }
}
