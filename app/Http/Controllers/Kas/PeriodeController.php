<?php

namespace App\Http\Controllers\Kas;

use App\Models\Periode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeriodeController
{
    public function index(Request $request): View
    {
        return view('kas.periode.index', [
            'periode' => Periode::withCount('pembayaran')->orderByDesc('jatuh_tempo')->get(),
            'edit'    => $request->filled('edit') ? Periode::find($request->query('edit')) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Periode::create($this->validasi($request));

        return redirect()->route('kas.periode.index')->with('sukses', 'Periode iuran ditambahkan.');
    }

    public function update(Request $request, Periode $periode): RedirectResponse
    {
        $periode->update($this->validasi($request));

        return redirect()->route('kas.periode.index')->with('sukses', 'Periode iuran diubah.');
    }

    public function destroy(Periode $periode): RedirectResponse
    {
        if ($periode->pembayaran()->exists()) {
            return redirect()->route('kas.periode.index')
                ->with('galat', 'Periode yang sudah punya pembayaran tidak bisa dihapus.');
        }

        $periode->delete();

        return redirect()->route('kas.periode.index')->with('sukses', 'Periode iuran dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_periode'  => ['required', 'string', 'max:100'],
            'nominal_iuran' => ['required', 'integer', 'min:1', 'max:4294967295'],
            'jatuh_tempo'   => ['required', 'date'],
        ], [
            'nominal_iuran.min' => 'Nominal iuran harus lebih dari 0.',
        ]);
    }
}
