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
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->comment('ID kelompok');
            $table->unsignedBigInteger('mahasiswa_id')->comment('ID mahasiswa');
            $table->string('role')->default('member')->comment('Role: leader, member');
            $table->string('status')->default('active')->comment('Status: active, inactive');
            $table->timestamps();
            
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('mahasiswa_id')->references('id')->on('users')->onDelete('cascade');
            
            // Unique constraint untuk mencegah mahasiswa masuk ke lebih dari satu kelompok
            $table->unique(['mahasiswa_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};

