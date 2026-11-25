@extends('kas.layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
  $rp = fn (int $n) => ($n < 0 ? '-' : '') . 'Rp ' . number_format(abs($n), 0, ',', '.');
  $terlihatSemua = $pengguna->bisaKelola();
@endphp

<div id="publicView">

  <section class="hero" id="dashboard" aria-labelledby="dashboard-title">

    <div>
      <p class="hint" style="margin-bottom:8px;">DASHBOARD KELAS</p>

      <h1 id="dashboard-title">Kas kelas SI 2A, transparan untuk semua.</h1>

      <p class="lead">
        Lihat saldo kas, cek status iuran, dan pantau riwayat transaksi kelas.
      </p>

      <div class="saldo">
        <span class="coin" aria-hidden="true">Rp</span>
        <p class="lbl">Saldo kas saat ini</p>
        <p class="big" id="saldoNum">{{ $rp($saldo) }}</p>
        <p class="upd">
          Data diperbarui:
          <strong>{{ $terakhir->timezone('Asia/Jakarta')->translatedFormat('j F Y, H.i') }} WIB</strong>
        </p>
      </div>

      <div class="mini">
        <div class="in">
          <p>Total pemasukan</p>
          <strong>{{ $rp($totalMasuk) }}</strong>
        </div>

        <div class="out">
          <p>Total pengeluaran</p>
          <strong>{{ $rp($totalKeluar) }}</strong>
        </div>
      </div>
    </div>

    <section class="card cek" id="cek-iuran" aria-labelledby="cek-title">

      <h2 id="cek-title">Cek status iuran</h2>

      @if($terlihatSemua)
        <p class="hint">Cari menggunakan nama atau NIM mahasiswa.</p>

        <form method="GET" action="{{ route('kas.dashboard') }}#cek-iuran">
          <input type="hidden" name="jenis" value="{{ $jenis }}">

          <div class="sbox" style="margin-top:14px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <circle cx="11" cy="11" r="7"/>
              <path d="m20 20-4-4"/>
            </svg>

            <label for="q" class="sr">Cari mahasiswa berdasarkan nama atau NIM</label>

            <input
              type="search"
              id="q"
              name="q"
              class="in search"
              placeholder="Cari nama atau NIM..."
              autocomplete="off"
              value="{{ $q }}"
            >
          </div>
        </form>
      @else
        <p class="hint">Berikut status iuran milik kamu.</p>
      @endif

      @if($iuran['jumlah'])
        <p class="sum">{{ $iuran['lunas'] }} dari {{ $iuran['jumlah'] }} mahasiswa sudah lunas</p>
      @endif

      <div
        class="meter"
        role="progressbar"
        aria-label="Persentase mahasiswa yang sudah lunas"
        aria-valuemin="0"
        aria-valuemax="100"
        aria-valuenow="{{ $iuran['persen'] }}"
      >
        <span style="width:{{ $iuran['persen'] }}%"></span>
      </div>

      <ul class="res" aria-live="polite">
        @forelse($iuran['daftar'] as $item)
          @php($a = $item['anggota'])
          <li class="r">
            <span class="av" aria-hidden="true" style="background:{{ ['#FFC83D','#8BE8C2','#FFB3C1','#9CD3FF','#C9BDFF'][crc32($a->nim) % 5] }}">{{ $a->inisial }}</span>

            <div class="rn">
              <strong>{{ $a->nama }}</strong>
              <span class="nim">{{ $a->nim }}</span>
            </div>

            @if($item['status']['lunas'])
              <span class="badge ok"><span>âœ“ Lunas</span></span>
            @else
              <span class="badge no">
                <span>Belum lunas</span>
                <small>Tunggakan {{ $rp($item['status']['tunggakan']) }}</small>
              </span>
            @endif
          </li>
        @empty
          <li class="none">
            <strong>{{ $iuran['jumlah'] ? 'Data tidak ditemukan.' : 'Daftar mahasiswa belum diisi.' }}</strong>
            {{ $iuran['jumlah'] ? 'Coba ketik nama depan atau NIM lengkap.' : 'Admin bisa menambahkannya di menu Mahasiswa.' }}
          </li>
        @endforelse
      </ul>

    </section>

  </section>


  <section class="hist" id="riwayat" aria-labelledby="riwayat-title">

    <div class="hist-head">
      <div>
        <h2 id="riwayat-title">Riwayat transaksi</h2>
        <p class="sub">Pantau pemasukan dan pengeluaran kas kelas secara transparan.</p>
      </div>

      @if($terlihatSemua)
        <div class="chips" role="group" aria-label="Filter jenis transaksi">
          @foreach(['semua' => 'Semua', 'masuk' => 'Pemasukan', 'keluar' => 'Pengeluaran'] as $kode => $label)
            <a
              class="chip"
              style="display:inline-flex;align-items:center;text-decoration:none"
              href="{{ route('kas.dashboard', array_filter(['jenis' => $kode, 'q' => $q])) }}#riwayat"
              aria-pressed="{{ $jenis === $kode ? 'true' : 'false' }}"
            >{{ $label }}</a>
          @endforeach
        </div>
      @endif
    </div>

    <div class="card tblwrap">

      @if($riwayat->isNotEmpty())
        <table class="tbl">
          <thead>
            <tr>
              <th scope="col">Tanggal</th>
              <th scope="col">Jenis</th>
              <th scope="col">Keterangan</th>
              <th scope="col" class="num">Nominal</th>
              @if($terlihatSemua)
                <th scope="col" class="num">Saldo</th>
              @endif
            </tr>
          </thead>

          <tbody>
            @foreach($riwayat as $r)
              @php($masuk = $r['jenis'] === 'masuk')
              <tr>
                <td class="tgl">{{ $r['tanggal']->translatedFormat('j M Y') }}</td>

                <td class="jns">
                  <span class="badge tag {{ $masuk ? 'ok' : 'no' }}">{{ $masuk ? 'Pemasukan' : 'Pengeluaran' }}</span>
                </td>

                <td class="ket">
                  <span>{{ $r['keterangan'] }}</span>
                  @if($r['dari'])
                    <span class="sub">Dari {{ $r['dari'] }}</span>
                  @endif
                </td>

                <td class="num nom {{ $masuk ? 'plus' : 'minus' }}">
                  {{ $masuk ? '+' : '-' }}{{ $rp($r['nominal']) }}
                </td>

                @if($terlihatSemua)
                  <td class="num sal">{{ $rp($r['saldo']) }}</td>
                @endif
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <div class="emptybox">
          <strong>Belum ada transaksi.</strong>
          <span>Riwayat transaksi akan muncul di sini setelah kas mulai dicatat.</span>
        </div>
      @endif

    </div>

  </section>

</div>
@endsection
