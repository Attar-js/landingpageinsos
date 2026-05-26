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
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->decimal('nilai_akhir', 5, 2)->nullable(); // Nilai 0-100 dengan 2 desimal
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_penilaian')->nullable();
            $table->timestamps();
            
            // Pastikan satu dosen hanya bisa memberi nilai satu kali per mahasiswa
            $table->unique(['mahasiswa_id', 'dosen_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};

