<?php

namespace App\Http\Controllers\Kas;

use App\Models\Anggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnggotaController
{
    public function index(Request $request): View
    {
        $q = $request->query('q');

        $anggota = Anggota::cari($q)->orderBy('nama')->get();

        return view('kas.anggota.index', [
            'anggota' => $anggota,
            'total'   => Anggota::count(),
            'q'       => $q,
            'edit'    => $request->filled('edit') ? Anggota::find($request->query('edit')) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nim'   => ['required', 'string', 'max:15', 'unique:anggota,nim'],
            'nama'  => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:15'],
        ]);

        Anggota::create($data + ['status' => 'aktif']);

        return redirect()->route('kas.anggota.index')->with('sukses', 'Anggota ditambahkan.');
    }

    public function update(Request $request, Anggota $anggota): RedirectResponse
    {
        $data = $request->validate([
            'nim'    => ['required', 'string', 'max:15', Rule::unique('anggota', 'nim')->ignore($anggota->id_anggota, 'id_anggota')],
            'nama'   => ['required', 'string', 'max:100'],
            'no_hp'  => ['nullable', 'string', 'max:15'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);

        $anggota->update($data);

        return redirect()->route('kas.anggota.index')->with('sukses', 'Data anggota diubah.');
    }

    /** Anggota tidak dihapus, hanya dinonaktifkan agar riwayat kas tetap utuh. */
    public function nonaktifkan(Anggota $anggota): RedirectResponse
    {
        $anggota->update(['status' => 'nonaktif']);

        return redirect()->route('kas.anggota.index')
            ->with('sukses', $anggota->nama . ' dinonaktifkan. Riwayat pembayarannya tetap tersimpan.');
    }
}
