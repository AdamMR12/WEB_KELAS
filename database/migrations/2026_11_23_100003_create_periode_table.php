<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Periode iuran kas beserta nominal dan jatuh temponya
        Schema::create('periode', function (Blueprint $table) {
            $table->id('id_periode');
            $table->string('nama_periode', 100);
            $table->unsignedInteger('nominal_iuran');
            $table->date('jatuh_tempo')->index();
            $table->timestamps();
        });

        // Nominal iuran harus lebih dari nol
        DB::statement('ALTER TABLE periode ADD CONSTRAINT chk_periode_nominal CHECK (nominal_iuran > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('periode');
    }
};
