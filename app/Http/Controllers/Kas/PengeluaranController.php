<?php

namespace App\Http\Controllers\Kas;

use App\Models\Pengeluaran;
use App\Services\KasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengeluaranController
{
    public function __construct(private KasService $kas)
    {
    }

    public function index(Request $request): View
    {
        return view('kas.pengeluaran.index', [
            'pengeluaran' => Pengeluaran::with('pencatat')
                ->orderByDesc('tanggal')
                ->orderByDesc('id_pengeluaran')
                ->get(),
            'saldo'       => $this->kas->saldo(),
            'edit'        => $request->filled('edit') ? Pengeluaran::find($request->query('edit')) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);

        if (! $this->kas->bolehKeluar($data['nominal'])) {
            return back()->withInput()->withErrors([
                'nominal' => 'Pengeluaran melebihi saldo kas yang tersedia (Rp ' . number_format($this->kas->saldo(), 0, ',', '.') . ').',
            ]);
        }

        // Pencatat selalu akun yang sedang login, bukan dari input form.
        Pengeluaran::create($data + ['id_user' => Auth::guard('kas')->id()]);

        return redirect()->route('kas.pengeluaran.index')->with('sukses', 'Pengeluaran dicatat.');
    }

    public function update(Request $request, Pengeluaran $pengeluaran): RedirectResponse
    {
        $data = $this->validasi($request);

        // Saldo dihitung tanpa nominal lama milik baris ini.
        if (! $this->kas->bolehKeluar($data['nominal'], $pengeluaran->id_pengeluaran)) {
            return back()->withInput()->withErrors([
                'nominal' => 'Pengeluaran melebihi saldo kas yang tersedia.',
            ]);
        }

        $pengeluaran->update($data);

        return redirect()->route('kas.pengeluaran.index')->with('sukses', 'Pengeluaran diubah.');
    }

    /** Hanya untuk koreksi salah input; saldo otomatis dihitung ulang. */
    public function destroy(Pengeluaran $pengeluaran): RedirectResponse
    {
        $pengeluaran->delete();

        return redirect()->route('kas.pengeluaran.index')
            ->with('sukses', 'Catatan pengeluaran dihapus. Saldo kas dihitung ulang.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'tanggal'    => ['required', 'date'],
            'keterangan' => ['required', 'string', 'max:255'],
            'nominal'    => ['required', 'integer', 'min:1', 'max:4294967295'],
        ], [
            'nominal.min' => 'Nominal harus lebih dari 0.',
        ]);
    }
}
