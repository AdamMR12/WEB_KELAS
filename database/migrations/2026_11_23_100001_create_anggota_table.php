<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mahasiswa anggota kelas yang wajib membayar kas
        Schema::create('anggota', function (Blueprint $table) {
            $table->id('id_anggota');
            $table->string('nim', 15)->unique();
            $table->string('nama', 100);
            $table->string('no_hp', 15)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
