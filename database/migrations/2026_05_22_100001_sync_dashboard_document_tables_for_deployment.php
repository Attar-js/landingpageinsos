<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Skema dokumen di database dashboard (connection: dashboard).
 * Dipakai untuk proposal, laporan_akhir, luaran (upload via API / query dashboard).
 */
return new class extends Migration
{
    protected string $connection = 'dashboard';

    public function up(): void
    {
        $schema = Schema::connection($this->connection);

        if (!$schema->hasTable('proposal')) {
            $schema->create('proposal', function (Blueprint $table) {
                $this->defineProposalColumns($table);
            });
        } else {
            $schema->table('proposal', function (Blueprint $table) {
                $this->addProposalColumnsIfMissing($table);
            });
        }

        if (!$schema->hasTable('laporan_akhir')) {
            $schema->create('laporan_akhir', function (Blueprint $table) {
                $this->defineLaporanAkhirColumns($table);
            });
        } else {
            $schema->table('laporan_akhir', function (Blueprint $table) {
                $this->addLaporanAkhirColumnsIfMissing($table);
            });
        }

        if (!$schema->hasTable('luaran')) {
            $schema->create('luaran', function (Blueprint $table) {
                $this->defineLuaranColumns($table);
            });
        } else {
            $schema->table('luaran', function (Blueprint $table) {
                $this->addLuaranColumnsIfMissing($table);
            });
        }
    }

    public function down(): void
    {
        // Deployment sync — tidak di-rollback otomatis.
    }

    private function defineProposalColumns(Blueprint $table): void
    {
        $table->id();
        $table->string('judul_kegiatan');
        $table->string('user_nim', 30);
        $table->string('file_path')->nullable();
        $table->string('file_name')->nullable();
        $table->unsignedBigInteger('file_size')->nullable();
        $table->longText('file_content')->nullable();
        $table->string('file_mime_type', 100)->nullable();
        $table->string('status', 30)->default('pending');
        $table->text('catatan')->nullable();
        $table->timestamps();
        $table->index(['user_nim', 'judul_kegiatan']);
    }

    private function addProposalColumnsIfMissing(Blueprint $table): void
    {
        $columns = [
            'judul_kegiatan' => fn () => $table->string('judul_kegiatan')->nullable(),
            'user_nim' => fn () => $table->string('user_nim', 30)->nullable(),
            'file_path' => fn () => $table->string('file_path')->nullable(),
            'file_name' => fn () => $table->string('file_name')->nullable(),
            'file_size' => fn () => $table->unsignedBigInteger('file_size')->nullable(),
            'file_content' => fn () => $table->longText('file_content')->nullable(),
            'file_mime_type' => fn () => $table->string('file_mime_type', 100)->nullable(),
            'status' => fn () => $table->string('status', 30)->default('pending'),
            'catatan' => fn () => $table->text('catatan')->nullable(),
        ];

        foreach ($columns as $name => $callback) {
            if (!Schema::connection($this->connection)->hasColumn('proposal', $name)) {
                $callback();
            }
        }
    }

    private function defineLaporanAkhirColumns(Blueprint $table): void
    {
        $table->id();
        $table->string('judul_kegiatan');
        $table->string('user_nim', 30);
        $table->string('file_path')->nullable();
        $table->string('file_name')->nullable();
        $table->unsignedBigInteger('file_size')->nullable();
        $table->longText('file_content')->nullable();
        $table->string('file_mime_type', 100)->nullable();
        $table->string('status', 30)->default('pending');
        $table->text('catatan')->nullable();
        $table->timestamps();
        $table->index(['user_nim', 'judul_kegiatan']);
    }

    private function addLaporanAkhirColumnsIfMissing(Blueprint $table): void
    {
        $columns = [
            'judul_kegiatan' => fn () => $table->string('judul_kegiatan')->nullable(),
            'user_nim' => fn () => $table->string('user_nim', 30)->nullable(),
            'file_path' => fn () => $table->string('file_path')->nullable(),
            'file_name' => fn () => $table->string('file_name')->nullable(),
            'file_size' => fn () => $table->unsignedBigInteger('file_size')->nullable(),
            'file_content' => fn () => $table->longText('file_content')->nullable(),
            'file_mime_type' => fn () => $table->string('file_mime_type', 100)->nullable(),
            'status' => fn () => $table->string('status', 30)->default('pending'),
            'catatan' => fn () => $table->text('catatan')->nullable(),
        ];

        foreach ($columns as $name => $callback) {
            if (!Schema::connection($this->connection)->hasColumn('laporan_akhir', $name)) {
                $callback();
            }
        }
    }

    private function defineLuaranColumns(Blueprint $table): void
    {
        $table->id();
        $table->string('judul_kegiatan');
        $table->string('user_nim', 30)->nullable();
        $table->string('video_aftermovie')->default('');
        $table->string('artikel_link')->default('');
        $table->string('artikel_file_path')->nullable();
        $table->string('artikel_file_name')->nullable();
        $table->string('file_path')->nullable();
        $table->string('file_name')->nullable();
        $table->unsignedBigInteger('file_size')->nullable();
        $table->string('status', 30)->default('pending');
        $table->text('catatan')->nullable();
        $table->timestamps();
        $table->index(['user_nim', 'judul_kegiatan']);
    }

    private function addLuaranColumnsIfMissing(Blueprint $table): void
    {
        $columns = [
            'judul_kegiatan' => fn () => $table->string('judul_kegiatan')->nullable(),
            'user_nim' => fn () => $table->string('user_nim', 30)->nullable(),
            'video_aftermovie' => fn () => $table->string('video_aftermovie')->default(''),
            'artikel_link' => fn () => $table->string('artikel_link')->default(''),
            'artikel_file_path' => fn () => $table->string('artikel_file_path')->nullable(),
            'artikel_file_name' => fn () => $table->string('artikel_file_name')->nullable(),
            'file_path' => fn () => $table->string('file_path')->nullable(),
            'file_name' => fn () => $table->string('file_name')->nullable(),
            'file_size' => fn () => $table->unsignedBigInteger('file_size')->nullable(),
            'status' => fn () => $table->string('status', 30)->default('pending'),
            'catatan' => fn () => $table->text('catatan')->nullable(),
        ];

        foreach ($columns as $name => $callback) {
            if (!Schema::connection($this->connection)->hasColumn('luaran', $name)) {
                $callback();
            }
        }
    }
};
