@extends('kas.layouts.app')

@section('title', 'Laporan Kas')

@push('styles')
<style>
  .adm{padding:28px 0 48px}
  .btns{display:flex;gap:8px;flex-wrap:wrap}
  .total td{font-weight:800;border-top:2.5px solid var(--line)}
  @media print{
    .top, footer, .noprint, .skip{display:none!important}
    body{background:#fff;color:#000}
    .card{box-shadow:none;border-color:#000}
  }
</style>
@endpush

@section('content')
@php($rp = fn (int $n) => ($n < 0 ? '-' : '') . 'Rp ' . number_format(abs($n), 0, ',', '.'))

<section class="adm">

  <div class="adm-head">
    <h1>Laporan kas</h1>

    <div class="btns noprint">
      <button class="btn coin" type="button" onclick="window.print()">Cetak laporan</button>
    </div>
  </div>

  <div class="stack">

    <form class="card noprint" method="GET" action="{{ route('kas.laporan') }}">
      <h2>Filter tanggal</h2>

      <div class="grid2">
        <div class="field">
          <label for="dari">Dari tanggal</label>
          <input class="in" id="dari" name="dari" type="date" value="{{ $dari }}">
        </div>

        <div class="field">
          <label for="sampai">Sampai tanggal</label>
          <input class="in" id="sampai" name="sampai" type="date" value="{{ $sampai }}">
          @error('sampai') <small class="err">{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="actions">
        <button class="btn" type="submit">Terapkan</button>
        <a class="btn ghost" href="{{ route('kas.laporan') }}">Reset</a>
      </div>
    </form>


    <div class="card">
      <h2>Ringkasan{{ $dari || $sampai ? ' (' . ($dari ?: 'awal') . ' s.d. ' . ($sampai ?: 'sekarang') . ')' : '' }}</h2>

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

      <p class="sum" style="margin-top:16px">Selisih (pemasukan - pengeluaran): {{ $rp($selisih) }}</p>
    </div>


    <div class="card tblwrap">
      <h2 style="padding:20px 20px 0">Pemasukan per periode</h2>

      @if($perPeriode->isEmpty())
        <div class="emptybox"><strong>Belum ada periode iuran.</strong></div>
      @else
        <table class="tbl">
          <thead>
            <tr>
              <th scope="col">Periode</th>
              <th scope="col">Jatuh tempo</th>
              <th scope="col" class="num">Jumlah bayar</th>
              <th scope="col" class="num">Total</th>
            </tr>
          </thead>

          <tbody>
            @foreach($perPeriode as $row)
              <tr>
                <td>{{ $row['periode']->nama_periode }}</td>
                <td>{{ $row['periode']->jatuh_tempo->translatedFormat('j M Y') }}</td>
                <td class="num">{{ $row['jumlah'] }}</td>
                <td class="num plus">{{ $rp($row['total']) }}</td>
              </tr>
            @endforeach

            <tr class="total">
              <td colspan="3">Total pemasukan</td>
              <td class="num">{{ $rp($totalMasuk) }}</td>
            </tr>
          </tbody>
        </table>
      @endif
    </div>


    <div class="card tblwrap">
      <h2 style="padding:20px 20px 0">Pengeluaran</h2>

      @if($pengeluaran->isEmpty())
        <div class="emptybox"><strong>Tidak ada pengeluaran pada rentang ini.</strong></div>
      @else
        <table class="tbl">
          <thead>
            <tr>
              <th scope="col">Tanggal</th>
              <th scope="col">Keterangan</th>
              <th scope="col" class="num">Nominal</th>
            </tr>
          </thead>

          <tbody>
            @foreach($pengeluaran as $k)
              <tr>
                <td>{{ $k->tanggal->translatedFormat('j M Y') }}</td>
                <td>{{ $k->keterangan }}</td>
                <td class="num minus">{{ $rp($k->nominal) }}</td>
              </tr>
            @endforeach

            <tr class="total">
              <td colspan="2">Total pengeluaran</td>
              <td class="num">{{ $rp($totalKeluar) }}</td>
            </tr>
          </tbody>
        </table>
      @endif
    </div>

  </div>

</section>
@endsection
