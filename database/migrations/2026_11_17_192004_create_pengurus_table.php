<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengurus', function (Blueprint $table) {
            $table->string('NIM', 15)->primary();
            $table->unsignedInteger('ID_Jabatan');
            $table->string('No_WhatsApp', 15)->nullable();
            $table->string('Email', 100)->nullable();
            $table->timestamps();

            $table->foreign('NIM')
                  ->references('NIM')
                  ->on('mahasiswa')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreign('ID_Jabatan')
                  ->references('ID_Jabatan')
                  ->on('jabatan')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengurus');
    }
};
