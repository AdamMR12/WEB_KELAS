<?php

namespace App\Http\Controllers;

use App\Models\AgendaCategory;
use App\Models\AgendaNonAkademik;
use App\Models\AgendaStatus;
use App\Models\JadwalAgenda;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $kategori = strtoupper($request->query('kategori', 'SEMUA'));

        $categories = AgendaCategory::all();
        $statuses = AgendaStatus::all();

        $query = JadwalAgenda::with(['agendaNonAkademik', 'ruanganLokasi']);

        if ($kategori !== 'SEMUA') {
            $query->whereHas('agendaNonAkademik', fn ($q) => $q->where('kategori', $kategori));
        }

        $agendas = $query->orderBy('tanggal')->orderBy('jam_mulai')->get();

        $totalCount = JadwalAgenda::count();
        $categoryCounts = JadwalAgenda::join('agenda_non_akademik', 'jadwal_agenda.id_agenda', '=', 'agenda_non_akademik.id_agenda')
            ->selectRaw('agenda_non_akademik.kategori, count(*) as count')
            ->groupBy('agenda_non_akademik.kategori')
            ->pluck('count', 'kategori');

        $nearest = JadwalAgenda::whereDate('tanggal', '>=', Carbon::today())
            ->orderBy('tanggal')
            ->first() ?? JadwalAgenda::orderBy('tanggal')->first();

        $nearestDate = $nearest && $nearest->tanggal
            ? Carbon::parse($nearest->tanggal)->locale('id')->translatedFormat('d M Y')
            : Carbon::today()->locale('id')->translatedFormat('d M Y');

        return view('agenda.index', compact(
            'agendas',
            'categories',
            'statuses',
            'categoryCounts',
            'kategori',
            'totalCount',
            'nearestDate'
        ));
    }

    public function create(Request $request)
    {
        $today = Carbon::today();
        $defaultDate = $today->toDateString();
        $defaultDisplay = $today->locale('id')->translatedFormat('l, d M Y');

        $categories = AgendaCategory::all();
        $statuses = AgendaStatus::all();
        $ruanganList = Ruangan::orderBy('nama_ruangan')->get();

        $firstCatKode = $categories->first()?->kode ?? 'ORGANISASI';
        $selectedKategori = strtoupper($request->query('kategori', $firstCatKode));

        return view('agenda.create', compact(
            'defaultDate',
            'defaultDisplay',
            'selectedKategori',
            'categories',
            'statuses',
            'ruanganList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'kategori' => 'required|string|max:100',
            'status' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tanggal_display' => 'nullable|string|max:100',
            'waktu' => 'required|string|max:100',
            'lokasi' => 'required|string|max:100',
            'koordinator' => 'nullable|string|max:150',
            'meta_label' => 'nullable|string|max:100',
            'meta_value' => 'nullable|string|max:150',
            'deskripsi' => 'nullable|string',
        ]);

        if (empty($validated['tanggal_display'])) {
            $mulai = Carbon::parse($validated['tanggal_mulai'])->locale('id');
            if (!empty($validated['tanggal_selesai'])) {
                $selesai = Carbon::parse($validated['tanggal_selesai'])->locale('id');
                $validated['tanggal_display'] = $mulai->translatedFormat('d') . ' - ' . $selesai->translatedFormat('d M Y');
            } else {
                $validated['tanggal_display'] = $mulai->translatedFormat('l, d M Y');
            }
        }

        // Ekstrak jam_mulai & jam_selesai dari teks waktu (misal "15:30 WIB" atau "09:00 - 15:00 WIB")
        preg_match_all('/\b(\d{1,2})[:.](\d{2})\b/', $validated['waktu'], $matches, PREG_SET_ORDER);
        $jamMulai = isset($matches[0])
            ? sprintf('%02d:%02d:00', min(23, (int) $matches[0][1]), min(59, (int) $matches[0][2]))
            : '08:00:00';
        $jamSelesai = isset($matches[1])
            ? sprintf('%02d:%02d:00', min(23, (int) $matches[1][1]), min(59, (int) $matches[1][2]))
            : null;

        $lokasiClean = trim($validated['lokasi']);
        $isOnline = in_array(strtolower($lokasiClean), ['daring', 'online', 'daring / online', '-'], true);
        $ruangan = $isOnline ? null : Ruangan::firstOrCreate(['nama_ruangan' => $lokasiClean]);

        $agendaMaster = AgendaNonAkademik::create([
            'nama_agenda' => trim($validated['judul']),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'kategori' => $validated['kategori'],
            'status' => $validated['status'],
            'koordinator' => $validated['koordinator'] ?? null,
            'meta_label' => $validated['meta_label'] ?? null,
            'meta_value' => $validated['meta_value'] ?? null,
        ]);

        JadwalAgenda::create([
            'id_agenda' => $agendaMaster->id_agenda,
            'id_ruangan' => $ruangan?->id_ruangan,
            'tanggal' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'tanggal_display' => $validated['tanggal_display'],
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'waktu_label' => $validated['waktu'],
        ]);

        return redirect()->route('agenda.index', ['kategori' => $validated['kategori']])
            ->with('success', 'Agenda non-akademik berhasil ditambahkan!');
    }

    public function destroy(JadwalAgenda $jadwalAgenda)
    {
        $agendaMaster = $jadwalAgenda->agendaNonAkademik;
        $jadwalAgenda->delete();

        if ($agendaMaster && $agendaMaster->jadwalAgenda()->count() === 0) {
            $agendaMaster->delete();
        }

        return redirect()->route('agenda.index')
            ->with('success', 'Agenda berhasil dihapus!');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:50',
            'warna_bg' => 'nullable|string|in:amber,lime,cyan,rose,purple,emerald,navy',
        ]);

        $rawKode = !empty($validated['kode']) ? $validated['kode'] : $validated['nama'];
        $cleanKode = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-'], '_', $rawKode)));

        if (empty($cleanKode)) {
            $cleanKode = 'CAT_' . time();
        }

        if (AgendaCategory::where('kode', $cleanKode)->exists()) {
            return back()->with('error', "Kode kategori '{$cleanKode}' sudah digunakan!");
        }

        AgendaCategory::create([
            'nama' => $validated['nama'],
            'kode' => $cleanKode,
            'warna_bg' => $validated['warna_bg'] ?? 'amber',
        ]);

        return back()->with('success', "Kategori '{$validated['nama']}' berhasil ditambahkan!");
    }

    public function destroyCategory(AgendaCategory $category)
    {
        $nama = $category->nama;
        $category->delete();

        return back()->with('success', "Kategori '{$nama}' berhasil dihapus!");
    }

    public function storeStatus(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:agenda_statuses,nama',
        ]);

        AgendaStatus::create($validated);

        return back()->with('success', "Status '{$validated['nama']}' berhasil ditambahkan!");
    }

    public function destroyStatus(AgendaStatus $status)
    {
        $nama = $status->nama;
        $status->delete();

        return back()->with('success', "Status '{$nama}' berhasil dihapus!");
    }
}
