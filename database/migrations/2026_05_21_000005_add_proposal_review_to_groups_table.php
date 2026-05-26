<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('proposal_review_status', 20)->default('pending')->after('catatan');
            $table->text('proposal_review_note')->nullable()->after('proposal_review_status');
            $table->timestamp('proposal_reviewed_at')->nullable()->after('proposal_review_note');
            $table->unsignedBigInteger('proposal_reviewed_by')->nullable()->after('proposal_reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn([
                'proposal_review_status',
                'proposal_review_note',
                'proposal_reviewed_at',
                'proposal_reviewed_by',
            ]);
        });
    }
};
