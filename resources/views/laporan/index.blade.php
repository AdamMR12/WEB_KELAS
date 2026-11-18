@extends('layouts.app')

@section('title', 'Generate Laporan â€” Memory Wall')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-900 to-indigo-700 text-white shadow-lg">
        <div class="p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 text-xs text-indigo-200">
                    <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="hover:text-white transition">&larr; Dashboard</a>
                    <span>/</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-white/15 text-white font-medium">Generate Laporan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Generate Laporan Kelas &amp; Mahasiswa</h1>
                <p class="text-sm text-indigo-100/90">Filter data, lihat pratinjau tabel, lalu export ke PDF, Excel, atau cetak langsung.</p>
            </div>
        </div>
    </section>

    {{-- Panel Filter & Tombol Export --}}
    <section class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
        <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            <div class="lg:col-span-3">
                <label for="jenis" class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Laporan</label>
                <select id="jenis" name="jenis" onchange="this.form.submit()"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="mahasiswa" {{ $jenis === 'mahasiswa' ? 'selected' : '' }}>Data Mahasiswa</option>
                    <option value="kelas" {{ $jenis === 'kelas' ? 'selected' : '' }}>Profil Kelas</option>
                </select>
            </div>

            @if($jenis === 'mahasiswa')
                <div class="lg:col-span-3">
                    <label for="kelas" class="block text-xs font-semibold text-slate-700 mb-1.5">Filter Kelas</label>
                    <select id="kelas" name="kelas"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Kelas</option>
                        @foreach($daftarKelas as $kls)
                            <option value="{{ $kls->ID_Kelas }}" {{ $kelasId == $kls->ID_Kelas ? 'selected' : '' }}>
                                {{ $kls->Nama_Kelas }} ({{ $kls->Angkatan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <label for="jabatan" class="block text-xs font-semibold text-slate-700 mb-1.5">Filter Jabatan</label>
                    <select id="jabatan" name="jabatan"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Jabatan</option>
                        @foreach($daftarJabatan as $jab)
                            <option value="{{ $jab->ID_Jabatan }}" {{ $jabatanId == $jab->ID_Jabatan ? 'selected' : '' }}>
                                {{ $jab->Nama_Jabatan }}
                            </option>
                        @endforeach
                        <option value="anggota" {{ $jabatanId === 'anggota' ? 'selected' : '' }}>Anggota Biasa</option>
                    </select>
                </div>

                <div class="lg:col-span-3 flex items-center gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition">
                        Terapkan Filter
                    </button>
                    @if($kelasId || $jabatanId)
                        <a href="{{ route('laporan.index', ['jenis' => 'mahasiswa']) }}"
                           class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium">
                            Reset
                        </a>
                    @endif
                </div>
            @endif
        </form>

        {{-- Tombol Export PDF / Excel / Cetak --}}
        <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs text-slate-500">
                Pratinjau siap diunduh:
                <strong class="text-slate-700">
                    {{ $jenis === 'kelas' ? $daftarKelas->count() . ' Profil Kelas' : $dataMahasiswa->count() . ' Data Mahasiswa' }}
                </strong>
            </p>

            <div class="flex flex-wrap items-center gap-2.5">
                @if($jenis === 'mahasiswa')
                    <a href="{{ route('laporan.mahasiswa.pdf', ['kelas' => $kelasId, 'jabatan' => $jabatanId]) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition">
                        Export PDF
                    </a>
                    <a href="{{ route('laporan.mahasiswa.excel', ['kelas' => $kelasId, 'jabatan' => $jabatanId]) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                        Export Excel
                    </a>
                @else
                    <a href="{{ route('laporan.kelas.pdf') }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition">
                        Export PDF Profil Kelas
                    </a>
                @endif
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                    Cetak Halaman
                </button>
            </div>
        </div>
    </section>

    {{-- Pratinjau Laporan --}}
    <section class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800">
                    Pratinjau Laporan â€” {{ $jenis === 'kelas' ? 'Profil Kelas' : 'Data Mahasiswa' }}
                </h2>
                <p class="text-xs text-slate-500">Dicetak pada {{ now()->format('d M Y, H:i') }} WIB</p>
            </div>
        </div>

        @if($jenis === 'kelas')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">ID Kelas</th>
                            <th class="py-3 px-4">Nama Kelas</th>
                            <th class="py-3 px-4">Program Studi</th>
                            <th class="py-3 px-4">Angkatan</th>
                            <th class="py-3 px-4 text-right">Jumlah Mahasiswa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($daftarKelas as $idx => $kls)
                            <tr>
                                <td class="py-3 px-4 text-xs text-slate-500">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 font-mono text-xs text-slate-700">{{ $kls->ID_Kelas }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $kls->Nama_Kelas }}</td>
                                <td class="py-3 px-4 text-xs text-slate-600">{{ $kls->Program_Studi }}</td>
                                <td class="py-3 px-4 text-xs text-slate-600">{{ $kls->Angkatan }}</td>
                                <td class="py-3 px-4 text-right font-bold text-indigo-600">{{ $kls->mahasiswa_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-xs text-slate-500">Belum ada data profil kelas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">NIM</th>
                            <th class="py-3 px-4">Nama Mahasiswa</th>
                            <th class="py-3 px-4">Kelas</th>
                            <th class="py-3 px-4">Jabatan</th>
                            <th class="py-3 px-4">No. WhatsApp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($dataMahasiswa as $idx => $mhs)
                            <tr>
                                <td class="py-3 px-4 text-xs text-slate-500">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 font-mono text-xs text-slate-700">{{ $mhs->NIM }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $mhs->Nama_Mahasiswa }}</td>
                                <td class="py-3 px-4 text-xs text-slate-600">{{ $mhs->kelas->Nama_Kelas ?? $mhs->ID_Kelas }}</td>
                                <td class="py-3 px-4 text-xs font-medium text-indigo-700">
                                    {{ $mhs->pengurus?->jabatan?->Nama_Jabatan ?? 'Anggota' }}
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-500">{{ $mhs->pengurus->No_WhatsApp ?? 'â€”' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-xs text-slate-500">Tidak ada data mahasiswa sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
