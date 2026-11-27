<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\JadwalMk;
use App\Models\MataKuliah;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $todayDate = $today->toDateString();
        $selectedDate = $request->query('tanggal', $todayDate);

        $showPengganti = $request->boolean('show_pengganti', true);
        $hideNonAkademik = $request->boolean('hide_non_akademik', false);

        $selectedCarbon = Carbon::parse($selectedDate)->locale('id');
        $dayName = $selectedCarbon->translatedFormat('l');
        $formattedDate = $selectedCarbon->translatedFormat('l, d F Y');
        $monthName = $selectedCarbon->translatedFormat('F Y');
        $currentMonthYear = $selectedCarbon->format('Y-m');

        // Jadwal reguler tampil berulang tiap hari yang sama; jadwal pengganti hanya pada tanggal spesifiknya
        $query = JadwalMk::with(['mataKuliah', 'dosenPengampu', 'ruanganLokasi'])
            ->where(function ($q) use ($dayName, $selectedDate) {
                $q->where(function ($reg) use ($dayName) {
                    $reg->where('is_pengganti', false)->where('hari', $dayName);
                })->orWhere(function ($sub) use ($selectedDate) {
                    $sub->where('is_pengganti', true)->whereDate('tanggal', $selectedDate);
                });
            });

        if (!$showPengganti) {
            $query->where('is_pengganti', false);
        }

        $schedules = $query->orderBy('jam_mulai')->get();

        $regularSchedules = $schedules->where('is_pengganti', false);
        $bebanSks = $regularSchedules->sum('sks');
        $totalSesi = $regularSchedules->count();
        $penggantiCount = JadwalMk::where('is_pengganti', true)->count();

        $startDayOfWeek = $selectedCarbon->copy()->startOfMonth()->dayOfWeekIso;
        $totalDaysInMonth = $selectedCarbon->daysInMonth;

        $prevDayDate = $selectedCarbon->copy()->subDay()->toDateString();
        $nextDayDate = $selectedCarbon->copy()->addDay()->toDateString();
        $prevMonthDate = $selectedCarbon->copy()->subMonth()->startOfMonth()->toDateString();
        $nextMonthDate = $selectedCarbon->copy()->addMonth()->startOfMonth()->toDateString();

        return view('jadwal.index', compact(
            'schedules',
            'bebanSks',
            'totalSesi',
            'penggantiCount',
            'selectedDate',
            'todayDate',
            'formattedDate',
            'monthName',
            'currentMonthYear',
            'showPengganti',
            'hideNonAkademik',
            'startDayOfWeek',
            'totalDaysInMonth',
            'prevDayDate',
            'nextDayDate',
            'prevMonthDate',
            'nextMonthDate'
        ));
    }

    public function create(Request $request)
    {
        $todayDate = Carbon::today()->toDateString();
        $selectedDate = $request->query('tanggal', $todayDate);
        $selectedCarbon = Carbon::parse($selectedDate)->locale('id');
        $dayName = $selectedCarbon->translatedFormat('l');

        $mataKuliahList = MataKuliah::withCount('jadwalMk')->orderBy('nama_mk')->get();
        $dosenList = Dosen::withCount('jadwalMk')->orderBy('nama')->get();
        $ruanganList = Ruangan::orderBy('nama_ruangan')->get();

        return view('jadwal.create', compact(
            'selectedDate',
            'dayName',
            'mataKuliahList',
            'dosenList',
            'ruanganList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'nullable|string|max:20',
            'nama_matakuliah' => 'required|string|max:100',
            'sks' => 'required|integer|min:1|max:6',
            'tipe_sks' => 'nullable|string|max:50',
            'hari' => 'required|string|max:20',
            'tanggal' => 'required|date',
            'jam_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'jam_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'ruang' => 'required|string|max:100',
            'dosen' => 'required|string|max:100',
            'nip' => 'nullable|string|max:30',
            'jenis' => 'nullable|string|max:50',
            'metode' => 'nullable|string|max:50',
            'warna_bg' => 'nullable|string|max:20',
            'is_pengganti' => 'nullable|boolean',
            'catatan_pekan' => 'nullable|string|max:150',
        ]);

        $kodeMk = trim($validated['kode_mk'] ?? '');
        $mk = MataKuliah::firstOrCreate(
            ['nama_mk' => trim($validated['nama_matakuliah'])],
            [
                'kode_mk' => $kodeMk !== '' ? $kodeMk : '-',
                'sks' => (int) $validated['sks'],
            ]
        );
        $mkUpdates = [];
        if ($mk->sks !== (int) $validated['sks']) {
            $mkUpdates['sks'] = (int) $validated['sks'];
        }
        if ($kodeMk !== '' && $mk->kode_mk !== $kodeMk) {
            $mkUpdates['kode_mk'] = $kodeMk;
        }
        if ($mkUpdates !== []) {
            $mk->update($mkUpdates);
        }

        $nip = trim($validated['nip'] ?? '');
        $dosen = Dosen::firstOrCreate(
            ['nama' => trim($validated['dosen'])],
            ['nip' => $nip !== '' ? $nip : null]
        );
        if ($nip !== '' && $dosen->nip !== $nip) {
            $dosen->update(['nip' => $nip]);
        }

        $ruangan = Ruangan::firstOrCreate([
            'nama_ruangan' => trim($validated['ruang']),
        ]);

        JadwalMk::create([
            'id_mk' => $mk->id_mk,
            'id_dosen' => $dosen->id_dosen,
            'id_ruangan' => $ruangan->id_ruangan,
            'hari' => $validated['hari'] ?: Carbon::parse($validated['tanggal'])->locale('id')->translatedFormat('l'),
            'tanggal' => $validated['tanggal'],
            'jam_mulai' => strlen($validated['jam_mulai']) === 5 ? $validated['jam_mulai'] . ':00' : $validated['jam_mulai'],
            'jam_selesai' => strlen($validated['jam_selesai']) === 5 ? $validated['jam_selesai'] . ':00' : $validated['jam_selesai'],
            'tipe_sks' => $validated['tipe_sks'] ?? $validated['metode'] ?? 'Teori',
            'jenis' => $validated['jenis'] ?? 'Mata Kuliah Wajib',
            'warna_bg' => $validated['warna_bg'] ?? 'lime',
            'is_pengganti' => $request->boolean('is_pengganti'),
            'catatan_pekan' => $validated['catatan_pekan'] ?? null,
        ]);

        return redirect()->route('jadwal.index', ['tanggal' => $validated['tanggal']])
            ->with('success', 'Jadwal kuliah berhasil ditambahkan!');
    }

    public function destroy(JadwalMk $jadwalMk)
    {
        $tanggal = $jadwalMk->tanggal ? $jadwalMk->tanggal->format('Y-m-d') : Carbon::today()->toDateString();
        $jadwalMk->delete();

        return redirect()->route('jadwal.index', ['tanggal' => $tanggal])
            ->with('success', 'Jadwal berhasil dihapus!');
    }

    public function destroyMataKuliah(MataKuliah $mataKuliah)
    {
        if ($mataKuliah->jadwalMk()->exists()) {
            return back()->with('error', 'Mata kuliah masih dipakai pada jadwal. Hapus jadwal terkait terlebih dahulu.');
        }

        $mataKuliah->delete();

        return back()->with('success', 'Data mata kuliah berhasil dihapus.');
    }

    public function destroyDosen(Dosen $dosen)
    {
        if ($dosen->jadwalMk()->exists()) {
            return back()->with('error', 'Dosen masih dipakai pada jadwal. Hapus jadwal terkait terlebih dahulu.');
        }

        $dosen->delete();

        return back()->with('success', 'Data dosen berhasil dihapus.');
    }
}
