@extends('layouts.jadwal')

@section('title', 'Tambah Jadwal Perkuliahan')

@section('page_title', 'Tambah Jadwal Perkuliahan')
@section('page_subtitle', 'Semester Ganjil 2026/2027 | D4 Sistem Informasi')

@section('hero_action')
    <a href="{{ route('jadwal.index', ['tanggal' => $selectedDate]) }}" data-magnetic data-press class="px-4 py-2 rounded-full bg-[#0369A1] text-white text-xs md:text-sm font-semibold hover:bg-[#075985] transition shadow-xs flex items-center gap-1.5 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Kembali ke Jadwal</span>
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto w-full pb-8">
    <div data-reveal class="theme-card-main rounded-3xl p-6 md:p-8 shadow-sm border theme-border">
        
        <!-- Header Row -->
        <div class="flex items-center justify-between pb-4 mb-5 border-b theme-border">
            <div class="flex items-center gap-3">
                <a href="{{ route('jadwal.index', ['tanggal' => $selectedDate]) }}" class="w-8 h-8 rounded-full border theme-border flex items-center justify-center text-slate-600 hover:text-[#1D4ED8] hover:bg-[#F8FAFC] transition" title="Kembali">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-base md:text-lg font-bold theme-heading tracking-tight">Formulir Jadwal Kuliah Baru</h2>
                    <p class="text-xs text-[#475569]">Isi rincian mata kuliah untuk ditampilkan pada jadwal harian perkuliahan.</p>
                </div>
            </div>

            <span class="hidden sm:inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#DBEAFE] text-[#1E3A8A]">
                Kelas SI - 2A
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

        <form action="{{ route('jadwal.store') }}" method="POST" data-stagger class="space-y-5 text-xs">
            @csrf

            <!-- 1. Nama Mata Kuliah -->
            <div>
                <label for="nama_matakuliah" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                    Mata Kuliah <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_matakuliah" name="nama_matakuliah" list="mkList" value="{{ old('nama_matakuliah') }}" required placeholder="Pilih atau ketik mata kuliah (mis. Pemrograman Web)"
                       class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition placeholder:text-slate-400">
                <datalist id="mkList">
                    @foreach($mataKuliahList as $mk)
                        <option value="{{ $mk->nama_mk }}" data-sks="{{ $mk->sks }}">{{ $mk->kode_mk }} ({{ $mk->sks }} SKS)</option>
                    @endforeach
                </datalist>
            </div>

            <!-- 2. SKS & Hari -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="sks" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Beban SKS <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="sks" name="sks" value="{{ old('sks', 3) }}" min="1" max="6" required
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition">
                </div>
                <div>
                    <label for="hari" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Hari Perkuliahan <span class="text-red-500">*</span>
                    </label>
                    <select id="hari" name="hari" required
                            class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                            <option value="{{ $hari }}" {{ old('hari', $dayName ?? 'Senin') === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 3. Tanggal & Jam Kuliah -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="tanggal" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', $selectedDate) }}" required
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition">
                </div>
                <div>
                    <label for="jam_mulai" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Jam Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', '08:00') }}" required placeholder="08:00"
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition">
                </div>
                <div>
                    <label for="jam_selesai" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Jam Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', '10:00') }}" required placeholder="10:00"
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition">
                </div>
            </div>

            <!-- 4. Ruangan & Dosen -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="ruang" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Ruang Kuliah / Lab <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="ruang" name="ruang" list="ruangList" value="{{ old('ruang') }}" required placeholder="Pilih atau ketik ruangan (mis. Lab Komputer 3)"
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition placeholder:text-slate-400">
                    <datalist id="ruangList">
                        @foreach($ruanganList as $r)
                            <option value="{{ $r->nama_ruangan }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label for="dosen" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Dosen Pengampu <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="dosen" name="dosen" list="dosenList" value="{{ old('dosen') }}" required placeholder="Pilih atau ketik dosen (mis. Pak Hendra, M.Kom.)"
                           class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition placeholder:text-slate-400">
                    <datalist id="dosenList">
                        @foreach($dosenList as $d)
                            <option value="{{ $d->nama }}">{{ $d->nip }}</option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <!-- 5. Jenis & Metode -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="jenis" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Jenis Mata Kuliah
                    </label>
                    <select id="jenis" name="jenis"
                            class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition">
                        <option value="Mata Kuliah Wajib" {{ old('jenis') === 'Mata Kuliah Wajib' ? 'selected' : '' }}>Mata Kuliah Wajib</option>
                        <option value="Mata Kuliah Pilihan" {{ old('jenis') === 'Mata Kuliah Pilihan' ? 'selected' : '' }}>Mata Kuliah Pilihan</option>
                        <option value="Kuliah Pengganti" {{ old('jenis') === 'Kuliah Pengganti' ? 'selected' : '' }}>Kuliah Pengganti</option>
                    </select>
                </div>
                <div>
                    <label for="metode" class="block font-bold uppercase tracking-wider theme-heading mb-1.5 text-xs">
                        Metode Perkuliahan
                    </label>
                    <select id="metode" name="metode"
                            class="w-full px-4 py-3 rounded-2xl border theme-border text-sm theme-heading focus:ring-2 focus:ring-[#1D4ED8] focus:outline-hidden transition">
                        <option value="Praktikum Lab" {{ old('metode') === 'Praktikum Lab' ? 'selected' : '' }}>Praktikum Lab</option>
                        <option value="Teori Kelas" {{ old('metode') === 'Teori Kelas' ? 'selected' : '' }}>Teori Kelas</option>
                        <option value="Kuliah Tambahan" {{ old('metode') === 'Kuliah Tambahan' ? 'selected' : '' }}>Kuliah Tambahan</option>
                    </select>
                </div>
            </div>

            <!-- 6. Pilihan Gaya Kartu (Maksimal 7 Pilihan Warna UI) -->
            <div>
                <label class="block font-bold uppercase tracking-wider theme-heading mb-2 text-xs">
                    Gaya Kartu UI (Maksimal 7 Opsi)
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    <!-- Option 1: Lime Neon -->
                    <label class="relative flex items-center gap-3 p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-[#1e1f37] has-checked:ring-2 has-checked:ring-[#d1f765] bg-[#d1f765]/20 border-transparent hover:bg-[#d1f765]/30">
                        <input type="radio" name="warna_bg" value="lime" {{ old('warna_bg', 'lime') === 'lime' ? 'checked' : '' }} class="sr-only">
                        <div class="w-7 h-7 rounded-lg bg-[#d1f765] text-[#1f3300] font-bold flex items-center justify-center shrink-0 shadow-xs text-xs">
                            ✓
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-[#1f3300] block truncate">Lime Neon (#d1f765)</span>
                            <span class="text-[11px] text-[#263e00] block truncate">Hijau terang kontras</span>
                        </div>
                    </label>

                    <!-- Option 2: Slate Purple -->
                    <label class="relative flex items-center gap-3 p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-[#1e1f37] has-checked:ring-2 has-checked:ring-[#58659e] bg-[#58659e]/20 border-transparent hover:bg-[#58659e]/30">
                        <input type="radio" name="warna_bg" value="purple" {{ old('warna_bg') === 'purple' ? 'checked' : '' }} class="sr-only">
                        <div class="w-7 h-7 rounded-lg bg-[#58659e] text-white font-bold flex items-center justify-center shrink-0 shadow-xs text-xs">
                            ✓
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-xs theme-heading block truncate">Slate Purple (#58659e)</span>
                            <span class="text-[11px] text-[#6f718f] block truncate">Biru ungu teduh</span>
                        </div>
                    </label>

                    <!-- Option 3: Amber Warm -->
                    <label class="relative flex items-center gap-3 p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-[#1e1f37] has-checked:ring-2 has-checked:ring-[#ffd8a8] bg-[#ffd8a8]/20 border-transparent hover:bg-[#ffd8a8]/30">
                        <input type="radio" name="warna_bg" value="amber" {{ old('warna_bg') === 'amber' ? 'checked' : '' }} class="sr-only">
                        <div class="w-7 h-7 rounded-lg bg-[#ffd8a8] text-[#5c3100] font-bold flex items-center justify-center shrink-0 shadow-xs text-xs">
                            ✓
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-[#5c3100] block truncate">Amber Warm (#ffd8a8)</span>
                            <span class="text-[11px] text-[#7a3e00] block truncate">Kuning oranye hangat</span>
                        </div>
                    </label>

                    <!-- Option 4: Cyan Sky -->
                    <label class="relative flex items-center gap-3 p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-[#1e1f37] has-checked:ring-2 has-checked:ring-[#c5f6fa] bg-[#c5f6fa]/20 border-transparent hover:bg-[#c5f6fa]/30">
                        <input type="radio" name="warna_bg" value="cyan" {{ old('warna_bg') === 'cyan' ? 'checked' : '' }} class="sr-only">
                        <div class="w-7 h-7 rounded-lg bg-[#c5f6fa] text-[#0c5460] font-bold flex items-center justify-center shrink-0 shadow-xs text-xs">
                            ✓
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-[#0c5460] block truncate">Cyan Sky (#c5f6fa)</span>
                            <span class="text-[11px] text-[#155d6a] block truncate">Biru toska segar</span>
                        </div>
                    </label>

                    <!-- Option 5: Rose Coral -->
                    <label class="relative flex items-center gap-3 p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-[#1e1f37] has-checked:ring-2 has-checked:ring-[#ffc9c9] bg-[#ffc9c9]/20 border-transparent hover:bg-[#ffc9c9]/30">
                        <input type="radio" name="warna_bg" value="rose" {{ old('warna_bg') === 'rose' ? 'checked' : '' }} class="sr-only">
                        <div class="w-7 h-7 rounded-lg bg-[#ffc9c9] text-[#5c0f0f] font-bold flex items-center justify-center shrink-0 shadow-xs text-xs">
                            ✓
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-[#5c0f0f] block truncate">Rose Coral (#ffc9c9)</span>
                            <span class="text-[11px] text-[#781717] block truncate">Merah muda pastel</span>
                        </div>
                    </label>

                    <!-- Option 6: Dark Navy -->
                    <label class="relative flex items-center gap-3 p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-[#1e1f37] has-checked:ring-2 has-checked:ring-[#1e1f37] bg-[#1e1f37]/10 border-transparent hover:bg-[#1e1f37]/20">
                        <input type="radio" name="warna_bg" value="navy" {{ old('warna_bg') === 'navy' ? 'checked' : '' }} class="sr-only">
                        <div class="w-7 h-7 rounded-lg bg-[#1e1f37] text-white font-bold flex items-center justify-center shrink-0 shadow-xs text-xs">
                            ✓
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-xs theme-heading block truncate">Dark Navy (#1e1f37)</span>
                            <span class="text-[11px] text-[#6f718f] block truncate">Biru gelap pekat</span>
                        </div>
                    </label>

                    <!-- Option 7: Mint Emerald -->
                    <label class="relative flex items-center gap-3 p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-[#1e1f37] has-checked:ring-2 has-checked:ring-[#bbf7d0] bg-[#bbf7d0]/20 border-transparent hover:bg-[#bbf7d0]/30">
                        <input type="radio" name="warna_bg" value="emerald" {{ old('warna_bg') === 'emerald' ? 'checked' : '' }} class="sr-only">
                        <div class="w-7 h-7 rounded-lg bg-[#bbf7d0] text-[#064e3b] font-bold flex items-center justify-center shrink-0 shadow-xs text-xs">
                            ✓
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-[#064e3b] block truncate">Mint Emerald (#bbf7d0)</span>
                            <span class="text-[11px] text-[#065f46] block truncate">Hijau mint natural</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 7. Opsi Kuliah Pengganti -->
            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#CBD5E1] flex items-center justify-between">
                <div>
                    <span class="font-bold text-xs theme-heading block">Sesi Kuliah Pengganti</span>
                    <span class="text-[11px] text-[#475569]">Aktifkan jika jadwal ini merupakan sesi pengganti dari jadwal reguler.</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_pengganti" value="1" {{ old('is_pengganti') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1D4ED8]"></div>
                </label>
            </div>

            <!-- Form Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-5 border-t theme-border">
                <a href="{{ route('jadwal.index', ['tanggal' => $selectedDate]) }}" class="px-5 py-2.5 rounded-full text-xs font-semibold text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" data-press data-magnetic class="px-6 py-2.5 rounded-full bg-[#1D4ED8] text-white text-xs font-semibold hover:bg-[#1E40AF] transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Jadwal Kuliah</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('tanggal');
        const daySelect = document.getElementById('hari');
        const daysMap = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        if (dateInput && daySelect) {
            dateInput.addEventListener('change', function () {
                if (!this.value) return;
                const d = new Date(this.value + 'T00:00:00');
                const dayName = daysMap[d.getDay()];
                if (dayName) {
                    daySelect.value = dayName;
                }
            });
        }
        const mkInput = document.getElementById('nama_matakuliah');
        const sksInput = document.getElementById('sks');
        const mkOptions = document.querySelectorAll('#mkList option');
        if (mkInput && sksInput) {
            mkInput.addEventListener('input', function () {
                for (const opt of mkOptions) {
                    if (opt.value === this.value && opt.dataset.sks) {
                        sksInput.value = opt.dataset.sks;
                        break;
                    }
                }
            });
        }
    });
</script>
@endsection
