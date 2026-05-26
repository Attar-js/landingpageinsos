<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('peer_review')) {
            return;
        }

        Schema::table('peer_review', function (Blueprint $table) {
            if (!Schema::hasColumn('peer_review', 'group_id')) {
                $table->unsignedBigInteger('group_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('peer_review', 'reviewer_id')) {
                $table->unsignedBigInteger('reviewer_id')->nullable()->after('group_id');
            }
            if (!Schema::hasColumn('peer_review', 'reviewee_id')) {
                $table->unsignedBigInteger('reviewee_id')->nullable()->after('reviewer_id');
            }
            if (!Schema::hasColumn('peer_review', 'kontribusi_kegiatan')) {
                $table->decimal('kontribusi_kegiatan', 5, 2)->nullable()->after('reviewee_id');
            }
            if (!Schema::hasColumn('peer_review', 'tanggung_jawab')) {
                $table->decimal('tanggung_jawab', 5, 2)->nullable()->after('kontribusi_kegiatan');
            }
            if (!Schema::hasColumn('peer_review', 'kerjasama_tim')) {
                $table->decimal('kerjasama_tim', 5, 2)->nullable()->after('tanggung_jawab');
            }
            if (!Schema::hasColumn('peer_review', 'inisiatif_motivasi')) {
                $table->decimal('inisiatif_motivasi', 5, 2)->nullable()->after('kerjasama_tim');
            }
            if (!Schema::hasColumn('peer_review', 'final_score')) {
                $table->decimal('final_score', 5, 2)->nullable()->after('inisiatif_motivasi');
            }
            if (!Schema::hasColumn('peer_review', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('final_score');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('peer_review')) {
            return;
        }

        Schema::table('peer_review', function (Blueprint $table) {
            $columns = [
                'group_id',
                'reviewer_id',
                'reviewee_id',
                'kontribusi_kegiatan',
                'tanggung_jawab',
                'kerjasama_tim',
                'inisiatif_motivasi',
                'final_score',
                'submitted_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('peer_review', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
