@extends('layouts.app')

@section('title', 'Struktur Kelas & Pengurus â€” Memory Wall')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & FILTER KELAS --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-900 to-indigo-700 text-white shadow-lg">
        <div class="absolute -right-10 -top-10 h-52 w-52 rounded-full bg-indigo-400/20 blur-2xl pointer-events-none"></div>
        <div class="relative p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-2.5 max-w-2xl">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                       class="inline-flex items-center gap-1 text-indigo-200 hover:text-white transition">
                        &larr; Kembali ke Dashboard
                    </a>
                    <span class="text-indigo-400">/</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-white/15 font-medium">Kelompok 4 &bull; Struktur Kelas</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                    Struktur Kelas &amp; Kepengurusan
                </h1>
                <p class="text-sm text-indigo-100/90 leading-relaxed">
                    Susunan pengurus aktif serta direktori lengkap anggota kelas{{ $activeKelas ? ' ' . $activeKelas->Nama_Kelas . ' (' . $activeKelas->Program_Studi . ')' : '' }}.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($isAdmin)
                    <button type="button"
                            onclick="openCreateModal()"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-900 font-semibold text-sm shadow-sm transition">
                        <span>+ Tambah / Atur Pengurus</span>
                    </button>
                @endif
            </div>
        </div>
    </section>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            <p class="font-semibold mb-1">Terjadi kesalahan input:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 2. TAB FILTER KELAS & RINGKASAN --}}
    <section class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('struktur-kelas.index', array_filter(['q' => $search])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ !$selectedKelasId ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Semua Kelas
            </a>
            @foreach($daftarKelas as $kelas)
                <a href="{{ route('struktur-kelas.index', array_filter(['kelas' => $kelas->ID_Kelas, 'q' => $search])) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ $selectedKelasId === $kelas->ID_Kelas ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ $kelas->Nama_Kelas }} ({{ $kelas->mahasiswa_count }})
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('struktur-kelas.index') }}" class="flex items-center gap-2 w-full md:w-auto">
            @if($selectedKelasId)
                <input type="hidden" name="kelas" value="{{ $selectedKelasId }}">
            @endif
            <input type="text"
                   name="q"
                   value="{{ $search }}"
                   placeholder="Cari nama atau NIM..."
                   class="w-full md:w-64 px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit"
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition">
                Cari
            </button>
            @if($search !== '')
                <a href="{{ route('struktur-kelas.index', array_filter(['kelas' => $selectedKelasId])) }}"
                   class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium">
                    Reset
                </a>
            @endif
        </form>
    </section>

    {{-- 3. BAGAN STRUKTUR KEPENGURUSAN INTI --}}
    <section class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Struktur Pengurus Inti</h2>
                <p class="text-xs text-slate-500">Koordinator utama dan perangkat kepengurusan kelas</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">
                {{ $daftarPengurus->count() }} Pengurus Aktif
            </span>
        </div>

        @if($daftarPengurus->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($daftarPengurus as $index => $pengurus)
                    @php
                        $mhs      = $pengurus->mahasiswa;
                        $namaMhs  = $mhs->Nama_Mahasiswa ?? $pengurus->NIM;
                        $jabatan  = $pengurus->jabatan->Nama_Jabatan ?? 'Pengurus';
                        $kelasMhs = $mhs?->kelas?->Nama_Kelas;
                        $isTop    = $index === 0;
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

                    <div class="relative rounded-2xl border {{ $isTop ? 'border-indigo-300 bg-gradient-to-b from-indigo-50/70 to-white' : 'border-slate-200/80 bg-slate-50/40' }} p-5 flex flex-col justify-between gap-4 hover:shadow-md transition">
                        <div class="flex flex-col items-center text-center">
                            <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide mb-3 {{ $isTop ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-700' }}">
                                {{ $jabatan }}
                            </span>

                            @if(!empty($mhs?->Foto))
                                <img src="{{ asset('storage/' . $mhs->Foto) }}"
                                     alt="{{ $namaMhs }}"
                                     class="h-16 w-16 rounded-full object-cover ring-4 ring-white shadow-sm mb-3">
                            @else
                                <div class="h-16 w-16 rounded-full {{ $isTop ? 'bg-indigo-600' : 'bg-slate-700' }} text-white font-bold text-lg flex items-center justify-center ring-4 ring-white shadow-sm mb-3">
                                    {{ $initials ?: 'P' }}
                                </div>
                            @endif

                            <h3 class="font-bold text-slate-800 text-sm leading-snug">{{ $namaMhs }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                NIM {{ $pengurus->NIM }}{{ $kelasMhs ? ' â€¢ ' . $kelasMhs : '' }}
                            </p>
                        </div>

                        <div class="space-y-2 pt-3 border-t border-slate-200/70">
                            @if($waNumber || $pengurus->Email)
                                <div class="flex items-center justify-center gap-2 text-xs">
                                    @if($waNumber)
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener"
                                           class="flex-1 text-center px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold transition">
                                            WhatsApp
                                        </a>
                                    @endif
                                    @if($pengurus->Email)
                                        <a href="mailto:{{ $pengurus->Email }}"
                                           class="flex-1 text-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">
                                            Email
                                        </a>
                                    @endif
                                </div>
                            @endif

                            @if($isAdmin)
                                <div class="flex items-center justify-center gap-2 pt-1">
                                    <button type="button"
                                            onclick="openEditModal('{{ $pengurus->NIM }}', '{{ addslashes($namaMhs) }}', '{{ $pengurus->ID_Jabatan }}', '{{ addslashes($pengurus->No_WhatsApp ?? '') }}', '{{ addslashes($pengurus->Email ?? '') }}')"
                                            class="flex-1 px-2.5 py-1.5 rounded-lg border border-indigo-200 bg-white hover:bg-indigo-50 text-indigo-700 text-xs font-semibold transition">
                                        Edit
                                    </button>
                                    <form method="POST"
                                          action="{{ route('struktur-kelas.pengurus.destroy', $pengurus->NIM) }}"
                                          onsubmit="return confirm('Hapus {{ addslashes($namaMhs) }} dari daftar pengurus?')"
                                          class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full px-2.5 py-1.5 rounded-lg border border-red-200 bg-white hover:bg-red-50 text-red-600 text-xs font-semibold transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-8 text-center">
                <p class="text-sm font-semibold text-slate-700">Belum Ada Data Pengurus Terdaftar</p>
                <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                    @if($isAdmin)
                        Klik tombol <strong>+ Tambah / Atur Pengurus</strong> di atas untuk menetapkan Ketua, Wakil Ketua, Sekretaris, atau Bendahara kelas.
                    @else
                        Susunan pengurus kelas akan tampil otomatis setelah ditetapkan oleh Administrator.
                    @endif
                </p>
            </div>
        @endif
    </section>

    {{-- 4. DIREKTORI ANGGOTA / MAHASISWA KELAS --}}
    <section class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Direktori Anggota Kelas</h2>
                <p class="text-xs text-slate-500">Daftar mahasiswa terdaftar beserta status kepengurusan</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                {{ $daftarMahasiswa->count() }} Mahasiswa
            </span>
        </div>

        @if($daftarMahasiswa->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-4">Mahasiswa</th>
                            <th class="py-3 px-4">NIM</th>
                            <th class="py-3 px-4">Kelas</th>
                            <th class="py-3 px-4">Peran / Jabatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($daftarMahasiswa as $mhs)
                            @php
                                $initials = collect(explode(' ', $mhs->Nama_Mahasiswa))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
                                    ->implode('');
                                $jabatanMhs = $mhs->pengurus?->jabatan?->Nama_Jabatan;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        @if(!empty($mhs->Foto))
                                            <img src="{{ asset('storage/' . $mhs->Foto) }}"
                                                 alt="{{ $mhs->Nama_Mahasiswa }}"
                                                 class="h-9 w-9 rounded-full object-cover shrink-0">
                                        @else
                                            <div class="h-9 w-9 rounded-full bg-indigo-100 text-indigo-700 font-semibold text-xs flex items-center justify-center shrink-0">
                                                {{ $initials ?: '?' }}
                                            </div>
                                        @endif
                                        <span class="font-semibold text-slate-800">{{ $mhs->Nama_Mahasiswa }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-mono text-xs">{{ $mhs->NIM }}</td>
                                <td class="py-3.5 px-4 text-slate-600 text-xs">
                                    {{ $mhs->kelas->Nama_Kelas ?? $mhs->ID_Kelas }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($jabatanMhs)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                            {{ $jabatanMhs }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                            Anggota Kelas
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-8 text-center">
                <p class="text-sm font-semibold text-slate-700">Data Mahasiswa Tidak Ditemukan</p>
                <p class="mt-1 text-xs text-slate-500">
                    Belum ada data mahasiswa yang sesuai dengan filter atau kata kunci pencarian.
                </p>
            </div>
        @endif
    </section>

</div>

{{-- MODAL TAMBAH / EDIT PENGURUS (KHUSUS ADMIN) --}}
@if($isAdmin)
    <div id="pengurusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 id="modalTitle" class="text-base font-bold text-slate-800">Tambah / Atur Pengurus</h3>
                <button type="button" onclick="closePengurusModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
            </div>

            <form id="pengurusForm" method="POST" action="{{ route('struktur-kelas.pengurus.store') }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST" disabled>

                <div id="nimSelectWrapper">
                    <label for="inputNIM" class="block text-xs font-semibold text-slate-700 mb-1">Pilih Mahasiswa</label>
                    <select id="inputNIM" name="NIM" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach($kandidatMahasiswa as $kandidat)
                            <option value="{{ $kandidat->NIM }}">
                                {{ $kandidat->Nama_Mahasiswa }} ({{ $kandidat->NIM }}{{ $kandidat->kelas ? ' - ' . $kandidat->kelas->Nama_Kelas : '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="nimReadonlyWrapper" class="hidden">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mahasiswa</label>
                    <div id="readonlyMahasiswaLabel" class="px-3.5 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-medium"></div>
                </div>

                <div>
                    <label for="inputJabatan" class="block text-xs font-semibold text-slate-700 mb-1">Jabatan</label>
                    <select id="inputJabatan" name="ID_Jabatan" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($daftarJabatan as $jab)
                            <option value="{{ $jab->ID_Jabatan }}">{{ $jab->Nama_Jabatan }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="inputWA" class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp (Opsional)</label>
                    <input type="text" id="inputWA" name="No_WhatsApp" placeholder="Contoh: 081234567890"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="inputEmail" class="block text-xs font-semibold text-slate-700 mb-1">Email (Opsional)</label>
                    <input type="email" id="inputEmail" name="Email" placeholder="Contoh: pengurus@kampus.ac.id"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closePengurusModal()"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition">
                        Simpan Pengurus
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            const modal = document.getElementById('pengurusModal');
            const form = document.getElementById('pengurusForm');
            const methodInput = document.getElementById('formMethod');

            document.getElementById('modalTitle').textContent = 'Tambah / Atur Pengurus Kelas';
            form.action = "{{ route('struktur-kelas.pengurus.store') }}";
            methodInput.disabled = true;
            methodInput.value = 'POST';

            document.getElementById('nimSelectWrapper').classList.remove('hidden');
            document.getElementById('inputNIM').disabled = false;
            document.getElementById('inputNIM').value = '';
            document.getElementById('nimReadonlyWrapper').classList.add('hidden');

            document.getElementById('inputJabatan').value = '';
            document.getElementById('inputWA').value = '';
            document.getElementById('inputEmail').value = '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openEditModal(nim, nama, jabatanId, wa, email) {
            const modal = document.getElementById('pengurusModal');
            const form = document.getElementById('pengurusForm');
            const methodInput = document.getElementById('formMethod');

            document.getElementById('modalTitle').textContent = 'Edit Pengurus Kelas';
            form.action = "{{ url('/struktur-kelas/pengurus') }}/" + encodeURIComponent(nim);
            methodInput.disabled = false;
            methodInput.value = 'PUT';

            document.getElementById('nimSelectWrapper').classList.add('hidden');
            document.getElementById('inputNIM').disabled = true;
            document.getElementById('nimReadonlyWrapper').classList.remove('hidden');
            document.getElementById('readonlyMahasiswaLabel').textContent = nama + ' (' + nim + ')';

            document.getElementById('inputJabatan').value = jabatanId;
            document.getElementById('inputWA').value = wa || '';
            document.getElementById('inputEmail').value = email || '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePengurusModal() {
            const modal = document.getElementById('pengurusModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endif
@endsection
