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
        Schema::create('luaran', function (Blueprint $table) {
            $table->id();
            $table->string('judul_kegiatan'); // Tambahan field judul kegiatan
            $table->string('video_aftermovie');
            $table->string('artikel_link');
            $table->string('artikel_file_path')->nullable();
            $table->string('artikel_file_name')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luaran');
    }
};

