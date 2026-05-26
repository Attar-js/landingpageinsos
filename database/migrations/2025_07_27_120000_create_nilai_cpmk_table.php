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
        if (Schema::hasTable('nilai_cpmk')) {
            return;
        }

        Schema::create('nilai_cpmk', function (Blueprint $table) {
            $table->id();
            $table->string('nim_mahasiswa', 20)->comment('NIM mahasiswa ketua kelompok');
            $table->string('nama_mahasiswa')->comment('Nama mahasiswa ketua kelompok');
            $table->string('judul_kegiatan')->comment('Judul kegiatan KKN');
            $table->string('file_name')->comment('Nama file PDF');
            $table->longText('file_content')->nullable()->comment('Konten file PDF');
            $table->string('file_mime_type')->nullable()->comment('MIME type file');
            $table->integer('file_size')->nullable()->comment('Ukuran file dalam bytes');
            $table->string('uploaded_by')->comment('NIM/username tim penciri yang upload');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan')->nullable()->comment('Catatan dari tim penciri');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['nim_mahasiswa']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_cpmk');
    }
}; 
