@extends('layouts.app')

@section('title', $mahasiswa->Nama_Mahasiswa . ' â€” Profil Mahasiswa')

@section('content')
@php
    $inisial = collect(explode(' ', $mahasiswa->Nama_Mahasiswa))->filter()->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->implode('');
@endphp
<div class="max-w-3xl mx-auto space-y-6">
    <section class="rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-900 to-indigo-700 text-white p-6 sm:p-8 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($mahasiswa->Foto)
                    <img src="{{ asset('storage/' . $mahasiswa->Foto) }}" alt="{{ $mahasiswa->Nama_Mahasiswa }}"
                         class="w-16 h-16 rounded-2xl object-cover border-2 border-white/30 shadow-md shrink-0">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-white/15 border border-white/25 text-white font-bold text-xl flex items-center justify-center shrink-0">
                        {{ $inisial ?: 'M' }}
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2 text-xs text-indigo-200 mb-1">
                        <a href="{{ route('mahasiswa.index') }}" class="hover:text-white transition">&larr; Data Mahasiswa</a>
                        <span>/</span>
                        <span class="font-mono">{{ $mahasiswa->NIM }}</span>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight">{{ $mahasiswa->Nama_Mahasiswa }}</h1>
                    <p class="text-xs text-indigo-100/90 mt-0.5">{{ $mahasiswa->kelas->Nama_Kelas ?? $mahasiswa->ID_Kelas }} &bull; {{ $mahasiswa->kelas->Program_Studi ?? '-' }}</p>
                </div>
            </div>

            @if($isAdmin)
                <div class="flex items-center gap-2">
                    <a href="{{ route('mahasiswa.edit', $mahasiswa->NIM) }}"
                       class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-900 text-xs font-semibold transition">
                        Edit Data
                    </a>
                </div>
            @endif
        </div>
    </section>

    <section class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4">Informasi Akademik &amp; Kepengurusan</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <dt class="text-xs text-slate-500">NIM</dt>
                <dd class="mt-1 font-bold font-mono text-slate-800">{{ $mahasiswa->NIM }}</dd>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <dt class="text-xs text-slate-500">Kelas &amp; Angkatan</dt>
                <dd class="mt-1 font-bold text-slate-800">
                    {{ $mahasiswa->kelas->Nama_Kelas ?? $mahasiswa->ID_Kelas }}
                    @if($mahasiswa->kelas?->Angkatan)
                        ({{ $mahasiswa->kelas->Angkatan }})
                    @endif
                </dd>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <dt class="text-xs text-slate-500">Peran / Jabatan</dt>
                <dd class="mt-1 font-bold text-indigo-600">
                    {{ $mahasiswa->pengurus?->jabatan?->Nama_Jabatan ?? 'Anggota Kelas' }}
                </dd>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <dt class="text-xs text-slate-500">No. WhatsApp</dt>
                <dd class="mt-1 font-semibold text-slate-800">{{ $mahasiswa->pengurus?->No_WhatsApp ?? 'â€”' }}</dd>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                <dt class="text-xs text-slate-500">Email</dt>
                <dd class="mt-1 font-semibold text-slate-800">{{ $mahasiswa->pengurus?->Email ?? 'â€”' }}</dd>
            </div>
        </dl>
    </section>
</div>
@endsection
