@extends('layouts.jadwal')

@section('title', 'Tambah Agenda Non-Akademik')

@section('page_title', 'Tambah Agenda Non-Akademik')
@section('page_subtitle', 'Semester Ganjil 2026/2027 | Kegiatan & Agenda Himpunan')

@section('hero_action')
    <a href="{{ route('agenda.index') }}" data-magnetic data-press class="px-4 py-2 rounded-full bg-[#0369A1] text-white text-xs md:text-sm font-semibold hover:bg-[#075985] transition shadow-xs flex items-center gap-1.5 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Kembali ke Agenda</span>
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto w-full pb-8">
    <div data-reveal class="theme-card-main rounded-3xl p-6 md:p-8 shadow-sm border theme-border">
        
        <!-- Header Row -->
        <div class="flex items-center justify-between pb-4 mb-5 border-b theme-border">
            <div class="flex items-center gap-3">
                <a href="{{ route('agenda.index') }}" class="w-8 h-8 rounded-full border theme-border flex items-center justify-center text-slate-600 hover:text-[#1D4ED8] hover:bg-[#F8FAFC] transition" title="Kembali">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-base md:text-lg font-bold theme-heading tracking-tight">Formulir Agenda Non-Akademik Baru</h2>
                    <p class="text-xs text-[#475569]">Isi rincian kegiatan himpunan, seminar, kompetisi, atau kegiatan sosial mahasiswa.</p>
                </div>
            </div>

            <span class="hidden sm:inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#DBEAFE] text-[#1E3A8A]">
                Kegiatan & Himpunan
            </span>
        </div>

        @if ($errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs">
                <ul class="list-disc list-inside space-y-1 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('agenda.store') }}" method="POST" data-stagger class="space-y-5 text-xs">
            @csrf

            <!-- 1. Judul Agenda -->
            <div>
                <label for="judul" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                    Judul Agenda Kegiatan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Workshop UI/UX Design System with FigCraft"
                       class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition placeholder:text-slate-400">
            </div>

            <!-- 2. Pilihan Kategori Visual Radio Card (Dinamis dari Database) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block font-bold uppercase tracking-wider theme-heading text-xs">
                        Kategori Agenda <span class="text-red-500">*</span>
                    </label>
                    <a href="{{ route('agenda.index') }}" class="text-xs font-semibold text-[#0369A1] hover:underline">
                        + Kelola Kategori
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($categories as $cat)
                        <label class="relative flex flex-col justify-between p-3.5 rounded-2xl border-2 cursor-pointer transition has-checked:border-[#1D4ED8] has-checked:ring-2 has-checked:ring-[#DBEAFE] bg-[#F8FAFC] hover:bg-[#DBEAFE]/40 border-transparent">
                            <input type="radio" name="kategori" value="{{ $cat->kode }}" {{ old('kategori', $selectedKategori) === $cat->kode ? 'checked' : '' }} class="sr-only">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $cat->badge_class }}">{{ $cat->kode }}</span>
                                <span class="text-xs font-bold text-slate-800">✓</span>
                            </div>
                            <span class="text-xs font-semibold theme-heading truncate">{{ $cat->nama }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 3. Status & Waktu Pelaksanaan (Status Dinamis dari Database) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Status Agenda <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition">
                        @foreach($statuses as $st)
                            <option value="{{ $st->nama }}" {{ old('status', 'Mendatang') === $st->nama ? 'selected' : '' }}>
                                {{ $st->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="waktu" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Waktu Pelaksanaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="waktu" name="waktu" value="{{ old('waktu', '15:30 WIB') }}" required placeholder="Contoh: 15:30 WIB"
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition">
                </div>
            </div>

            <!-- 4. Tanggal Mulai & Tanggal Selesai -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $defaultDate) }}" required
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition">
                </div>
                <div>
                    <label for="tanggal_selesai" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Tanggal Selesai (Opsional)
                    </label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition">
                </div>
            </div>

            <!-- 5. Lokasi & Koordinator (Koordinator Pelaksana Opsional) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="lokasi" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Lokasi / Tempat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="lokasi" name="lokasi" list="ruangAgendaList" value="{{ old('lokasi') }}" required placeholder="Pilih atau ketik lokasi (mis. Ruang Sidang Utama Gedung TI)"
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition placeholder:text-slate-400">
                    <datalist id="ruangAgendaList">
                        @foreach($ruanganList as $r)
                            <option value="{{ $r->nama_ruangan }}"></option>
                        @endforeach
                        <option value="Daring / Online"></option>
                    </datalist>
                </div>
                <div>
                    <label for="koordinator" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Koordinator Pelaksana (Opsional)
                    </label>
                    <input type="text" id="koordinator" name="koordinator" value="{{ old('koordinator') }}" placeholder="Contoh: BEM Jurusan TI / Divisi Litbang (Opsional)"
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition placeholder:text-slate-400">
                </div>
            </div>

            <!-- 6. Metadata Badge Info Tambahan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="meta_label" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Label Info Tambahan (Opsional)
                    </label>
                    <input type="text" id="meta_label" name="meta_label" value="{{ old('meta_label') }}" placeholder="Contoh: Peserta / Fasilitas / Pembimbing / Kategori"
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition placeholder:text-slate-400">
                </div>
                <div>
                    <label for="meta_value" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Nilai Info Tambahan (Opsional)
                    </label>
                    <input type="text" id="meta_value" name="meta_value" value="{{ old('meta_value') }}" placeholder="Contoh: Delegasi SI-2A / E-Sertifikat & Modul"
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition placeholder:text-slate-400">
                </div>
            </div>

            <!-- 7. Deskripsi / Keterangan -->
            <div>
                <label for="deskripsi" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                    Deskripsi / Catatan Agenda (Opsional)
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Tambahkan catatan singkat mengenai kegiatan ini..."
                          class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition placeholder:text-slate-400">{{ old('deskripsi') }}</textarea>
            </div>

            <!-- Form Action Buttons Row -->
            <div class="flex items-center justify-end gap-3 pt-5 border-t theme-border">
                <a href="{{ route('agenda.index') }}" class="px-5 py-2.5 rounded-full text-xs font-semibold text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" data-press data-magnetic class="px-6 py-2.5 rounded-full bg-[#1D4ED8] text-white text-xs font-semibold hover:bg-[#1E40AF] transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Agenda Kegiatan</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
