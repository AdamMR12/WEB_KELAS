<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">

            {{-- Kiri: Logo + menu utama --}}
            <div class="flex items-center gap-6">
                <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                   class="font-bold text-lg text-indigo-600">
                    Web Kelas
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                       class="px-3 py-2 rounded text-sm hover:bg-gray-100 {{ request()->routeIs('dashboard.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('jadwal.index') }}"
                       class="px-3 py-2 rounded text-sm hover:bg-gray-100 {{ request()->routeIs('jadwal.*') ? 'bg-[#E0F2FE] text-[#0C4A6E] font-medium' : 'text-gray-700' }}">
                        Jadwal Kuliah
                    </a>

                    <a href="{{ route('agenda.index') }}"
                       class="px-3 py-2 rounded text-sm hover:bg-gray-100 {{ request()->routeIs('agenda.*') ? 'bg-[#E0F2FE] text-[#0C4A6E] font-medium' : 'text-gray-700' }}">
                        Agenda
                    </a>
                </div>
            </div>

            {{-- Kanan: info user + logout --}}
            <div class="flex items-center gap-3">
                @if(request()->routeIs('jadwal.*', 'agenda.*') && isset($totalReminders))
                    <div class="relative" id="reminderContainer">
                        <button type="button" id="reminderToggleBtn" title="Pengingat Kuliah & Agenda" aria-haspopup="true" aria-expanded="false" aria-controls="reminderDropdown" data-press
                                class="relative w-9 h-9 rounded-full bg-[#E0F2FE] hover:bg-[#BAE6FD] text-[#0C4A6E] flex items-center justify-center transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                            @if($totalReminders > 0)
                                <span id="reminderBadge" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-[#38BDF8] text-[#082F49] font-bold text-[10px] flex items-center justify-center shadow-xs">
                                    {{ $totalReminders }}
                                </span>
                            @endif
                        </button>

                        <div id="reminderDropdown" class="hidden absolute -right-10 sm:right-0 top-full mt-2.5 w-[min(24rem,calc(100vw-3rem))] sm:w-96 bg-white rounded-3xl shadow-2xl ring-1 ring-slate-900/10 border border-slate-200/90 p-5 z-50 text-slate-800">
                            <div class="flex items-center justify-between gap-3 pb-3 mb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-[#E0F2FE] text-[#0C4A6E] flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-sm text-[#0F172A] leading-tight">Pengingat Aktif</h4>
                                        <p class="text-[11px] text-slate-500 truncate">Jadwal kuliah hari ini & agenda mendatang</p>
                                    </div>
                                </div>
                                <span class="shrink-0 whitespace-nowrap px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#E0F2FE] text-[#0C4A6E]">
                                    {{ $totalReminders }} Pengingat
                                </span>
                            </div>

                            <div data-lenis-prevent class="max-h-[340px] overflow-y-auto space-y-4 pr-1 scrollbar-thin text-xs">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400">Kuliah Hari Ini</span>
                                        <span class="text-[10px] text-slate-500 font-medium">{{ $todaySchedules->count() ?? 0 }} Sesi</span>
                                    </div>

                                    @if(isset($todaySchedules) && $todaySchedules->isNotEmpty())
                                        <div class="space-y-2">
                                            @foreach($todaySchedules as $item)
                                                <div data-reminder-item class="p-2.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-start justify-between gap-2">
                                                    <div>
                                                        <span class="font-bold text-slate-800 block text-xs">{{ $item->nama_matakuliah }}</span>
                                                        <span class="text-[11px] text-slate-500 block">{{ $item->jam_mulai }} - {{ $item->jam_selesai }} WIB • {{ $item->ruang }}</span>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold shrink-0 {{ $item->is_pengganti ? 'bg-[#FEE2E2] text-[#7F1D1D]' : 'bg-[#E0F2FE] text-[#0C4A6E]' }}">
                                                        {{ $item->is_pengganti ? 'Pengganti' : $item->sks . ' SKS' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-[11px] text-slate-400 italic py-1">Tidak ada jadwal kuliah hari ini.</p>
                                    @endif
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400">Agenda Non-Akademik Terdekat</span>
                                        <span class="text-[10px] text-slate-500 font-medium">{{ $upcomingAgendas->count() ?? 0 }} Kegiatan</span>
                                    </div>

                                    @if(isset($upcomingAgendas) && $upcomingAgendas->isNotEmpty())
                                        <div class="space-y-2">
                                            @foreach($upcomingAgendas as $agenda)
                                                <div data-reminder-item class="p-2.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-start justify-between gap-2">
                                                    <div class="min-w-0">
                                                        <span class="font-bold text-slate-800 block text-xs truncate">{{ $agenda->judul }}</span>
                                                        <span class="text-[11px] text-slate-500 block">{{ $agenda->tanggal_display }} • {{ $agenda->waktu }}</span>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold shrink-0 bg-[#FEF3C7] text-[#78350F] uppercase">
                                                        {{ $agenda->kategori }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-[11px] text-slate-400 italic py-1">Tidak ada agenda mendatang terdaftar.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                <a href="{{ route('jadwal.index') }}" class="font-semibold text-[#0369A1] hover:underline">Lihat Jadwal</a>
                                <a href="{{ route('agenda.index') }}" class="font-semibold text-[#0369A1] hover:underline">Lihat Agenda</a>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="text-right hidden sm:block">
                    <div class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->level_label }}</div>
                </div>

                <div class="h-9 w-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                    @csrf
                    <button class="text-sm text-red-600 hover:text-red-700 hover:underline">
                        Logout
                    </button>
                </form>

                {{-- Tombol menu mobile --}}
                <button type="button" class="md:hidden p-2 rounded hover:bg-gray-100"
                        onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        aria-label="Buka menu">
                    <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Menu mobile --}}
        <div id="mobile-menu" class="hidden md:hidden pb-3 pt-2 border-t space-y-1">
            <a href="{{ route(auth()->user()->dashboardRoute()) }}"
               class="block px-3 py-2 rounded text-sm {{ request()->routeIs('dashboard.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                Dashboard
            </a>
            <a href="{{ route('jadwal.index') }}"
               class="block px-3 py-2 rounded text-sm {{ request()->routeIs('jadwal.*') ? 'bg-[#E0F2FE] text-[#0C4A6E] font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                Jadwal Kuliah
            </a>
            <a href="{{ route('agenda.index') }}"
               class="block px-3 py-2 rounded text-sm {{ request()->routeIs('agenda.*') ? 'bg-[#E0F2FE] text-[#0C4A6E] font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                Agenda
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="block w-full text-left px-3 py-2 rounded text-sm text-red-600 hover:bg-red-50">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>