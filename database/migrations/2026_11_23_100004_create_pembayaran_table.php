<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catatan pembayaran kas oleh seorang anggota untuk satu periode
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->unsignedBigInteger('id_anggota');
            $table->unsignedBigInteger('id_periode');
            $table->unsignedBigInteger('id_user'); // pencatat (bendahara)
            $table->date('tanggal_bayar');
            $table->unsignedInteger('nominal');
            $table->timestamps();

            // Satu anggota hanya punya satu catatan pembayaran per periode
            $table->unique(['id_anggota', 'id_periode'], 'uq_pembayaran_anggota_periode');
            $table->index('id_periode');
            $table->index('id_user');
            $table->index('tanggal_bayar');

            // Data transaksi tidak dihapus sembarangan agar saldo & laporan tetap bisa ditelusuri
            $table->foreign('id_anggota')
                  ->references('id_anggota')->on('anggota')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_periode')
                  ->references('id_periode')->on('periode')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_user')
                  ->references('id_user')->on('pengguna')
                  ->onUpdate('cascade')->onDelete('restrict');
        });

        // Nominal pembayaran harus lebih dari nol
        DB::statement('ALTER TABLE pembayaran ADD CONSTRAINT chk_pembayaran_nominal CHECK (nominal > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
