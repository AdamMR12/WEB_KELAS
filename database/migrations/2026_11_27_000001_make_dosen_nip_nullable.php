<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->string('nip', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('dosen')->whereNull('nip')->update(['nip' => '']);

        Schema::table('dosen', function (Blueprint $table) {
            $table->string('nip', 30)->nullable(false)->change();
        });
    }
};
