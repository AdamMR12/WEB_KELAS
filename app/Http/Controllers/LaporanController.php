<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $jenis     = $request->query('jenis', 'mahasiswa');
        $kelasId   = $request->query('kelas');
        $jabatanId = $request->query('jabatan');

        $daftarKelas   = Kelas::withCount('mahasiswa')->orderBy('Nama_Kelas')->get();
        $daftarJabatan = Jabatan::orderBy('ID_Jabatan')->get();

        $dataMahasiswa = Mahasiswa::with(['kelas', 'pengurus.jabatan'])
            ->when($kelasId, fn ($q) => $q->where('ID_Kelas', $kelasId))
            ->when($jabatanId, function ($q) use ($jabatanId) {
                if ($jabatanId === 'anggota') {
                    $q->doesntHave('pengurus');
                } else {
                    $q->whereHas('pengurus', fn ($sub) => $sub->where('ID_Jabatan', $jabatanId));
                }
            })
            ->orderBy('Nama_Mahasiswa')
            ->get();

        return view('laporan.index', [
            'jenis'         => $jenis,
            'kelasId'       => $kelasId,
            'jabatanId'     => $jabatanId,
            'daftarKelas'   => $daftarKelas,
            'daftarJabatan' => $daftarJabatan,
            'dataMahasiswa' => $dataMahasiswa,
        ]);
    }

    public function mahasiswaPdf(Request $request)
    {
        $kelasId   = $request->query('kelas');
        $jabatanId = $request->query('jabatan');

        $dataMahasiswa = Mahasiswa::with(['kelas', 'pengurus.jabatan'])
            ->when($kelasId, fn ($q) => $q->where('ID_Kelas', $kelasId))
            ->when($jabatanId, function ($q) use ($jabatanId) {
                if ($jabatanId === 'anggota') {
                    $q->doesntHave('pengurus');
                } else {
                    $q->whereHas('pengurus', fn ($sub) => $sub->where('ID_Jabatan', $jabatanId));
                }
            })
            ->orderBy('Nama_Mahasiswa')
            ->get();

        $kelasDipilih = $kelasId ? Kelas::find($kelasId) : null;

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf-mahasiswa', [
                'dataMahasiswa' => $dataMahasiswa,
                'kelasDipilih'  => $kelasDipilih,
                'autoPrint'     => false,
            ]);
            return $pdf->download('Laporan_Mahasiswa_' . date('Ymd_His') . '.pdf');
        }

        return view('laporan.pdf-mahasiswa', [
            'dataMahasiswa' => $dataMahasiswa,
            'kelasDipilih'  => $kelasDipilih,
            'autoPrint'     => true,
        ]);
    }

    public function mahasiswaExcel(Request $request)
    {
        $kelasId   = $request->query('kelas');
        $jabatanId = $request->query('jabatan');

        $dataMahasiswa = Mahasiswa::with(['kelas', 'pengurus.jabatan'])
            ->when($kelasId, fn ($q) => $q->where('ID_Kelas', $kelasId))
            ->when($jabatanId, function ($q) use ($jabatanId) {
                if ($jabatanId === 'anggota') {
                    $q->doesntHave('pengurus');
                } else {
                    $q->whereHas('pengurus', fn ($sub) => $sub->where('ID_Jabatan', $jabatanId));
                }
            })
            ->orderBy('Nama_Mahasiswa')
            ->get();

        $filename = 'Laporan_Mahasiswa_' . date('Ymd_His') . '.xls';

        return response()
            ->view('laporan.excel-mahasiswa', [
                'dataMahasiswa' => $dataMahasiswa,
            ])
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function kelasPdf()
    {
        $daftarKelas = Kelas::withCount('mahasiswa')
            ->with(['mahasiswa.pengurus.jabatan'])
            ->orderByDesc('Angkatan')
            ->orderBy('Nama_Kelas')
            ->get();

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf-kelas', [
                'daftarKelas' => $daftarKelas,
                'autoPrint'   => false,
            ]);
            return $pdf->download('Laporan_Profil_Kelas_' . date('Ymd_His') . '.pdf');
        }

        return view('laporan.pdf-kelas', [
            'daftarKelas' => $daftarKelas,
            'autoPrint'   => true,
        ]);
    }
}
