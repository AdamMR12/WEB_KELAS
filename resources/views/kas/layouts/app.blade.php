<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="description" content="Kas Kelas SI 2A - Politeknik Negeri Subang">
  <meta name="theme-color" content="#5B3DF0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Kas Kelas') | Kas Kelas SI 2A</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('kas/css/style.css') }}">

  @stack('styles')
</head>
<body>

  <a href="#main" class="skip">Lewati ke konten utama</a>

  @auth('kas')
    @php($pengguna = auth('kas')->user())

    <header class="top">
      <div class="wrap hbar">

        <a href="{{ route('kas.dashboard') }}" class="brand" aria-label="Kembali ke dashboard Kas Kelas SI 2A">
          <span class="mark" aria-hidden="true">K</span>
          <span>Kelas SI 2A</span>
        </a>

        <nav class="nav" aria-label="Navigasi utama">
          <a href="{{ route('kas.dashboard') }}" @if(request()->routeIs('kas.dashboard')) aria-current="page" @endif>Dashboard</a>
          <a href="{{ route('kas.anggota.index') }}" @if(request()->routeIs('kas.anggota.*')) aria-current="page" @endif>Mahasiswa</a>

          @if($pengguna->isBendahara())
            <a href="{{ route('kas.pembayaran.index') }}" @if(request()->routeIs('kas.pembayaran.*')) aria-current="page" @endif>Setoran</a>
            <a href="{{ route('kas.pengeluaran.index') }}" @if(request()->routeIs('kas.pengeluaran.*')) aria-current="page" @endif>Pengeluaran</a>
            <a href="{{ route('kas.periode.index') }}" @if(request()->routeIs('kas.periode.*')) aria-current="page" @endif>Periode</a>
          @endif

          @if($pengguna->bisaKelola())
            <a href="{{ route('kas.laporan') }}" @if(request()->routeIs('kas.laporan')) aria-current="page" @endif>Laporan</a>
          @endif
        </nav>

        <div class="hbtns">
          <span class="hint" id="whoami">{{ $pengguna->nama_tampil }} ({{ $pengguna->role_label }})</span>

          <form method="POST" action="{{ route('kas.logout') }}">
            @csrf
            <button type="submit" class="btn ghost">Keluar</button>
          </form>
        </div>

      </div>
    </header>
  @endauth

  <main id="main" class="wrap">

    @if(session('sukses'))
      <div class="demo" role="status" style="margin-top:20px">{{ session('sukses') }}</div>
    @endif

    @if(session('galat'))
      <p class="err" role="alert" style="margin-top:20px">{{ session('galat') }}</p>
    @endif

    @yield('content')

  </main>

  <footer>
    <div class="wrap fbox">
      <strong>Website Kelas SI 2A</strong>
      <span>Politeknik Negeri Subang.</span>
      <span>Dikelola Kelompok 6.</span>
    </div>
  </footer>

  @stack('scripts')
</body>
</html>
