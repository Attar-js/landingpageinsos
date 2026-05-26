<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_cpmk_rubrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->unique()->constrained('groups')->cascadeOnDelete();
            $table->text('deskripsi_p5')->nullable();
            $table->text('deskripsi_c3')->nullable();
            $table->text('deskripsi_a2')->nullable();
            $table->decimal('skor_p5', 5, 2)->nullable();
            $table->decimal('skor_c3', 5, 2)->nullable();
            $table->decimal('skor_a2', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('filled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_cpmk_rubrics');
    }
};
