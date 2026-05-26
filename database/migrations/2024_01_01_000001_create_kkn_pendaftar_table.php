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
        Schema::create('kkn_pendaftar', function (Blueprint $table) {
            $table->id();
            $table->string('judul_kegiatan');
            $table->string('mitra');
            $table->string('lokasi_mitra');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamps();
        });

        Schema::create('kkn_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kkn_pendaftar_id')->constrained('kkn_pendaftar')->onDelete('cascade');
            $table->string('nama');
            $table->string('nim');
            $table->string('program_studi');
            $table->enum('peran', ['Ketua', 'Anggota']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kkn_anggota');
        Schema::dropIfExists('kkn_pendaftar');
    }
}; 
