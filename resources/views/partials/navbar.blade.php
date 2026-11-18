<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">

            {{-- Kiri: Logo + menu utama --}}
            <div class="flex items-center gap-6">
                <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                   class="inline-flex items-center gap-2 font-bold text-lg text-indigo-600">
                    <svg class="w-5 h-5 text-indigo-600 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>Memory Wall</span>
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                       class="px-3 py-2 rounded text-sm hover:bg-gray-100 {{ request()->routeIs('dashboard.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('kelas.index') }}"
                       class="px-3 py-2 rounded text-sm hover:bg-gray-100 {{ request()->routeIs('kelas.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                        Profil Kelas
                    </a>

                    <a href="{{ route('mahasiswa.index') }}"
                       class="px-3 py-2 rounded text-sm hover:bg-gray-100 {{ request()->routeIs('mahasiswa.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                        Mahasiswa
                    </a>

                    <a href="{{ route('struktur-kelas.index') }}"
                       class="px-3 py-2 rounded text-sm hover:bg-gray-100 {{ request()->routeIs('struktur-kelas.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                        Struktur Kelas
                    </a>

                    <a href="{{ route('laporan.index') }}"
                       class="px-3 py-2 rounded text-sm hover:bg-gray-100 {{ request()->routeIs('laporan.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                        Laporan
                    </a>

                    {{-- Placeholder: ganti '#' dengan route saat fiturnya sudah dibuat --}}
                    <a href="#" class="px-3 py-2 rounded text-sm text-gray-400 cursor-not-allowed" title="Segera">
                        Memory Wall
                    </a>
                </div>
            </div>

            {{-- Kanan: info user + logout --}}
            <div class="flex items-center gap-3">
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
            <a href="{{ route('kelas.index') }}"
               class="block px-3 py-2 rounded text-sm {{ request()->routeIs('kelas.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                Profil Kelas
            </a>
            <a href="{{ route('mahasiswa.index') }}"
               class="block px-3 py-2 rounded text-sm {{ request()->routeIs('mahasiswa.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                Mahasiswa
            </a>
            <a href="{{ route('struktur-kelas.index') }}"
               class="block px-3 py-2 rounded text-sm {{ request()->routeIs('struktur-kelas.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                Struktur Kelas
            </a>
            <a href="{{ route('laporan.index') }}"
               class="block px-3 py-2 rounded text-sm {{ request()->routeIs('laporan.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                Laporan
            </a>
            <a href="#" class="block px-3 py-2 rounded text-sm text-gray-400">Memory Wall</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="block w-full text-left px-3 py-2 rounded text-sm text-red-600 hover:bg-red-50">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>
