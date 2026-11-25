@extends('kas.layouts.app')

@section('title', 'Periode Iuran')

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
    <h1>Periode iuran</h1>
  </div>

  <div class="stack">

    <form
      class="card"
      method="POST"
      action="{{ $edit ? route('kas.periode.update', $edit) : route('kas.periode.store') }}"
    >
      @csrf
      @if($edit) @method('PUT') @endif

      <h2>{{ $edit ? 'Ubah periode' : 'Tambah periode' }}</h2>

      <div class="grid2">
        <div class="field span2">
          <label for="nama_periode">Nama periode</label>
          <input class="in" id="nama_periode" name="nama_periode" type="text" maxlength="100" required
                 placeholder="Contoh: September 2026"
                 value="{{ old('nama_periode', $edit->nama_periode ?? '') }}">
          @error('nama_periode') <small class="err">{{ $message }}</small> @enderror
        </div>

        <div class="field">
          <label for="nominal_iuran">Nominal iuran (Rp)</label>
          <input class="in" id="nominal_iuran" name="nominal_iuran" type="number" min="1" step="1" inputmode="numeric" required
                 value="{{ old('nominal_iuran', $edit->nominal_iuran ?? '') }}">
          @error('nominal_iuran') <small class="err">{{ $message }}</small> @enderror
        </div>

        <div class="field">
          <label for="jatuh_tempo">Jatuh tempo</label>
          <input class="in" id="jatuh_tempo" name="jatuh_tempo" type="date" required
                 value="{{ old('jatuh_tempo', optional($edit?->jatuh_tempo)->toDateString()) }}">
          @error('jatuh_tempo') <small class="err">{{ $message }}</small> @enderror
        </div>
      </div>

      <p class="hint" style="margin-top:10px">
        Anggota dinyatakan menunggak jika sudah melewati jatuh tempo suatu periode dan belum tercatat membayar.
      </p>

      <div class="actions">
        <button class="btn" type="submit">{{ $edit ? 'Simpan perubahan' : 'Tambah periode' }}</button>

        @if($edit)
          <a class="btn ghost" href="{{ route('kas.periode.index') }}">Batal</a>
        @endif
      </div>
    </form>


    <div class="card">
      <h2>Semua periode ({{ $periode->count() }})</h2>

      @if($periode->isEmpty())
        <p class="hint">Belum ada periode. Tambahkan yang pertama lewat formulir di atas.</p>
      @else
        <ul class="list">
          @foreach($periode as $p)
            <li class="arow">
              <div class="info">
                <strong>{{ $p->nama_periode }}</strong>
                <div class="hint">
                  {{ $rp($p->nominal_iuran) }} Â· jatuh tempo {{ $p->jatuh_tempo->translatedFormat('j F Y') }}
                  Â· {{ $p->pembayaran_count }} pembayaran
                </div>
              </div>

              <div class="btns">
                <a class="btn ghost" href="{{ route('kas.periode.index', ['edit' => $p->id_periode]) }}" aria-label="Ubah {{ $p->nama_periode }}">Ubah</a>

                @if($p->pembayaran_count === 0)
                  <form method="POST" action="{{ route('kas.periode.destroy', $p) }}"
                        onsubmit="return confirm('Hapus periode {{ addslashes($p->nama_periode) }}?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn danger" type="submit" aria-label="Hapus {{ $p->nama_periode }}">Hapus</button>
                  </form>
                @endif
              </div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>

  </div>

</section>
@endsection
