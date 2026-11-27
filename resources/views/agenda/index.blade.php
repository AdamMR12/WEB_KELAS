@extends('layouts.jadwal')

@section('title', 'Agenda Non-Akademik')

@section('page_title', 'Agenda Non-Akademik')
@section('page_subtitle', 'Semester Ganjil 2026/2027 | Kegiatan & Agenda Himpunan')

@section('hero_stats')
    <div class="text-right">
        <span class="block text-[10px] md:text-[11px] font-semibold tracking-wider text-[#475569] uppercase">TOTAL AGENDA</span>
        <span class="text-lg md:text-xl font-bold text-[#0F172A]"><span data-count="{{ $totalCount }}">{{ $totalCount }}</span> Kegiatan</span>
    </div>
    <div class="text-right">
        <span class="block text-[10px] md:text-[11px] font-semibold tracking-wider text-[#475569] uppercase">AGENDA TERDEKAT</span>
        <span class="text-lg md:text-xl font-bold text-[#0F172A]">{{ $nearestDate }}</span>
    </div>
@endsection

@section('hero_action')
    @if(auth()->user()?->isAdmin())
    <div class="flex items-center gap-2">
        <!-- Button Buka Modal Kelola Kategori & Status -->
        <button type="button" id="openManageModalBtn" data-press data-magnetic class="px-3.5 py-2 rounded-full bg-[#DBEAFE] hover:bg-[#BFDBFE] text-[#1E3A8A] text-xs font-semibold transition flex items-center gap-1.5 cursor-pointer" title="Kelola Kategori & Status">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            <span class="hidden sm:inline">Kelola Kategori & Status</span>
        </button>

        <a href="{{ route('agenda.create') }}" data-magnetic data-press class="px-4 py-2 rounded-full bg-[#1D4ED8] text-white text-xs md:text-sm font-semibold hover:bg-[#1E40AF] transition shadow-xs flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Agenda</span>
        </a>
    </div>
    @endif
@endsection

@section('content')
<div id="agendaViewer" data-selected-kategori="{{ $kategori }}" data-reveal class="theme-card-main rounded-3xl p-5 md:p-7 shadow-sm border theme-border overflow-hidden">
    
    <!-- FILTER CHIPS BAR (Dynamic from categories, without weekly/daily toggle) -->
    <div id="agendaChips" class="flex items-center gap-2 overflow-x-auto scrollbar-none pb-4 mb-5 border-b theme-border">
        <a href="{{ route('agenda.index', ['kategori' => 'SEMUA']) }}" 
           data-agenda-nav data-press class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition
           {{ $kategori === 'SEMUA' ? 'bg-[#1D4ED8] text-white shadow-xs' : 'bg-transparent border theme-border text-slate-700 hover:bg-[#F8FAFC]' }}">
            Semua Agenda ({{ $totalCount }})
        </a>

        @foreach($categories as $cat)
            @php
                $catCount = $categoryCounts[$cat->kode] ?? 0;
                $isActive = ($kategori === $cat->kode);
            @endphp
            <a href="{{ route('agenda.index', ['kategori' => $cat->kode]) }}" 
               data-agenda-nav data-press class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition
               {{ $isActive ? 'bg-[#1D4ED8] text-white shadow-xs' : 'bg-transparent border theme-border text-slate-700 hover:bg-[#F8FAFC]' }}">
                {{ $cat->nama }} ({{ $catCount }})
            </a>
        @endforeach
    </div>

    <!-- 2x2 AGENDA GRID -->
    <div id="agendaGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($agendas as $agenda)
            @php
                $catModel = $categories->firstWhere('kode', $agenda->kategori);
                $badgeBg = $catModel ? $catModel->badge_class : 'bg-slate-100 text-slate-800';
            @endphp

            <div data-reveal data-agenda-item data-tilt class="theme-card-sub rounded-3xl p-5 border theme-border shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                <div>
                    <!-- Top Bar: Category badge & Status -->
                    <div class="flex items-center justify-between gap-2.5 mb-2.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase {{ $badgeBg }}">
                            {{ $agenda->kategori }}
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-[#6f718f] font-medium">
                                {{ $agenda->status }}
                            </span>
                            @if(auth()->user()?->isAdmin())
                            <!-- Delete form -->
                            <form action="{{ route('agenda.destroy', $agenda) }}" method="POST" onsubmit="return confirm('Hapus agenda ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus Agenda" class="opacity-0 group-hover:opacity-100 p-1 text-slate-400 hover:text-red-500 transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    <!-- Title -->
                    <h3 class="font-bold text-sm md:text-base theme-heading tracking-tight mb-2 leading-snug">
                        {{ $agenda->judul }}
                    </h3>

                    <!-- Details -->
                    <div class="space-y-0.5 text-xs text-[#475569] font-medium mb-4">
                        <p>Tanggal: {{ $agenda->tanggal_display }}</p>
                        <p>Waktu: {{ $agenda->waktu }}</p>
                        <p>Lokasi: {{ $agenda->lokasi }}</p>
                    </div>
                </div>

                <!-- Bottom Meta Row -->
                <div class="pt-3 border-t theme-border flex items-center justify-between text-[11px]">
                    <span class="font-semibold text-[#0369A1]">
                        Koordinator: {{ $agenda->koordinator ?: '-' }}
                    </span>
                    @if($agenda->meta_label && $agenda->meta_value)
                        <span class="text-[#475569]">
                            {{ $agenda->meta_label }}: {{ $agenda->meta_value }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div data-agenda-item class="col-span-1 lg:col-span-2 py-12 text-center">
                <p class="text-slate-500 text-xs">Belum ada agenda untuk kategori ini.</p>
                @if(auth()->user()?->isAdmin())
                <a href="{{ route('agenda.create') }}" class="inline-block mt-2.5 px-4 py-1.5 rounded-full bg-[#1D4ED8] text-white text-xs font-semibold hover:bg-[#1E40AF] transition">
                    + Tambah Agenda Baru
                </a>
                @endif
            </div>
        @endforelse
    </div>

</div>
@endsection

@if(auth()->user()?->isAdmin())
@section('modals')
<!-- MODAL KELOLA KATEGORI & STATUS -->
<div id="manageModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
    <div data-modal-box role="dialog" aria-modal="true" aria-labelledby="manageModalTitle" class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
            <div>
                <h3 id="manageModalTitle" class="font-bold text-base text-[#0F172A]">Kelola Kategori & Status Agenda</h3>
                <p class="text-xs text-slate-500">Tambah atau hapus pilihan kategori dan status untuk agenda kegiatan.</p>
            </div>
            <button type="button" data-modal-close aria-label="Tutup" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Modal Tabs -->
        <div role="tablist" class="relative flex border-b border-slate-100 px-6 shrink-0 text-xs font-bold">
            <button type="button" role="tab" data-tab="panelKategori" aria-selected="true" class="py-3 px-4 text-[#1D4ED8] transition cursor-pointer">
                Kategori Agenda ({{ $categories->count() }})
            </button>
            <button type="button" role="tab" data-tab="panelStatus" aria-selected="false" class="py-3 px-4 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                Status Agenda ({{ $statuses->count() }})
            </button>
            <span data-tab-indicator aria-hidden="true" class="absolute bottom-0 left-0 h-0.5 w-0 bg-[#1D4ED8] rounded-full"></span>
        </div>

        <!-- Modal Body Scrollable -->
        <div data-lenis-prevent class="flex-1 min-h-0 overflow-y-auto p-6 space-y-6 scrollbar-thin text-xs">
            
            <!-- PANEL 1: KATEGORI AGENDA -->
            <div id="panelKategori" class="space-y-6">
                <!-- Form Tambah Kategori -->
                <form action="{{ route('agenda.category.store') }}" method="POST" class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    @csrf
                    <span class="font-bold text-xs text-slate-800 block">Tambah Kategori Baru</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Nama Kategori</label>
                            <input type="text" name="nama" required placeholder="Contoh: Seminar Nasional" 
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:outline-hidden focus:ring-2 focus:ring-[#1D4ED8]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Tema Warna</label>
                            <select name="warna_bg" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:outline-hidden focus:ring-2 focus:ring-[#1D4ED8]">
                                <option value="amber">Amber / Orange</option>
                                <option value="lime">Lime / Hijau</option>
                                <option value="cyan">Cyan / Biru Muda</option>
                                <option value="rose">Rose / Merah Muda</option>
                                <option value="purple">Purple / Ungu</option>
                                <option value="emerald">Emerald / Hijau Tua</option>
                                <option value="navy">Navy / Gelap</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-4 py-1.5 rounded-full bg-[#1D4ED8] text-white text-xs font-semibold hover:bg-[#1E40AF] transition cursor-pointer">
                            + Tambah Kategori
                        </button>
                    </div>
                </form>

                <!-- Daftar Kategori Existing -->
                <div>
                    <span class="font-bold text-xs text-slate-800 block mb-2">Daftar Kategori Tersedia</span>
                    <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden">
                        @foreach($categories as $cat)
                            <div class="p-3 flex items-center justify-between hover:bg-slate-50 transition">
                                <div class="flex items-center gap-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $cat->badge_class }}">
                                        {{ $cat->kode }}
                                    </span>
                                    <div>
                                        <span class="font-semibold text-slate-800">{{ $cat->nama }}</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $categoryCounts[$cat->kode] ?? 0 }} agenda terkait</span>
                                    </div>
                                </div>
                                <form action="{{ route('agenda.category.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $cat->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 transition cursor-pointer" title="Hapus Kategori">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- PANEL 2: STATUS AGENDA -->
            <div id="panelStatus" class="hidden space-y-6">
                <!-- Form Tambah Status -->
                <form action="{{ route('agenda.status.store') }}" method="POST" class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    @csrf
                    <span class="font-bold text-xs text-slate-800 block">Tambah Status Baru</span>
                    <div class="flex gap-2">
                        <input type="text" name="nama" required placeholder="Contoh: Ditunda / Selesai / Terbuka" 
                               class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:outline-hidden focus:ring-2 focus:ring-[#1D4ED8]">
                        <button type="submit" class="px-4 py-2 rounded-full bg-[#1D4ED8] text-white text-xs font-semibold hover:bg-[#1E40AF] transition cursor-pointer shrink-0">
                            + Tambah Status
                        </button>
                    </div>
                </form>

                <!-- Daftar Status Existing -->
                <div>
                    <span class="font-bold text-xs text-slate-800 block mb-2">Daftar Status Tersedia</span>
                    <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden">
                        @foreach($statuses as $st)
                            <div class="p-3 flex items-center justify-between hover:bg-slate-50 transition">
                                <span class="font-semibold text-slate-800">{{ $st->nama }}</span>
                                <form action="{{ route('agenda.status.destroy', $st) }}" method="POST" onsubmit="return confirm('Hapus status {{ $st->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 transition cursor-pointer" title="Hapus Status">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3 border-t border-slate-100 flex justify-end shrink-0">
            <button type="button" data-modal-close class="px-5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                Tutup
            </button>
        </div>

    </div>
</div>

@endsection
@endif
