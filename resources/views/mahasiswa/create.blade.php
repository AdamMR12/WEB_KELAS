@extends('layouts.app')

@section('title', 'Tambah Mahasiswa â€” Memory Wall')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <section class="rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-900 to-indigo-700 text-white p-6 sm:p-8 shadow-lg">
        <div class="flex items-center gap-2 text-xs text-indigo-200 mb-2">
            <a href="{{ route('mahasiswa.index') }}" class="hover:text-white transition">&larr; Kembali ke Data Mahasiswa</a>
            <span>/</span>
            <span class="px-2.5 py-0.5 rounded-full bg-white/15 text-white font-medium">Tambah Mahasiswa</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight">Tambah Data Mahasiswa</h1>
        <p class="text-sm text-indigo-100/90 mt-1">Daftarkan anggota kelas baru beserta foto dan peran kepengurusan (opsional).</p>
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
        <form method="POST" action="{{ route('mahasiswa.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="NIM" class="block text-xs font-semibold text-slate-700 mb-1.5">NIM <span class="text-red-500">*</span></label>
                    <input type="text" id="NIM" name="NIM" value="{{ old('NIM') }}" required maxlength="15"
                           placeholder="Contoh: 10112001"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="ID_Kelas" class="block text-xs font-semibold text-slate-700 mb-1.5">Kelas <span class="text-red-500">*</span></label>
                    <select id="ID_Kelas" name="ID_Kelas" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($daftarKelas as $kls)
                            <option value="{{ $kls->ID_Kelas }}" {{ old('ID_Kelas') == $kls->ID_Kelas ? 'selected' : '' }}>
                                {{ $kls->Nama_Kelas }} ({{ $kls->Program_Studi }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="Nama_Mahasiswa" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap Mahasiswa <span class="text-red-500">*</span></label>
                <input type="text" id="Nama_Mahasiswa" name="Nama_Mahasiswa" value="{{ old('Nama_Mahasiswa') }}" required maxlength="100"
                       placeholder="Masukkan nama lengkap mahasiswa"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="Foto" class="block text-xs font-semibold text-slate-700 mb-1.5">Foto Profil (Opsional)</label>
                <input type="file" id="Foto" name="Foto" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="text-[11px] text-slate-400 mt-1">Format JPG, PNG, atau WebP maksimal 2 MB.</p>
            </div>

            <div class="pt-4 border-t border-slate-100 space-y-4">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kepengurusan &amp; Kontak (Opsional)</p>

                <div>
                    <label for="ID_Jabatan" class="block text-xs font-semibold text-slate-700 mb-1.5">Jabatan Pengurus</label>
                    <select id="ID_Jabatan" name="ID_Jabatan"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Anggota Biasa (Bukan Pengurus)</option>
                        @foreach($daftarJabatan as $jab)
                            <option value="{{ $jab->ID_Jabatan }}" {{ old('ID_Jabatan') == $jab->ID_Jabatan ? 'selected' : '' }}>
                                {{ $jab->Nama_Jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="No_WhatsApp" class="block text-xs font-semibold text-slate-700 mb-1.5">No. WhatsApp</label>
                        <input type="text" id="No_WhatsApp" name="No_WhatsApp" value="{{ old('No_WhatsApp') }}" maxlength="15"
                               placeholder="081234567890"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="Email" class="block text-xs font-semibold text-slate-700 mb-1.5">Email</label>
                        <input type="email" id="Email" name="Email" value="{{ old('Email') }}" maxlength="100"
                               placeholder="mahasiswa@student.polsub.ac.id"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('mahasiswa.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                    Simpan Data Mahasiswa
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
