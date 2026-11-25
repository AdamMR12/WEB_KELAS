@extends('kas.layouts.app')

@section('title', 'Setoran')

@push('styles')
<style>
  .adm{padding:28px 0 48px}
  .btns{display:flex;gap:8px;flex-wrap:wrap}
  .btns form{margin:0}
</style>
@endpush

@section('content')
@php($rp = fn (int $n) => 'Rp ' . number_format($n, 0, ',', '.'))

<section class="adm">

  <div class="adm-head">
    <h1>Setoran iuran</h1>
  </div>

  <div class="stack">

    <form class="card" method="POST" action="{{ route('kas.pembayaran.store') }}">
      @csrf

      <h2>Catat pembayaran</h2>

      @if($anggota->isEmpty() || $periode->isEmpty())
        <p class="hint">
          Pembayaran belum bisa dicatat.
          @if($anggota->isEmpty()) Belum ada anggota aktif. @endif
          @if($periode->isEmpty()) Belum ada periode iuran, tambahkan dulu di menu Periode. @endif
        </p>
      @else
        <div class="grid2">
          <div class="field">
            <label for="id_anggota">Anggota</label>
            <select class="in" id="id_anggota" name="id_anggota" required>
              <option value="">Pilih anggota</option>
              @foreach($anggota as $a)
                <option value="{{ $a->id_anggota }}" @selected(old('id_anggota') == $a->id_anggota)>{{ $a->nama }} ({{ $a->nim }})</option>
              @endforeach
            </select>
            @error('id_anggota') <small class="err">{{ $message }}</small> @enderror
          </div>

          <div class="field">
            <label for="id_periode">Periode</label>
            <select class="in" id="id_periode" name="id_periode" required>
              <option value="">Pilih periode</option>
              @foreach($periode as $p)
                <option value="{{ $p->id_periode }}" data-nominal="{{ $p->nominal_iuran }}" @selected(old('id_periode') == $p->id_periode)>{{ $p->nama_periode }} ({{ $rp($p->nominal_iuran) }})</option>
              @endforeach
            </select>
            @error('id_periode') <small class="err">{{ $message }}</small> @enderror
          </div>

          <div class="field">
            <label for="tanggal_bayar">Tanggal bayar</label>
            <input class="in" id="tanggal_bayar" name="tanggal_bayar" type="date" required
                   value="{{ old('tanggal_bayar', now('Asia/Jakarta')->toDateString()) }}">
            @error('tanggal_bayar') <small class="err">{{ $message }}</small> @enderror
          </div>

          <div class="field">
            <label for="nominal">Nominal (Rp)</label>
            <input class="in" id="nominal" name="nominal" type="number" min="1" step="1" inputmode="numeric" required
                   value="{{ old('nominal') }}">
            @error('nominal') <small class="err">{{ $message }}</small> @enderror
          </div>
        </div>

        <div class="actions">
          <button class="btn" type="submit">Catat pembayaran</button>
        </div>
      @endif
    </form>


    <div class="card">
      <h2>Riwayat setoran ({{ $pembayaran->count() }})</h2>

      @if($pembayaran->isEmpty())
        <p class="hint">Belum ada pembayaran tercatat.</p>
      @else
        <ul class="list">
          @foreach($pembayaran as $b)
            <li class="arow">
              <div class="info">
                <strong>{{ $b->anggota?->nama }}</strong>
                <div class="hint">
                  {{ $b->periode?->nama_periode }} Â· {{ $rp($b->nominal) }} Â·
                  {{ $b->tanggal_bayar->translatedFormat('j M Y') }} Â·
                  dicatat {{ $b->pencatat?->username }}
                </div>
              </div>

              <div class="btns">
                <form method="POST" action="{{ route('kas.pembayaran.destroy', $b) }}"
                      onsubmit="return confirm('Hapus catatan pembayaran {{ addslashes($b->anggota?->nama) }}? Saldo kas akan dihitung ulang.')">
                  @csrf
                  @method('DELETE')
                  <button class="btn danger" type="submit" aria-label="Hapus pembayaran {{ $b->anggota?->nama }}">Hapus</button>
                </form>
              </div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>

  </div>

</section>
@endsection

@push('scripts')
<script>
(function () {
  'use strict';

  var periode = document.getElementById('id_periode');
  var nominal = document.getElementById('nominal');
  if (!periode || !nominal) return;

  // Isi nominal otomatis dari periode yang dipilih (masih bisa diubah).
  periode.addEventListener('change', function () {
    var opt = periode.options[periode.selectedIndex];
    if (opt && opt.dataset.nominal) nominal.value = opt.dataset.nominal;
  });
})();
</script>
@endpush
