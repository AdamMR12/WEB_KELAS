<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catatan penggunaan dana kas kelas
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id('id_pengeluaran');
            $table->unsignedBigInteger('id_user'); // pencatat (bendahara)
            $table->date('tanggal')->index();
            $table->string('keterangan', 255);
            $table->unsignedInteger('nominal');
            $table->timestamps();

            $table->index('id_user');

            // Data transaksi tidak dihapus sembarangan agar saldo & laporan tetap bisa ditelusuri
            $table->foreign('id_user')
                  ->references('id_user')->on('pengguna')
                  ->onUpdate('cascade')->onDelete('restrict');
        });

        // Nominal pengeluaran harus lebih dari nol
        DB::statement('ALTER TABLE pengeluaran ADD CONSTRAINT chk_pengeluaran_nominal CHECK (nominal > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
