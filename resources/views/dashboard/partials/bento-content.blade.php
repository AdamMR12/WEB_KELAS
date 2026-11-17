@php
    $hour = (int) now()->format('H');
    $greeting = match (true) {
        $hour < 11 => 'Selamat Pagi',
        $hour < 15 => 'Selamat Siang',
        $hour < 18 => 'Selamat Sore',
        default    => 'Selamat Malam',
    };

    $primaryKelas = $daftarKelas->first();
    $userName     = auth()->user()->name;
    $userInitial  = strtoupper(mb_substr($userName, 0, 1));
@endphp

<div class="space-y-6">

    {{-- 1. HERO BANNER BENTO --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-slate-900 text-white shadow-lg">
        <div class="absolute -right-12 -top-12 h-56 w-56 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-16 left-1/3 h-48 w-48 rounded-full bg-indigo-400/20 blur-2xl pointer-events-none"></div>

        <div class="relative p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <div class="flex flex-wrap items-center gap-2 text-xs font-medium">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-sm text-white">
                        <span class="h-2 w-2 rounded-full {{ $isAdmin ? 'bg-amber-300' : 'bg-emerald-300' }}"></span>
                        Mode {{ $isAdmin ? 'Administrator' : 'Mahasiswa / Anggota' }}
                    </span>
                    @if($primaryKelas)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-500/40 border border-white/15 text-indigo-100">
                            {{ $primaryKelas->Nama_Kelas }} &bull; {{ $primaryKelas->Program_Studi }} ({{ $primaryKelas->Angkatan }})
                        </span>
                    @endif
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-900/40 text-indigo-100">
                        {{ $today->translatedFormat('l, d F Y') }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                    {{ $greeting }}, {{ $userName }}!
                </h1>
                <p class="text-sm sm:text-base text-indigo-100/90 leading-relaxed">
                    @if($isAdmin)
                        Pantau ringkasan data kelas, kepengurusan aktif, status akun pengguna, serta momen ulang tahun anggota kelas dalam satu tampilan terpadu.
                    @else
                        Selamat datang di pusat informasi kelas &amp; Memory Wall. Cek susunan pengurus kelas, pengingat ulang tahun teman sekelas, dan pintasan fitur utama di bawah ini.
                    @endif
                </p>
            </div>

            {{-- Pintasan Cepat --}}
            <div class="flex flex-wrap sm:flex-nowrap lg:flex-col gap-2.5 shrink-0">
                @foreach($quickLinks as $link)
                    <a href="{{ $link['url'] }}"
                       class="group flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 backdrop-blur-sm transition text-sm">
                        <div>
                            <div class="font-semibold text-white group-hover:translate-x-0.5 transition-transform">
                                {{ $link['label'] }}
                            </div>
                            <div class="text-[11px] text-indigo-200">{{ $link['description'] }}</div>
                        </div>
                        <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md {{ $link['available'] ? 'bg-emerald-400/20 text-emerald-200' : 'bg-white/15 text-indigo-100' }}">
                            {{ $link['available'] ? 'Buka' : 'Bagian' }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 2. BENTO ROW 1: KARTU STATISTIK RINGKAS --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Stat 1: Total Mahasiswa --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Mahasiswa</p>
                    <p class="mt-2 text-3xl font-bold text-slate-800">{{ number_format($totalMahasiswa) }}</p>
                </div>
                <div class="h-11 w-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-500">
                Terdaftar di <span class="font-medium text-slate-700">{{ $totalKelas }}</span> kelas aktif
            </p>
        </div>

        {{-- Stat 2: Pengurus Aktif --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pengurus Kelas</p>
                    <p class="mt-2 text-3xl font-bold text-slate-800">{{ number_format($totalPengurus) }}</p>
                </div>
                <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-500">
                Mengisi <span class="font-medium text-slate-700">{{ $totalJabatan }}</span> posisi struktur jabatan
            </p>
        </div>

        {{-- Stat 3: Ulang Tahun Bulan Ini / Terdekat --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Momen Ulang Tahun</p>
                    <p class="mt-2 text-3xl font-bold text-slate-800">
                        {{ $todayBirthdays->count() + $upcomingBirthdays->count() }}
                    </p>
                </div>
                <div class="h-11 w-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A1.75 1.75 0 003 15.546V19a2 2 0 002 2h14a2 2 0 002-2v-3.454zM12 4v4m0 0a2 2 0 100 4 2 2 0 000-4zm-6 4h12a2 2 0 012 2v1.5H4V10a2 2 0 012-2z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-500">
                @if($todayBirthdays->isNotEmpty())
                    <span class="font-semibold text-amber-600">{{ $todayBirthdays->count() }} berulang tahun hari ini!</span>
                @else
                    {{ $upcomingBirthdays->count() }} ulang tahun terdekat terpantau
                @endif
            </p>
        </div>

        {{-- Stat 4: Kontekstual per Role --}}
        @if($isAdmin)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Akun Pengguna</p>
                        <p class="mt-2 text-3xl font-bold text-slate-800">{{ number_format($userStats['total']) }}</p>
                    </div>
                    <div class="h-11 w-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-medium">
                        {{ $userStats['active'] }} Aktif
                    </span>
                    <span class="px-2 py-0.5 rounded-full {{ $userStats['unverified'] > 0 ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-600' }} font-medium">
                        {{ $userStats['unverified'] }} Belum Verifikasi
                    </span>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kelas Terdaftar</p>
                        <p class="mt-2 text-3xl font-bold text-slate-800">{{ number_format($totalKelas) }}</p>
                    </div>
                    <div class="h-11 w-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-xs text-slate-500 truncate">
                    {{ $primaryKelas ? $primaryKelas->Program_Studi : 'Program Studi Sistem Informasi' }}
                </p>
            </div>
        @endif
    </section>

    {{-- 3. BENTO ROW 2: BIRTHDAY SPOTLIGHT (FITUR UTAMA) --}}
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Kolom Kiri (5/12): Ulang Tahun Hari Ini --}}
        <div class="lg:col-span-5 rounded-2xl bg-gradient-to-br from-amber-50 via-orange-50/70 to-rose-50 border border-amber-200/80 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500 text-white shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Ulang Tahun Hari Ini</h2>
                            <p class="text-xs text-slate-500">{{ $today->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-800">
                        {{ $todayBirthdays->count() }} Orang
                    </span>
                </div>

                @if($todayBirthdays->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($todayBirthdays as $bday)
                            @php
                                $initials = collect(explode(' ', $bday['name']))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
                                    ->implode('');
                                $waClean = $bday['whatsapp'] ? preg_replace('/[^0-9]/', '', $bday['whatsapp']) : null;
                                if ($waClean && str_starts_with($waClean, '0')) {
                                    $waClean = '62' . substr($waClean, 1);
                                }
                                $wishText = urlencode("Selamat ulang tahun {$bday['name']}! Semoga sehat selalu, sukses kuliahnya, dan tercapai semua cita-citanya.");
                            @endphp

                            <div class="rounded-xl bg-white/90 backdrop-blur-sm border border-amber-200/70 p-4 shadow-sm">
                                <div class="flex items-center gap-3.5">
                                    @if(!empty($bday['foto']))
                                        <img src="{{ asset('storage/' . $bday['foto']) }}"
                                             alt="{{ $bday['name'] }}"
                                             class="h-12 w-12 rounded-full object-cover ring-2 ring-amber-400 shrink-0">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-amber-500 to-rose-500 text-white font-bold flex items-center justify-center ring-2 ring-amber-200 shrink-0">
                                            {{ $initials ?: '?' }}
                                        </div>
                                    @endif

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <h3 class="font-bold text-slate-800 truncate">{{ $bday['name'] }}</h3>
                                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-rose-100 text-rose-700">
                                                Hari Ini!
                                            </span>
                                        </div>
                                        <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                                            @if($bday['kelas'])
                                                <span>{{ $bday['kelas'] }}</span>
                                            @endif
                                            @if($bday['jabatan'])
                                                <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-medium">
                                                    {{ $bday['jabatan'] }}
                                                </span>
                                            @endif
                                            @if($bday['turning_age'])
                                                <span class="text-amber-700 font-medium">&bull; Ke-{{ $bday['turning_age'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($waClean)
                                    <div class="mt-3 pt-3 border-t border-amber-100 flex justify-end">
                                        <a href="https://wa.me/{{ $waClean }}?text={{ $wishText }}"
                                           target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium transition">
                                            Kirim Ucapan WhatsApp
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Empty State Estetik --}}
                    <div class="rounded-xl bg-white/75 border border-dashed border-amber-300/90 p-6 text-center">
                        <div class="mx-auto mb-3 h-12 w-12 rounded-2xl bg-amber-100/80 text-amber-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">
                            Belum Ada yang Berulang Tahun Hari Ini
                        </h3>
                        <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                            @if(!$hasBirthdayColumn)
                                Kolom tanggal lahir belum tersedia pada tabel mahasiswa/user. Saat data tanggal lahir ditambahkan, perayaan hari ini akan otomatis muncul di kartu ini.
                            @else
                                Tidak ada anggota kelas yang berulang tahun pada {{ $today->translatedFormat('d F') }}. Cek daftar ulang tahun terdekat di samping.
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-amber-200/60 flex items-center justify-between text-[11px] text-amber-900/75">
                <span>Birthday Spotlight &bull; Memory Wall</span>
                <span>Otomatis diperbarui harian</span>
            </div>
        </div>

        {{-- Kolom Kanan (7/12): Ulang Tahun Terdekat / Bulan Ini --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex flex-wrap items-center justify-between gap-2 mb-5">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Ulang Tahun Terdekat &amp; Bulan Ini</h2>
                        <p class="text-xs text-slate-500">Hitung mundur perayaan ulang tahun anggota kelas berikutnya</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">
                        {{ $today->translatedFormat('F Y') }}
                    </span>
                </div>

                @if($upcomingBirthdays->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($upcomingBirthdays as $item)
                            @php
                                $initials = collect(explode(' ', $item['name']))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
                                    ->implode('');
                                $isUrgent = $item['days_remaining'] <= 7;
                            @endphp

                            <div class="group flex items-center justify-between gap-3 p-3.5 rounded-xl border border-slate-200/80 hover:border-indigo-300 hover:bg-indigo-50/30 transition">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if(!empty($item['foto']))
                                        <img src="{{ asset('storage/' . $item['foto']) }}"
                                             alt="{{ $item['name'] }}"
                                             class="h-10 w-10 rounded-full object-cover shrink-0">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-slate-100 group-hover:bg-indigo-100 text-slate-700 group-hover:text-indigo-700 font-semibold text-xs flex items-center justify-center shrink-0 transition">
                                            {{ $initials ?: '?' }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $item['name'] }}</p>
                                        <div class="flex flex-wrap items-center gap-1.5 text-[11px] text-slate-500 mt-0.5">
                                            <span>{{ $item['next_birthday']->translatedFormat('d M') }}</span>
                                            @if($item['kelas'])
                                                <span>&bull; {{ $item['kelas'] }}</span>
                                            @endif
                                            @if($item['jabatan'])
                                                <span class="px-1.5 py-0.2 bg-slate-100 text-slate-700 rounded font-medium">
                                                    {{ $item['jabatan'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <span class="shrink-0 px-2.5 py-1 rounded-lg text-xs font-bold {{ $isUrgent ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-indigo-50 text-indigo-700' }}">
                                    H-{{ $item['days_remaining'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-8 text-center">
                        <div class="mx-auto mb-3 h-11 w-11 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Belum Ada Jadwal Ulang Tahun Terdekat</p>
                        <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                            @if(!$hasBirthdayColumn)
                                Tambahkan kolom <code class="px-1.5 py-0.5 bg-white border border-slate-200 rounded text-[11px]">Tanggal_Lahir</code> pada data mahasiswa agar pengingat hitung mundur <span class="font-medium">H-X</span> tampil di sini.
                            @else
                                Tidak ada ulang tahun dalam 45 hari ke depan. Semua momen ulang tahun terbaru akan tampil otomatis di panel ini.
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span>Menampilkan hingga 6 agenda ulang tahun terdekat</span>
                <span>Rentang pantauan: 45 hari</span>
            </div>
        </div>
    </section>

    {{-- 4. BENTO ROW 3: PENGURUS INTI KELAS & INFO KELAS / SLOT KOLABORASI --}}
    <section id="struktur-kelas-section" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Kolom Kiri (7/12): Highlight Pengurus Inti --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between gap-2 mb-5">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Pengurus Inti Kelas</h2>
                    <p class="text-xs text-slate-500">Struktur koordinasi aktif berdasarkan data kepengurusan</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                    {{ $pengurusInti->count() }} Pengurus
                </span>
            </div>

            @if($pengurusInti->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    @foreach($pengurusInti as $pengurus)
                        @php
                            $mhs      = $pengurus->mahasiswa;
                            $namaMhs  = $mhs->Nama_Mahasiswa ?? $pengurus->NIM;
                            $jabatan  = $pengurus->jabatan->Nama_Jabatan ?? 'Pengurus';
                            $kelasMhs = $mhs?->kelas?->Nama_Kelas;
                            $initials = collect(explode(' ', $namaMhs))
                                ->filter()
                                ->take(2)
                                ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
                                ->implode('');
                            $waNumber = $pengurus->No_WhatsApp ? preg_replace('/[^0-9]/', '', $pengurus->No_WhatsApp) : null;
                            if ($waNumber && str_starts_with($waNumber, '0')) {
                                $waNumber = '62' . substr($waNumber, 1);
                            }
                        @endphp

                        <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/40 hover:bg-white hover:shadow-sm transition flex flex-col justify-between gap-3">
                            <div class="flex items-center gap-3">
                                @if(!empty($mhs?->Foto))
                                    <img src="{{ asset('storage/' . $mhs->Foto) }}"
                                         alt="{{ $namaMhs }}"
                                         class="h-11 w-11 rounded-full object-cover ring-2 ring-indigo-100 shrink-0">
                                @else
                                    <div class="h-11 w-11 rounded-full bg-indigo-600 text-white font-semibold text-sm flex items-center justify-center shrink-0">
                                        {{ $initials ?: 'P' }}
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 mb-1">
                                        {{ $jabatan }}
                                    </span>
                                    <h3 class="text-sm font-bold text-slate-800 truncate">{{ $namaMhs }}</h3>
                                    <p class="text-xs text-slate-500">
                                        NIM {{ $pengurus->NIM }}{{ $kelasMhs ? ' â€¢ ' . $kelasMhs : '' }}
                                    </p>
                                </div>
                            </div>

                            @if($waNumber || $pengurus->Email)
                                <div class="pt-2.5 border-t border-slate-200/70 flex items-center gap-2 text-xs">
                                    @if($waNumber)
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-medium transition">
                                            WhatsApp
                                        </a>
                                    @endif
                                    @if($pengurus->Email)
                                        <a href="mailto:{{ $pengurus->Email }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium truncate transition">
                                            Email
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-8 text-center">
                    <p class="text-sm font-semibold text-slate-700">Belum Ada Data Pengurus Inti</p>
                    <p class="mt-1 text-xs text-slate-500">
                        Data Ketua, Wakil Ketua, Sekretaris, dan Bendahara akan tampil otomatis setelah diinput pada modul Struktur Kelas.
                    </p>
                </div>
            @endif
        </div>

        {{-- Kolom Kanan (5/12): Daftar Kelas & Slot Ekstensi Kolaborasi --}}
        <div class="lg:col-span-5 flex flex-col gap-6">
            {{-- Kartu Ringkasan Kelas --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Daftar Kelas</h2>
                        <p class="text-xs text-slate-500">Distribusi anggota per kelas</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">{{ $totalKelas }} Kelas</span>
                </div>

                @if($daftarKelas->isNotEmpty())
                    <div class="space-y-2.5">
                        @foreach($daftarKelas as $kelas)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/70">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm text-slate-800">{{ $kelas->Nama_Kelas }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-100 text-indigo-700">
                                            {{ $kelas->Angkatan }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 truncate mt-0.5">{{ $kelas->Program_Studi }}</p>
                                </div>
                                <div class="text-right shrink-0 ml-3">
                                    <span class="text-base font-bold text-slate-800">{{ $kelas->mahasiswa_count }}</span>
                                    <span class="block text-[10px] text-slate-400">Mahasiswa</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-500 py-4 text-center">Belum ada data kelas terdaftar.</p>
                @endif
            </div>

            {{-- Slot Fleksibel untuk Integrasi Kelompok Lain --}}
            <div class="bg-white rounded-2xl border border-dashed border-indigo-200 p-6 shadow-sm flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600">
                            <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                            Ruang Integrasi Modul Kelas
                        </span>
                        <span class="text-[11px] text-slate-400">Slot Kolaborasi</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Memory Wall &amp; Aktivitas Kelas</h3>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                        Area ini disiapkan sebagai wadah widget ringkasan dari modul kelompok lain (seperti galeri foto kenangan, pengumuman kelas, atau agenda terdekat) tanpa mengubah struktur grid dashboard.
                    </p>
                </div>

                @if(View::exists('dashboard.partials.external-widgets'))
                    <div class="mt-4">
                        @include('dashboard.partials.external-widgets')
                    </div>
                @endif
            </div>
        </div>
    </section>

</div>
