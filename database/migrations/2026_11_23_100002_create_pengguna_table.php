<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Akun untuk masuk ke sistem kas beserta perannya.
        // Dinamai 'pengguna' agar tidak bentrok dengan tabel bawaan Laravel 'users'.
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('username', 50)->unique();
            $table->string('password'); // disimpan sebagai hash
            $table->enum('role', ['admin', 'bendahara', 'anggota'])->index();
            // Opsional: hanya terisi untuk akun yang berstatus anggota (relasi 1 : 0..1)
            $table->unsignedBigInteger('id_anggota')->nullable()->unique();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_anggota')
                  ->references('id_anggota')
                  ->on('anggota')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
