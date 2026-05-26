<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menyelaraskan skema database aplikasi (default) dengan fitur yang dipakai saat ini.
 * Aman dijalankan berulang: memakai Schema::hasColumn / hasTable.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->syncUsersTable();
        $this->syncKknPendaftarTable();
        $this->syncPenilaianTable();
        $this->syncLuaranTableOnAppDb();
    }

    public function down(): void
    {
        // Deployment sync — tidak di-rollback otomatis.
    }

    private function syncUsersTable(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'nim')) {
                $table->string('nim', 30)->nullable()->unique()->after('username');
            }
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip', 30)->nullable()->after('nim');
            }
            if (!Schema::hasColumn('users', 'program_studi')) {
                $table->string('program_studi', 100)->nullable()->after('nip');
            }
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('program_studi');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 30)->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->string('user_type', 30)->default('user')->after('phone_number');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 30)->default('mahasiswa')->after('user_type');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status', 30)->default('active')->after('role');
            }
        });
    }

    private function syncKknPendaftarTable(): void
    {
        if (!Schema::hasTable('kkn_pendaftar')) {
            return;
        }

        Schema::table('kkn_pendaftar', function (Blueprint $table) {
            if (!Schema::hasColumn('kkn_pendaftar', 'user_nim')) {
                $table->string('user_nim', 30)->nullable()->after('status');
            }
        });
    }

    private function syncPenilaianTable(): void
    {
        if (!Schema::hasTable('penilaian')) {
            return;
        }

        if (Schema::hasColumn('penilaian', 'mahasiswa_id') && !Schema::hasColumn('penilaian', 'mahasiswa_nim')) {
            try {
                Schema::table('penilaian', function (Blueprint $table) {
                    $table->dropForeign(['mahasiswa_id']);
                });
            } catch (\Throwable $e) {
                // Foreign key mungkin sudah dihapus pada environment tertentu.
            }

            Schema::table('penilaian', function (Blueprint $table) {
                $table->string('mahasiswa_nim', 30)->nullable()->after('id');
            });

            foreach (DB::table('penilaian')->whereNotNull('mahasiswa_id')->orderBy('id')->get() as $row) {
                $nim = DB::table('users')->where('id', $row->mahasiswa_id)->value('nim');
                if ($nim) {
                    DB::table('penilaian')->where('id', $row->id)->update(['mahasiswa_nim' => $nim]);
                }
            }

            Schema::table('penilaian', function (Blueprint $table) {
                $table->dropColumn('mahasiswa_id');
            });
        }

        Schema::table('penilaian', function (Blueprint $table) {
            if (!Schema::hasColumn('penilaian', 'mahasiswa_nim')) {
                $table->string('mahasiswa_nim', 30)->after('id');
            }
            foreach ([
                'proposal_kegiatan',
                'asistensi',
                'peer_review',
                'laporan_akhir',
                'presentasi_akhir',
                'pembimbing_lapangan',
            ] as $column) {
                if (!Schema::hasColumn('penilaian', $column)) {
                    $table->decimal($column, 5, 2)->nullable();
                }
            }
        });

        if (Schema::hasColumn('penilaian', 'mahasiswa_nim') && Schema::hasColumn('penilaian', 'dosen_id')) {
            try {
                Schema::table('penilaian', function (Blueprint $table) {
                    $table->unique(['mahasiswa_nim', 'dosen_id'], 'penilaian_mahasiswa_nim_dosen_id_unique');
                });
            } catch (\Throwable $e) {
                // Index mungkin sudah ada dengan nama lain.
            }
        }
    }

    private function syncLuaranTableOnAppDb(): void
    {
        if (!Schema::hasTable('luaran')) {
            return;
        }

        Schema::table('luaran', function (Blueprint $table) {
            if (!Schema::hasColumn('luaran', 'user_nim')) {
                $table->string('user_nim', 30)->nullable()->after('judul_kegiatan');
            }
        });
    }
};
