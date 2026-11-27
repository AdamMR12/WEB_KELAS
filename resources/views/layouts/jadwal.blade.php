<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Jadwal Perkuliahan & Agenda') - Web Kelas</title>
    <link rel="stylesheet" href="{{ asset('assets-jadwal/style.css') }}?v=6">
    <script type="module" src="{{ asset('assets-jadwal/script.js') }}?v=6"></script>
</head>
<body class="min-h-screen bg-[#F8FAFC] text-[#0F172A] flex flex-col font-sans antialiased selection:bg-[#DBEAFE] selection:text-[#1E3A8A]">

    @auth
        @include('partials.navbar')
    @endauth

    <main data-page class="flex-1 container mx-auto py-6 px-4 flex flex-col">
        <!-- HERO STRIP -->
        <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-3 mb-5">
            <div>
                <h1 data-anim="title" class="text-xl md:text-2xl font-bold text-[#0F172A] tracking-tight">
                    @yield('page_title')
                </h1>
                <p data-anim="hero" class="text-[#475569] text-xs mt-0.5">
                    @yield('page_subtitle', 'Semester Ganjil 2026/2027 | D4 Sistem Informasi')
                </p>
            </div>

            <!-- Stats & Primary Action -->
            <div data-anim="hero" class="flex items-center gap-4 md:gap-5">
                @yield('hero_stats')

                @yield('hero_action')
            </div>
        </div>

        <!-- FLASH MESSAGE -->
        @if(session('success'))
            <div data-flash role="status" class="mb-4 px-4 py-2.5 rounded-2xl bg-[#DBEAFE] border border-[#BFDBFE] text-[#1E3A8A] text-xs font-semibold flex items-center justify-between shadow-xs">
                <span>{{ session('success') }}</span>
                <button type="button" aria-label="Tutup notifikasi" class="text-[#1E3A8A]/80 hover:text-[#1E3A8A] text-base leading-none font-bold cursor-pointer">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div data-flash role="status" class="mb-4 px-4 py-2.5 rounded-2xl bg-red-100 border border-red-200 text-red-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                <span>{{ session('error') }}</span>
                <button type="button" aria-label="Tutup notifikasi" class="text-red-800/80 hover:text-red-900 text-base leading-none font-bold cursor-pointer">&times;</button>
            </div>
        @endif

        <!-- CONTENT BODY -->
        <div class="relative z-0 flex-1">
            @yield('content')
        </div>
    </main>

    <!-- MODAL INJECTION (if any) -->
    @yield('modals')

</body>
</html>
