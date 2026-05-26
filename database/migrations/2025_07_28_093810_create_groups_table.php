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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelompok')->comment('Nama kelompok KKN');
            $table->text('judul_kegiatan')->comment('Judul kegiatan KKN');
            $table->string('lokasi_kkn')->comment('Lokasi KKN');
            $table->text('deskripsi_kegiatan')->nullable()->comment('Deskripsi kegiatan');
            $table->string('nama_mitra')->nullable()->comment('Nama mitra/partner');
            $table->string('lokasi_mitra')->nullable()->comment('Lokasi mitra');
            $table->unsignedBigInteger('dosen_id')->nullable()->comment('ID dosen pembimbing (assigned)');
            $table->unsignedBigInteger('assigned_by')->nullable()->comment('ID admin yang assign');
            $table->timestamp('assigned_at')->nullable()->comment('Waktu assignment');
            $table->text('assignment_note')->nullable()->comment('Catatan assignment');
            $table->string('status')->default('pending')->comment('Status: pending, assigned, approved, rejected');
            $table->text('catatan')->nullable()->comment('Catatan dari dosen');
            $table->integer('progress_verifikasi')->default(0)->comment('Progress verifikasi (0-100%)');
            $table->timestamps();
            
            $table->foreign('dosen_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};

