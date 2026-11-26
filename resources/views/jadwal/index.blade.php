@extends('layouts.jadwal')

@section('title', 'Jadwal Perkuliahan')

@section('page_title', 'Jadwal Perkuliahan')
@section('page_subtitle', 'Semester Ganjil 2026/2027 | D4 Sistem Informasi')

@section('hero_stats')
    <div class="text-right">
        <span class="block text-[10px] md:text-[11px] font-semibold tracking-wider text-[#475569] uppercase">BEBAN HARI TERPILIH</span>
        <span id="statBebanSks" class="text-lg md:text-xl font-bold text-[#0F172A]"><span data-count="{{ $bebanSks }}">{{ $bebanSks }}</span> SKS</span>
    </div>
    <div class="text-right">
        <span class="block text-[10px] md:text-[11px] font-semibold tracking-wider text-[#475569] uppercase">TOTAL SESI KULIAH</span>
        <span id="statTotalSesi" class="text-lg md:text-xl font-bold text-[#0F172A]"><span data-count="{{ $totalSesi }}">{{ $totalSesi }}</span> Kuliah</span>
    </div>
@endsection

@section('hero_action')
    @if(auth()->user()?->isAdmin())
    <a id="createScheduleBtn" href="{{ route('jadwal.create', ['tanggal' => $selectedDate]) }}" data-magnetic data-press class="px-4 py-2 rounded-full bg-[#38BDF8] text-[#082F49] text-xs md:text-sm font-semibold hover:bg-[#0EA5E9] transition shadow-xs flex items-center gap-1.5 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Tambah Jadwal</span>
    </a>
    @endif
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 pb-8">
    
    <!-- LEFT COLUMN WIDGETS (Calendar & Filters) -->
    <div class="lg:col-span-4 xl:col-span-3 flex flex-col gap-4">
        
        <!-- MINI CALENDAR CARD -->
        <div id="miniCalendar" data-reveal class="bg-[#0C4A6E] rounded-3xl p-5 text-white shadow-sm">
            <!-- Calendar Header -->
            <div class="flex items-center justify-between mb-3">
                <span class="font-bold text-sm tracking-tight capitalize">{{ $monthName }}</span>
                <div class="flex items-center gap-1.5 text-white/80">
                    <a href="{{ route('jadwal.index', array_merge(request()->query(), ['tanggal' => $prevMonthDate])) }}" data-jadwal-nav class="p-1 hover:text-white transition cursor-pointer" title="Bulan Sebelumnya">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <a href="{{ route('jadwal.index', array_merge(request()->query(), ['tanggal' => $nextMonthDate])) }}" data-jadwal-nav class="p-1 hover:text-white transition cursor-pointer" title="Bulan Berikutnya">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- Days of Week Header -->
            <div class="grid grid-cols-7 text-center text-xs text-white/80 font-medium mb-2">
                <span>Sen</span>
                <span>Sel</span>
                <span>Rab</span>
                <span>Kam</span>
                <span>Jum</span>
                <span>Sab</span>
                <span>Min</span>
            </div>

            <!-- Calendar Days Grid -->
            <div class="grid grid-cols-7 gap-y-1.5 text-center text-xs">
                @for ($b = 1; $b < $startDayOfWeek; $b++)
                    <div></div>
                @endfor

                @for ($day = 1; $day <= $totalDaysInMonth; $day++)
                    @php
                        $dayStr = sprintf('%s-%02d', $currentMonthYear, $day);
                        $isSelected = ($dayStr === $selectedDate);
                        $isToday = ($dayStr === $todayDate);
                    @endphp
                    <div class="flex items-center justify-center">
                        <a href="{{ route('jadwal.index', array_merge(request()->query(), ['tanggal' => $dayStr])) }}"
                           data-jadwal-nav data-press class="w-7 h-7 flex items-center justify-center rounded-full transition text-xs
                           {{ $isSelected ? 'bg-[#38BDF8] text-[#082F49] font-bold shadow' : ($isToday ? 'border-2 border-[#38BDF8] text-white font-bold' : 'text-white hover:bg-white/20') }}"
                           title="{{ $isToday ? 'Hari Ini' : '' }}">
                            {{ $day }}
                        </a>
                    </div>
                @endfor
            </div>
        </div>

        <!-- JADWAL PENGGANTI CARD -->
        <div id="filterCard" data-reveal class="theme-card-main rounded-3xl p-5 shadow-sm border theme-border">
            <div class="flex items-center justify-between mb-3">
                <span class="font-bold text-xs theme-heading">Jadwal Pengganti</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#E0F2FE] text-[#0C4A6E]">
                    Filter
                </span>
            </div>

            <div class="space-y-2 text-xs">
                <!-- Checkbox 1: Tampilkan Kuliah Pengganti -->
                <a href="{{ route('jadwal.index', array_merge(request()->query(), ['show_pengganti' => $showPengganti ? '0' : '1'])) }}" 
                   data-jadwal-nav data-press class="flex items-start gap-2.5 p-2 rounded-2xl transition {{ $showPengganti ? 'bg-[#E0F2FE]' : 'hover:bg-slate-50' }}">
                    <div class="w-4 h-4 rounded-md flex items-center justify-center shrink-0 mt-0.5 {{ $showPengganti ? 'bg-[#0C4A6E] text-white' : 'border border-slate-300' }}">
                        @if($showPengganti)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </div>
                    <div>
                        <span class="font-semibold theme-heading block text-xs">Tampilkan Kuliah Pengganti</span>
                        <span class="text-[#475569] text-[10px]">{{ $penggantiCount }} sesi pengganti aktif</span>
                    </div>
                </a>

                <!-- Checkbox 2: Sembunyikan Non-Akademik -->
                <a href="{{ route('jadwal.index', array_merge(request()->query(), ['hide_non_akademik' => $hideNonAkademik ? '0' : '1'])) }}" 
                   data-jadwal-nav data-press class="flex items-start gap-2.5 p-2 rounded-2xl transition {{ $hideNonAkademik ? 'bg-[#E0F2FE]' : 'hover:bg-slate-50' }}">
                    <div class="w-4 h-4 rounded-md flex items-center justify-center shrink-0 mt-0.5 {{ $hideNonAkademik ? 'bg-[#0C4A6E] text-white' : 'border border-slate-300' }}">
                        @if($hideNonAkademik)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </div>
                    <div>
                        <span class="font-medium text-slate-700 block text-xs">Sembunyikan Non-Akademik</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- AGENDA NON-AKADEMIK LINK CARD (hidden if hide_non_akademik is true) -->
        <div id="nonAkademikSlot">
        @if(!$hideNonAkademik)
        <a href="{{ route('agenda.index') }}" data-reveal data-hover-lift class="theme-card-main rounded-3xl p-4 shadow-sm border theme-border flex items-center gap-3.5 hover:shadow-md transition group">
            <div class="w-9 h-9 rounded-full bg-[#FEF3C7] text-[#78350F] font-bold text-xs flex items-center justify-center shrink-0">
                AG
            </div>
            <div class="min-w-0">
                <span class="font-bold text-xs theme-heading block group-hover:text-[#0369A1] transition">Agenda Non-Akademik</span>
                <span class="text-[#0369A1] text-xs">Kegiatan & Agenda Himpunan</span>
            </div>
            <div class="ml-auto text-slate-400 group-hover:translate-x-1 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
        @endif
        </div>

    </div>

    <!-- MAIN TIMELINE / SCHEDULE AREA -->
    <div class="lg:col-span-8 xl:col-span-9 flex flex-col">
        <div id="scheduleViewer"
             data-today-date="{{ $todayDate }}"
             data-selected-date="{{ $selectedDate }}"
             data-show-pengganti="{{ $showPengganti ? '1' : '0' }}"
             data-hide-non-akademik="{{ $hideNonAkademik ? '1' : '0' }}"
             data-reveal class="theme-card-main rounded-3xl p-5 md:p-7 shadow-sm border theme-border overflow-hidden">
            
            <!-- Date Subheader & Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b theme-border">
                <div data-schedule-date class="flex items-center gap-2.5">
                    <h2 class="text-base md:text-lg font-bold theme-heading tracking-tight capitalize">
                        {{ $formattedDate }}
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#E0F2FE] text-[#0C4A6E]">
                        Pekan Perkuliahan Reguler
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('jadwal.index', array_merge(request()->query(), ['tanggal' => $todayDate])) }}" data-jadwal-nav data-press class="px-3.5 py-1.5 rounded-full bg-[#38BDF8] text-[#082F49] text-xs font-semibold hover:bg-[#0EA5E9] transition">
                        Hari Ini
                    </a>
                    <a href="{{ route('jadwal.index', array_merge(request()->query(), ['tanggal' => $prevDayDate])) }}" data-jadwal-nav data-press title="Hari Sebelumnya" class="w-7 h-7 rounded-full border theme-border flex items-center justify-center text-slate-600 hover:text-[#0C4A6E] hover:bg-[#F0F9FF] transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <a href="{{ route('jadwal.index', array_merge(request()->query(), ['tanggal' => $nextDayDate])) }}" data-jadwal-nav data-press title="Hari Berikutnya" class="w-7 h-7 rounded-full border theme-border flex items-center justify-center text-slate-600 hover:text-[#0C4A6E] hover:bg-[#F0F9FF] transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- Timeline Items -->
            <div data-schedule-body class="pt-5 space-y-4">
                @forelse($schedules as $item)
                    <div data-reveal data-schedule-item class="flex items-start gap-3 md:gap-5">
                        <!-- Time label -->
                        <div class="w-12 shrink-0 text-right font-semibold text-xs text-slate-500 pt-3">
                            {{ substr($item->jam_mulai, 0, 5) }}
                        </div>

                        <!-- Schedule Card -->
                        <div class="flex-1">
                            @if($loop->first)
                                <div class="inline-block mb-2 px-2.5 py-0.5 rounded-full bg-[#0C4A6E] text-white text-[9px] font-bold tracking-wider uppercase">
                                    {{ strtoupper($formattedDate) }}
                                </div>
                            @endif

                            @php
                                $cardStyle = match($item->warna_bg) {
                                    'lime' => [
                                        'card' => 'bg-[#d1f765] text-[#1f3300]',
                                        'sks' => 'bg-[#1e1f37] text-white',
                                        'tag' => 'bg-black/10 text-[#1f3300]',
                                        'sub' => 'text-[#263e00]',
                                    ],
                                    'purple' => [
                                        'card' => 'bg-[#58659e] text-white',
                                        'sks' => 'bg-white/20 text-white',
                                        'tag' => 'bg-white/20 text-white',
                                        'sub' => 'text-slate-100',
                                    ],
                                    'amber' => [
                                        'card' => 'bg-[#ffd8a8] text-[#5c3100]',
                                        'sks' => 'bg-[#5c3100] text-white',
                                        'tag' => 'bg-black/10 text-[#5c3100]',
                                        'sub' => 'text-[#7a3e00]',
                                    ],
                                    'cyan' => [
                                        'card' => 'bg-[#c5f6fa] text-[#0c5460]',
                                        'sks' => 'bg-[#0c5460] text-white',
                                        'tag' => 'bg-black/10 text-[#0c5460]',
                                        'sub' => 'text-[#155d6a]',
                                    ],
                                    'rose' => [
                                        'card' => 'bg-[#ffc9c9] text-[#5c0f0f]',
                                        'sks' => 'bg-[#5c0f0f] text-white',
                                        'tag' => 'bg-black/10 text-[#5c0f0f]',
                                        'sub' => 'text-[#781717]',
                                    ],
                                    'navy' => [
                                        'card' => 'bg-[#1e1f37] text-white',
                                        'sks' => 'bg-[#d1f765] text-[#1f3300]',
                                        'tag' => 'bg-white/20 text-white',
                                        'sub' => 'text-slate-300',
                                    ],
                                    'emerald' => [
                                        'card' => 'bg-[#bbf7d0] text-[#064e3b]',
                                        'sks' => 'bg-[#064e3b] text-white',
                                        'tag' => 'bg-black/10 text-[#064e3b]',
                                        'sub' => 'text-[#065f46]',
                                    ],
                                    default => [
                                        'card' => 'bg-[#d1f765] text-[#1f3300]',
                                        'sks' => 'bg-[#1e1f37] text-white',
                                        'tag' => 'bg-black/10 text-[#1f3300]',
                                        'sub' => 'text-[#263e00]',
                                    ],
                                };
                            @endphp

                            <div data-tilt class="rounded-3xl p-4 md:p-5 {{ $cardStyle['card'] }} shadow-xs transition hover:shadow-md relative group">
                                <!-- Card Header: Title & SKS -->
                                <div class="flex items-center justify-between gap-3 mb-2">
                                    <h3 class="font-bold text-sm md:text-base tracking-tight">
                                        {{ $item->nama_matakuliah }}
                                        @if($item->is_pengganti)
                                            <span class="ml-1.5 text-xs font-normal opacity-80">(Kuliah Pengganti)</span>
                                        @endif
                                    </h3>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $cardStyle['sks'] }}">
                                            {{ $item->sks }} SKS
                                        </span>
                                        @if(auth()->user()?->isAdmin())
                                        <!-- Delete button -->
                                        <form action="{{ route('jadwal.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Jadwal" class="opacity-0 group-hover:opacity-100 p-1 hover:text-red-600 transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>

                                <!-- Details Row -->
                                <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-xs font-medium {{ $cardStyle['sub'] }} mb-3">
                                    <span>Waktu: {{ $item->jam_mulai }} - {{ $item->jam_selesai }} WIB</span>
                                    <span>Ruang: {{ $item->ruang }}</span>
                                    <span class="italic">Dosen: {{ $item->dosen }}</span>
                                </div>

                                <!-- Badges -->
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cardStyle['tag'] }}">
                                        {{ $item->jenis }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cardStyle['tag'] }}">
                                        {{ $item->metode }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div data-schedule-item class="py-12 text-center">
                        <p class="text-slate-400 text-xs">Tidak ada jadwal perkuliahan pada tanggal ini.</p>
                        @if(auth()->user()?->isAdmin())
                        <a href="{{ route('jadwal.create', ['tanggal' => $selectedDate]) }}" class="inline-block mt-2.5 px-4 py-1.5 rounded-full bg-[#38BDF8] text-[#082F49] text-xs font-semibold hover:bg-[#0EA5E9] transition">
                            + Tambah Jadwal Baru
                        </a>
                        @endif
                    </div>
                @endforelse
            </div>

        </div>
    </div>

</div>
@endsection
