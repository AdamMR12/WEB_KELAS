@extends('layouts.app')

@section('title', 'Data Mahasiswa â€” Memory Wall')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-900 to-indigo-700 text-white shadow-lg">
        <div class="p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 text-xs text-indigo-200">
                    <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="hover:text-white transition">&larr; Dashboard</a>
                    <span>/</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-white/15 text-white font-medium">Data Mahasiswa</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Data Mahasiswa</h1>
                <p class="text-sm text-indigo-100/90">Daftar, cari, filter, dan kelola data anggota kelas beserta jabatan kepengurusan.</p>
            </div>

            @if($isAdmin)
                <a href="{{ route('mahasiswa.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-900 font-semibold text-sm shadow-sm transition shrink-0">
                    + Tambah Mahasiswa
                </a>
            @endif
        </div>
    </section>

    {{-- Filter & Pencarian --}}
    <section class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
        <form method="GET" action="{{ route('mahasiswa.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <div class="lg:col-span-5">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari berdasarkan NIM atau nama mahasiswa..."
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="lg:col-span-3">
                <select name="kelas" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Kelas</option>
                    @foreach($daftarKelas as $kls)
                        <option value="{{ $kls->ID_Kelas }}" {{ $kelasId == $kls->ID_Kelas ? 'selected' : '' }}>
                            {{ $kls->Nama_Kelas }} ({{ $kls->Angkatan }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <select name="jabatan" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Peran</option>
                    @foreach($daftarJabatan as $jab)
                        <option value="{{ $jab->ID_Jabatan }}" {{ $jabatanId == $jab->ID_Jabatan ? 'selected' : '' }}>
                            {{ $jab->Nama_Jabatan }}
                        </option>
                    @endforeach
                    <option value="anggota" {{ $jabatanId === 'anggota' ? 'selected' : '' }}>Anggota Biasa</option>
                </select>
            </div>
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition">
                    Filter
                </button>
                @if($search !== '' || $kelasId || $jabatanId)
                    <a href="{{ route('mahasiswa.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </section>

    {{-- Tabel Data Mahasiswa --}}
    <section class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs text-slate-500">Total ditemukan <strong class="text-slate-700">{{ $daftarMahasiswa->total() }}</strong> mahasiswa.</p>
            <a href="{{ route('laporan.index', ['jenis' => 'mahasiswa', 'kelas' => $kelasId, 'jabatan' => $jabatanId]) }}"
               class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                Export Laporan &rarr;
            </a>
        </div>

        @if($daftarMahasiswa->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-4">Mahasiswa</th>
                            <th class="py-3 px-4">NIM</th>
                            <th class="py-3 px-4">Kelas</th>
                            <th class="py-3 px-4">Jabatan</th>
                            <th class="py-3 px-4">Kontak</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($daftarMahasiswa as $mhs)
                            @php
                                $inisial = collect(explode(' ', $mhs->Nama_Mahasiswa))->filter()->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->implode('');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        @if($mhs->Foto)
                                            <img src="{{ asset('storage/' . $mhs->Foto) }}" alt="{{ $mhs->Nama_Mahasiswa }}"
                                                 class="w-9 h-9 rounded-xl object-cover border border-slate-200 shrink-0">
                                        @else
                                            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center shrink-0">
                                                {{ $inisial ?: 'M' }}
                                            </div>
                                        @endif
                                        <a href="{{ route('mahasiswa.show', $mhs->NIM) }}" class="font-semibold text-slate-800 hover:text-indigo-600 transition">
                                            {{ $mhs->Nama_Mahasiswa }}
                                        </a>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-mono text-xs text-slate-600">{{ $mhs->NIM }}</td>
                                <td class="py-3 px-4 text-xs text-slate-600">{{ $mhs->kelas->Nama_Kelas ?? $mhs->ID_Kelas }}</td>
                                <td class="py-3 px-4">
                                    @if($mhs->pengurus?->jabatan)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                            {{ $mhs->pengurus->jabatan->Nama_Jabatan }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600">Anggota</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-500">
                                    {{ $mhs->pengurus->No_WhatsApp ?? $mhs->pengurus->Email ?? 'â€”' }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('mahasiswa.show', $mhs->NIM) }}"
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition">
                                            Detail
                                        </a>
                                        @if($isAdmin)
                                            <a href="{{ route('mahasiswa.edit', $mhs->NIM) }}"
                                               class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold transition">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('mahasiswa.destroy', $mhs->NIM) }}"
                                                  onsubmit="return confirm('Hapus data mahasiswa {{ addslashes($mhs->Nama_Mahasiswa) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $daftarMahasiswa->links() }}
            </div>
        @else
            <div class="py-10 text-center">
                <p class="text-sm font-semibold text-slate-700">Data Mahasiswa Tidak Ditemukan</p>
                <p class="text-xs text-slate-500 mt-1">Coba ubah kata kunci pencarian atau tambahkan data mahasiswa baru.</p>
            </div>
        @endif
    </section>
</div>
@endsection
