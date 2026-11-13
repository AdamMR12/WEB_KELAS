@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-2">Dashboard</h1>
    <p class="text-gray-600 mb-6">
        Halo, {{ auth()->user()->name }}. Selamat datang di <strong>Memory Wall</strong>.
    </p>

    <div class="p-4 bg-indigo-50 rounded-lg text-sm text-gray-600">
        Belum ada memori. Fitur untuk menambah dan melihat memori akan tersedia di sini.
    </div>
</div>
@endsection