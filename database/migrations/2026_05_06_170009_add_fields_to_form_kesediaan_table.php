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
        Schema::table('form_kesediaan', function (Blueprint $table) {
            if (!Schema::hasColumn('form_kesediaan', 'judul_kegiatan')) {
                $table->string('judul_kegiatan')->nullable()->after('id');
            }
            if (!Schema::hasColumn('form_kesediaan', 'file_name')) {
                $table->string('file_name')->nullable()->after('judul_kegiatan');
            }
            if (!Schema::hasColumn('form_kesediaan', 'file_path')) {
                $table->string('file_path')->nullable()->after('file_name');
            }
            if (!Schema::hasColumn('form_kesediaan', 'user_nim')) {
                $table->string('user_nim', 30)->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('form_kesediaan', 'status')) {
                $table->string('status', 30)->default('pending')->after('user_nim');
            }
            if (!Schema::hasColumn('form_kesediaan', 'catatan')) {
                $table->text('catatan')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_kesediaan', function (Blueprint $table) {
            $columns = ['judul_kegiatan', 'file_name', 'file_path', 'user_nim', 'status', 'catatan'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('form_kesediaan', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
