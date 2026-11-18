@extends('layouts.app')

@section('title', 'Tambah Profil Kelas â€” Memory Wall')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <section class="rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-900 to-indigo-700 text-white p-6 sm:p-8 shadow-lg">
        <div class="flex items-center gap-2 text-xs text-indigo-200 mb-2">
            <a href="{{ route('kelas.index') }}" class="hover:text-white transition">&larr; Kembali ke Profil Kelas</a>
            <span>/</span>
            <span class="px-2.5 py-0.5 rounded-full bg-white/15 text-white font-medium">Tambah Kelas</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight">Tambah Profil Kelas Baru</h1>
        <p class="text-sm text-indigo-100/90 mt-1">Lengkapi identitas kode kelas, nama kelas, program studi, dan tahun angkatan.</p>
    </section>

    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('kelas.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="ID_Kelas" class="block text-xs font-semibold text-slate-700 mb-1.5">Kode / ID Kelas <span class="text-red-500">*</span></label>
                <input type="text" id="ID_Kelas" name="ID_Kelas" value="{{ old('ID_Kelas') }}" required maxlength="10"
                       placeholder="Contoh: SI2A"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-[11px] text-slate-400 mt-1">Maksimal 10 karakter, unik untuk setiap kelas.</p>
            </div>

            <div>
                <label for="Nama_Kelas" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Kelas <span class="text-red-500">*</span></label>
                <input type="text" id="Nama_Kelas" name="Nama_Kelas" value="{{ old('Nama_Kelas') }}" required maxlength="50"
                       placeholder="Contoh: SI 2A"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="Program_Studi" class="block text-xs font-semibold text-slate-700 mb-1.5">Program Studi <span class="text-red-500">*</span></label>
                <input type="text" id="Program_Studi" name="Program_Studi" value="{{ old('Program_Studi', 'D3 Sistem Informasi') }}" required maxlength="100"
                       placeholder="Contoh: D3 Sistem Informasi"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="Angkatan" class="block text-xs font-semibold text-slate-700 mb-1.5">Tahun Angkatan <span class="text-red-500">*</span></label>
                <input type="number" id="Angkatan" name="Angkatan" value="{{ old('Angkatan', date('Y')) }}" required min="2000" max="{{ date('Y') + 2 }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('kelas.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                    Simpan Profil Kelas
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
