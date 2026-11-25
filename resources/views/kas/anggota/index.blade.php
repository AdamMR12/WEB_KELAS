@extends('kas.layouts.app')

@section('title', 'Mahasiswa')

@push('styles')
<style>
  .mhead{padding:32px 0 8px}
  .mhead h1{font-size:clamp(1.9rem,5vw,2.8rem)}
  .mhead .lead{margin-top:10px;color:var(--muted);max-width:52ch}
  .mtoolbar{margin:22px 0 18px}
  .mcount{margin-top:10px;color:var(--muted);font-weight:600;font-size:.95rem}
  .mlist{list-style:none;margin:0 0 40px;padding:0;display:grid;gap:10px}
  .mrow{display:flex;align-items:center;flex-wrap:wrap;gap:10px 14px;padding:14px;border:2px solid var(--rule);border-radius:16px;background:var(--surface)}
  .mrow .idx{flex:none;min-width:52px;color:var(--muted);font-weight:700;font-size:.85rem}
  .mrow .rn{flex:1 1 160px}
  .copyBtn{flex:none;min-height:40px;padding:0 14px;font-size:.92rem}
  .copyBtn.done{background:var(--in-bg);color:var(--in-ink);border-color:var(--in-ink)}
  .mrow.off{opacity:.6}
  .formAdm{margin-bottom:28px}
  .btns{display:flex;gap:8px;flex-wrap:wrap}
</style>
@endpush

@section('content')
@php($pengguna = auth('kas')->user())

<section class="mhead" aria-labelledby="mahasiswa-title">

  <p class="hint" style="margin-bottom:8px;">DAFTAR MAHASISWA</p>

  <h1 id="mahasiswa-title">Mahasiswa kelas SI 2A</h1>

  <p class="lead">
    Daftar lengkap nama dan NIM mahasiswa kelas SI 2A.
    Ketuk tombol Salin NIM untuk menyalin NIM ke clipboard.
  </p>

  <form method="GET" action="{{ route('kas.anggota.index') }}" class="sbox mtoolbar">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
      <circle cx="11" cy="11" r="7"/>
      <path d="m20 20-4-4"/>
    </svg>

    <label for="mq" class="sr">Cari mahasiswa berdasarkan nama atau NIM</label>

    <input
      type="search"
      id="mq"
      name="q"
      class="in search"
      placeholder="Cari nama atau NIM..."
      autocomplete="off"
      value="{{ $q }}"
    >
  </form>

  <p class="mcount">
    @if($q)
      {{ $anggota->count() }} dari {{ $total }} mahasiswa cocok dengan pencarian
    @else
      {{ $total }} mahasiswa terdaftar
    @endif
  </p>

</section>


{{-- Form tambah / ubah: hanya admin --}}
@if($pengguna->isAdmin())
  <form
    class="card formAdm"
    method="POST"
    action="{{ $edit ? route('kas.anggota.update', $edit) : route('kas.anggota.store') }}"
  >
    @csrf
    @if($edit) @method('PUT') @endif

    <h2>{{ $edit ? 'Ubah mahasiswa' : 'Tambah mahasiswa' }}</h2>

    <div class="grid2">
      <div class="field">
        <label for="nim">NIM</label>
        <input class="in" id="nim" name="nim" type="text" inputmode="numeric" maxlength="15" required value="{{ old('nim', $edit->nim ?? '') }}">
        @error('nim') <small class="err">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label for="nama">Nama lengkap</label>
        <input class="in" id="nama" name="nama" type="text" maxlength="100" required value="{{ old('nama', $edit->nama ?? '') }}">
        @error('nama') <small class="err">{{ $message }}</small> @enderror
      </div>

      <div class="field">
        <label for="no_hp">No. HP (opsional)</label>
        <input class="in" id="no_hp" name="no_hp" type="text" inputmode="tel" maxlength="15" value="{{ old('no_hp', $edit->no_hp ?? '') }}">
        @error('no_hp') <small class="err">{{ $message }}</small> @enderror
      </div>

      @if($edit)
        <div class="field">
          <label for="status">Status</label>
          <select class="in" id="status" name="status">
            <option value="aktif" @selected(old('status', $edit->status) === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(old('status', $edit->status) === 'nonaktif')>Nonaktif</option>
          </select>
        </div>
      @endif
    </div>

    <div class="actions">
      <button class="btn" type="submit">{{ $edit ? 'Simpan perubahan' : 'Tambah mahasiswa' }}</button>

      @if($edit)
        <a class="btn ghost" href="{{ route('kas.anggota.index') }}">Batal</a>
      @endif
    </div>
  </form>
@endif


<ul class="mlist" id="mlist" aria-live="polite">
  @forelse($anggota as $i => $a)
    <li class="mrow {{ $a->status === 'nonaktif' ? 'off' : '' }}">

      <span class="idx" aria-hidden="true">No. {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>

      <span class="av" aria-hidden="true" style="background:{{ ['#FFC83D','#8BE8C2','#FFB3C1','#9CD3FF','#C9BDFF'][crc32($a->nim) % 5] }}">{{ $a->inisial }}</span>

      <div class="rn">
        <strong>{{ $a->nama }}</strong>
        <span class="nim">NIM {{ $a->nim }}@if($a->status === 'nonaktif') Â· nonaktif @endif</span>
      </div>

      <div class="btns">
        <button
          class="btn ghost copyBtn"
          type="button"
          data-nim="{{ $a->nim }}"
          aria-label="Salin NIM {{ $a->nama }}"
        >Salin NIM</button>

        @if($pengguna->isAdmin())
          <a class="btn ghost copyBtn" href="{{ route('kas.anggota.index', ['edit' => $a->id_anggota]) }}" aria-label="Ubah {{ $a->nama }}">Ubah</a>

          @if($a->status === 'aktif')
            <form method="POST" action="{{ route('kas.anggota.nonaktif', $a) }}" onsubmit="return confirm('Nonaktifkan {{ addslashes($a->nama) }}? Riwayat pembayarannya tetap tersimpan.')">
              @csrf
              @method('PATCH')
              <button class="btn danger copyBtn" type="submit" aria-label="Nonaktifkan {{ $a->nama }}">Nonaktifkan</button>
            </form>
          @endif
        @endif
      </div>

    </li>
  @empty
    <li class="none">
      <strong>Data tidak ditemukan.</strong>
      Coba ketik nama depan atau NIM lengkap.
    </li>
  @endforelse
</ul>

<div id="toast" role="status" aria-live="polite"></div>
@endsection

@push('scripts')
<script>
(function () {
  'use strict';

  var toastTimer;
  function toast(msg) {
    var t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { t.classList.remove('show'); }, 3200);
  }

  async function copyNim(nim, btn) {
    var ok = false;

    try {
      await navigator.clipboard.writeText(nim);
      ok = true;
    } catch (e) {
      try {
        var ta = document.createElement('textarea');
        ta.value = nim;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        ok = true;
      } catch (e2) { ok = false; }
    }

    if (ok) {
      toast('NIM ' + nim + ' disalin.');
      var orig = btn.textContent;
      btn.textContent = 'Tersalin \u2713';
      btn.classList.add('done');
      setTimeout(function () { btn.textContent = orig; btn.classList.remove('done'); }, 1600);
    } else {
      toast('Gagal menyalin NIM. Salin manual: ' + nim);
    }
  }

  document.querySelectorAll('.copyBtn[data-nim]').forEach(function (btn) {
    btn.addEventListener('click', function () { copyNim(btn.dataset.nim, btn); });
  });
})();
</script>
@endpush
