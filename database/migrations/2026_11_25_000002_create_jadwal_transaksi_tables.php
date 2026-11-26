<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_mk', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->foreignId('id_mk')->constrained('mata_kuliah', 'id_mk')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_dosen')->constrained('dosen', 'id_dosen')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_ruangan')->constrained('ruangan', 'id_ruangan')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('hari', 20);
            $table->date('tanggal')->nullable();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('tipe_sks', 50)->default('Praktikum Lab');
            $table->string('jenis', 50)->default('Mata Kuliah Wajib');
            $table->string('warna_bg', 20)->default('lime');
            $table->boolean('is_pengganti')->default(false);
            $table->string('catatan_pekan', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('jadwal_agenda', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->foreignId('id_agenda')->constrained('agenda_non_akademik', 'id_agenda')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_ruangan')->nullable()->constrained('ruangan', 'id_ruangan')->cascadeOnUpdate()->nullOnDelete();
            $table->date('tanggal');
            $table->date('tanggal_selesai')->nullable();
            $table->string('tanggal_display', 100)->nullable();
            $table->time('jam_mulai');
            $table->time('jam_selesai')->nullable();
            $table->string('waktu_label', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_agenda');
        Schema::dropIfExists('jadwal_mk');
    }
};
