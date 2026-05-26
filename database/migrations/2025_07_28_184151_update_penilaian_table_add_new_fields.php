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
        Schema::table('penilaian', function (Blueprint $table) {
            // Ubah mahasiswa_id menjadi mahasiswa_nim
            $table->renameColumn('mahasiswa_id', 'mahasiswa_nim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            // Kembalikan nama kolom
            $table->renameColumn('mahasiswa_nim', 'mahasiswa_id');
        });
    }
};

