<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id('id_mk');
            $table->string('kode_mk', 20);
            $table->string('nama_mk', 100);
            $table->integer('sks')->default(3);
            $table->timestamps();
        });

        Schema::create('dosen', function (Blueprint $table) {
            $table->id('id_dosen');
            $table->string('nip', 30);
            $table->string('nama', 100);
            $table->timestamps();
        });

        Schema::create('ruangan', function (Blueprint $table) {
            $table->id('id_ruangan');
            $table->string('nama_ruangan', 100);
            $table->timestamps();
        });

        Schema::create('agenda_non_akademik', function (Blueprint $table) {
            $table->id('id_agenda');
            $table->string('nama_agenda', 150);
            $table->text('deskripsi')->nullable();
            $table->string('kategori', 100)->default('ORGANISASI');
            $table->string('status', 100)->default('Mendatang');
            $table->string('koordinator', 150)->nullable();
            $table->string('meta_label', 100)->nullable();
            $table->string('meta_value', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('agenda_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kode', 50)->unique();
            $table->string('warna_bg', 20)->default('amber');
            $table->timestamps();
        });

        Schema::create('agenda_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_statuses');
        Schema::dropIfExists('agenda_categories');
        Schema::dropIfExists('agenda_non_akademik');
        Schema::dropIfExists('ruangan');
        Schema::dropIfExists('dosen');
        Schema::dropIfExists('mata_kuliah');
    }
};
