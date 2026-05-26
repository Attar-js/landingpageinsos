<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_cpmk_rubrics', function (Blueprint $table) {
            $table->foreignId('skor_filled_by')->nullable()->after('filled_by')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('group_cpmk_rubrics', function (Blueprint $table) {
            $table->dropForeign(['skor_filled_by']);
            $table->dropColumn('skor_filled_by');
        });
    }
};
