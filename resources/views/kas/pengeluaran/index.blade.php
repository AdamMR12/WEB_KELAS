@extends('kas.layouts.app')

@section('title', 'Pengeluaran')

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
    <h1>Pengeluaran kas</h1>
  </div>

  <div class="stack">

    <form
      class="card"
      method="POST"
      action="{{ $edit ? route('kas.pengeluaran.update', $edit) : route('kas.pengeluaran.store') }}"
    >
      @csrf
      @if($edit) @method('PUT') @endif

      <h2>{{ $edit ? 'Ubah pengeluaran' : 'Catat pengeluaran' }}</h2>

      <p class="hint" style="margin-bottom:14px">
        Saldo kas saat ini: <strong>{{ $rp($saldo) }}</strong>.
        Pengeluaran tidak boleh melebihi saldo yang tersedia.
      </p>

      <div class="grid2">
        <div class="field">
          <label for="tanggal">Tanggal</label>
          <input class="in" id="tanggal" name="tanggal" type="date" required
                 value="{{ old('tanggal', optional($edit?->tanggal)->toDateString() ?? now('Asia/Jakarta')->toDateString()) }}">
          @error('tanggal') <small class="err">{{ $message }}</small> @enderror
        </div>

        <div class="field">
          <label for="nominal">Nominal (Rp)</label>
          <input class="in" id="nominal" name="nominal" type="number" min="1" step="1" inputmode="numeric" required
                 value="{{ old('nominal', $edit->nominal ?? '') }}">
          @error('nominal') <small class="err">{{ $message }}</small> @enderror
        </div>

        <div class="field span2">
          <label for="keterangan">Keterangan penggunaan</label>
          <input class="in" id="keterangan" name="keterangan" type="text" maxlength="255" required
                 placeholder="Contoh: Fotokopi materi praktikum"
                 value="{{ old('keterangan', $edit->keterangan ?? '') }}">
          @error('keterangan') <small class="err">{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="actions">
        <button class="btn" type="submit">{{ $edit ? 'Simpan perubahan' : 'Catat pengeluaran' }}</button>

        @if($edit)
          <a class="btn ghost" href="{{ route('kas.pengeluaran.index') }}">Batal</a>
        @endif
      </div>
    </form>


    <div class="card">
      <h2>Riwayat pengeluaran ({{ $pengeluaran->count() }})</h2>

      @if($pengeluaran->isEmpty())
        <p class="hint">Belum ada pengeluaran tercatat.</p>
      @else
        <ul class="list">
          @foreach($pengeluaran as $k)
            <li class="arow">
              <div class="info">
                <strong>{{ $k->keterangan }}</strong>
                <div class="hint">
                  {{ $rp($k->nominal) }} Â· {{ $k->tanggal->translatedFormat('j M Y') }} Â·
                  dicatat {{ $k->pencatat?->username }}
                </div>
              </div>

              <div class="btns">
                <a class="btn ghost" href="{{ route('kas.pengeluaran.index', ['edit' => $k->id_pengeluaran]) }}" aria-label="Ubah {{ $k->keterangan }}">Ubah</a>

                <form method="POST" action="{{ route('kas.pengeluaran.destroy', $k) }}"
                      onsubmit="return confirm('Hapus pengeluaran {{ addslashes($k->keterangan) }}? Saldo kas akan dihitung ulang.')">
                  @csrf
                  @method('DELETE')
                  <button class="btn danger" type="submit" aria-label="Hapus {{ $k->keterangan }}">Hapus</button>
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
