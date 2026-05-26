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
        if (!Schema::hasTable('penilaian')) {
            return;
        }

        Schema::table('penilaian', function (Blueprint $table) {
            // noop: handled below with defensive operations
        });

        try {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->dropForeign(['mahasiswa_nim']);
            });
        } catch (\Throwable $e) {
            // Constraint mungkin memang tidak ada pada beberapa environment.
        }

        try {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->dropForeign(['dosen_id']);
            });
        } catch (\Throwable $e) {
            // Constraint mungkin memang tidak ada pada beberapa environment.
        }

        if (Schema::getColumnType('penilaian', 'mahasiswa_nim') !== 'string') {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->string('mahasiswa_nim')->change();
            });
        }

        try {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->foreign('dosen_id')->references('id')->on('users');
            });
        } catch (\Throwable $e) {
            // Constraint sudah ada, lewati.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('penilaian')) {
            return;
        }

        try {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->dropForeign(['dosen_id']);
            });
        } catch (\Throwable $e) {
            // Constraint mungkin tidak ada.
        }

        if (Schema::getColumnType('penilaian', 'mahasiswa_nim') !== 'bigint') {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->bigInteger('mahasiswa_nim')->change();
            });
        }

        try {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->foreign('mahasiswa_nim')->references('id')->on('users');
            });
        } catch (\Throwable $e) {
            // Constraint sudah ada.
        }

        try {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->foreign('dosen_id')->references('id')->on('users');
            });
        } catch (\Throwable $e) {
            // Constraint sudah ada.
        }
    }
};

