@extends('layouts.app')

@section('title', 'Profil Kelas â€” Memory Wall')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-900 to-indigo-700 text-white shadow-lg">
        <div class="p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 text-xs text-indigo-200">
                    <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="hover:text-white transition">&larr; Dashboard</a>
                    <span>/</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-white/15 text-white font-medium">Profil Kelas</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Profil &amp; Data Kelas</h1>
                <p class="text-sm text-indigo-100/90">Identitas program studi, angkatan, dan jumlah mahasiswa terdaftar.</p>
            </div>

            @if($isAdmin)
                <a href="{{ route('kelas.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-900 font-semibold text-sm shadow-sm transition shrink-0">
                    + Tambah Kelas
                </a>
            @endif
        </div>
    </section>

    {{-- Pencarian --}}
    <section class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-xs text-slate-500">Menampilkan <strong class="text-slate-700">{{ $daftarKelas->count() }}</strong> profil kelas terdaftar.</p>
        <form method="GET" action="{{ route('kelas.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari kode, nama kelas, prodi..."
                   class="w-full sm:w-64 px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition">Cari</button>
            @if($search !== '')
                <a href="{{ route('kelas.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium">Reset</a>
            @endif
        </form>
    </section>

    {{-- Grid Kartu Profil Kelas --}}
    @if($daftarKelas->isNotEmpty())
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($daftarKelas as $kelas)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between gap-5 hover:shadow-md transition">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold tracking-wide">
                                {{ $kelas->ID_Kelas }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                                Angkatan {{ $kelas->Angkatan }}
                            </span>
                        </div>

                        <div>
                            <h2 class="text-xl font-bold text-slate-800">{{ $kelas->Nama_Kelas }}</h2>
                            <p class="text-sm text-slate-500 mt-0.5">{{ $kelas->Program_Studi }}</p>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Total Mahasiswa</span>
                            <span class="font-bold text-slate-800">{{ $kelas->mahasiswa_count }} Mahasiswa</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <a href="{{ route('kelas.show', $kelas->ID_Kelas) }}"
                           class="flex-1 text-center px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold transition">
                            Detail
                        </a>
                        @if($isAdmin)
                            <a href="{{ route('kelas.edit', $kelas->ID_Kelas) }}"
                               class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('kelas.destroy', $kelas->ID_Kelas) }}"
                                  onsubmit="return confirm('Yakin ingin menghapus kelas {{ addslashes($kelas->Nama_Kelas) }}? Semua data mahasiswa di kelas ini juga akan terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-2 rounded-xl border border-red-200 hover:bg-red-50 text-red-600 text-xs font-semibold transition">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </section>
    @else
        <section class="bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center">
            <p class="text-sm font-semibold text-slate-700">Belum Ada Data Profil Kelas</p>
            <p class="text-xs text-slate-500 mt-1">Tambahkan profil kelas pertama untuk mulai mengelola data mahasiswa.</p>
        </section>
    @endif
</div>
@endsection
