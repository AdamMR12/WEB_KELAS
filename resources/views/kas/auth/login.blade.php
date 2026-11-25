@extends('kas.layouts.app')

@section('title', 'Masuk')

@push('styles')
<style>
  #gate{min-height:100dvh;display:grid;place-items:center;padding:32px 16px}
  #gate .card{max-width:440px;width:100%}
  #gate h1{font-size:1.6rem;margin-bottom:6px}
  .hbtns{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-left:auto}
  #whoami{color:var(--muted);font-weight:600;font-size:.9rem;white-space:nowrap}
  @media (max-width:480px){ #whoami{display:none} }
</style>
@endpush

@section('content')
<div id="gate">

  <section class="card" aria-labelledby="gate-title">

    <h1 id="gate-title">Masuk ke Kas Kelas SI 2A</h1>

    <p class="hint" style="margin-bottom:20px;">
      Masukkan NIM / username dan password untuk melihat dashboard kas kelas.
    </p>

    <form method="POST" action="{{ route('kas.login.attempt') }}">
      @csrf

      <div class="field">
        <label for="username">NIM / username</label>
        <input
          class="in"
          id="username"
          name="username"
          type="text"
          inputmode="numeric"
          autocomplete="username"
          required
          autofocus
          value="{{ old('username') }}"
          placeholder="Contoh: 10112001"
        >
      </div>

      <div class="field" style="margin-top:14px;">
        <label for="password">Password</label>
        <input
          class="in"
          id="password"
          name="password"
          type="password"
          autocomplete="current-password"
          required
          placeholder="Password"
        >
      </div>

      @if($errors->any())
        <p class="err" role="alert" style="margin-top:14px;">
          {{ $errors->first() }}
        </p>
      @endif

      <div class="actions" style="margin-top:18px;">
        <button type="submit" class="btn">Masuk</button>
      </div>

    </form>

  </section>

</div>
@endsection
