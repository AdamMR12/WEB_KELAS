<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('id_laporan');

            // Salah satu harus diisi: media ATAU pesan yang dilaporkan
            $table->unsignedBigInteger('id_media')->nullable();
            $table->unsignedBigInteger('id_pesan')->nullable();

            $table->text('alasan');
            $table->enum('status', ['menunggu', 'ditindak', 'ditolak'])->default('menunggu');
            $table->timestamp('tanggal_laporan')->useCurrent();
            $table->timestamps();

            $table->foreign('id_media')
                  ->references('id_media')
                  ->on('media')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreign('id_pesan')
                  ->references('id_pesan')
                  ->on('pesan')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
