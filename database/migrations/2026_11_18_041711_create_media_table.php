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
        Schema::create('media', function (Blueprint $table) {
            $table->id('id_media');
            $table->unsignedBigInteger('id_album');
            $table->string('file_path', 255);
            $table->string('caption', 255)->nullable();
            $table->timestamp('tgl_upload')->useCurrent();
            $table->timestamps();

            $table->foreign('id_album')
                  ->references('id_album')
                  ->on('album')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
