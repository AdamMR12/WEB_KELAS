@extends('layouts.app')

@section('title', 'Detail Kelas ' . $kelas->Nama_Kelas . ' â€” Memory Wall')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-900 to-indigo-700 text-white p-6 sm:p-8 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 text-xs text-indigo-200">
                    <a href="{{ route('kelas.index') }}" class="hover:text-white transition">&larr; Kembali ke Profil Kelas</a>
                    <span>/</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-white/15 text-white font-medium">{{ $kelas->ID_Kelas }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Profil Kelas {{ $kelas->Nama_Kelas }}</h1>
                <p class="text-sm text-indigo-100/90">{{ $kelas->Program_Studi }} &bull; Angkatan {{ $kelas->Angkatan }}</p>
            </div>

            @if($isAdmin)
                <div class="flex items-center gap-2">
                    <a href="{{ route('kelas.edit', $kelas->ID_Kelas) }}"
                       class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-900 text-xs font-semibold transition">
                        Edit Kelas
                    </a>
                    <a href="{{ route('mahasiswa.create') }}"
                       class="px-4 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-semibold transition">
                        + Tambah Mahasiswa
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- Ringkasan Identitas Kelas --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <p class="text-xs font-medium text-slate-400 uppercase">Kode Kelas</p>
            <p class="mt-1 text-lg font-bold text-slate-800 font-mono">{{ $kelas->ID_Kelas }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <p class="text-xs font-medium text-slate-400 uppercase">Nama Kelas</p>
            <p class="mt-1 text-lg font-bold text-slate-800">{{ $kelas->Nama_Kelas }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <p class="text-xs font-medium text-slate-400 uppercase">Program Studi</p>
            <p class="mt-1 text-base font-bold text-slate-800">{{ $kelas->Program_Studi }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <p class="text-xs font-medium text-slate-400 uppercase">Angkatan / Anggota</p>
            <p class="mt-1 text-lg font-bold text-indigo-600">{{ $kelas->Angkatan }} &bull; {{ $kelas->mahasiswa->count() }} Mhs</p>
        </div>
    </section>

    {{-- Daftar Mahasiswa di Kelas Ini --}}
    <section class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-800">Anggota Kelas {{ $kelas->Nama_Kelas }}</h2>
                <p class="text-xs text-slate-500">Daftar mahasiswa terdaftar pada kelas ini</p>
            </div>
            <a href="{{ route('struktur-kelas.index', ['kelas' => $kelas->ID_Kelas]) }}"
               class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                Lihat Struktur Pengurus &rarr;
            </a>
        </div>

        @if($kelas->mahasiswa->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">NIM</th>
                            <th class="py-3 px-4">Nama Mahasiswa</th>
                            <th class="py-3 px-4">Jabatan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($kelas->mahasiswa as $idx => $mhs)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 text-xs text-slate-500">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 font-mono text-xs text-slate-700">{{ $mhs->NIM }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $mhs->Nama_Mahasiswa }}</td>
                                <td class="py-3 px-4">
                                    @if($mhs->pengurus?->jabatan)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                            {{ $mhs->pengurus->jabatan->Nama_Jabatan }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600">Anggota</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('mahasiswa.show', $mhs->NIM) }}" class="text-xs font-semibold text-indigo-600 hover:underline">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-xs text-slate-500 py-6 text-center">Belum ada mahasiswa yang terdaftar di kelas ini.</p>
        @endif
    </section>
</div>
@endsection
