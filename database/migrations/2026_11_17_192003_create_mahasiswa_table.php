<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->string('NIM', 15)->primary();
            $table->string('Nama_Mahasiswa', 100);
            $table->string('Foto', 255)->nullable();
            $table->string('ID_Kelas', 10);
            $table->timestamps();

            $table->foreign('ID_Kelas')
                  ->references('ID_Kelas')
                  ->on('kelas')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
